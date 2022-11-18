<?php

namespace Tests\Feature;

use App\Classes\Creator;
use App\Classes\HttpStatus;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Faker\Factory;

class PostTest extends TestCase
{
    use RefreshDatabase;

    private $bearer_prefix = 'Bearer ';
    
    private $api_save = '/api/post/save';
    private $api_delete = '/api/post/{post_id}/delete';

    public function testCreatePostByAdmin()
    {
        $user = User::factory()->create();
        $admin = $user->admin()->create();
        $token = $user->createToken('test-token');
        $post = Post::factory()->make();
        $attributes = $post->toArray();
        unset($attributes['admin_id']);
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            postJson($this->api_save, $attributes);
        $response->
            assertCreated()->
            assertJson(
                [
                    'message' => Creator::createSuccessMessage('post_saved'),
                    'data' => 
                    [
                        'post' => 
                        [
                            'subject' => $post->subject,
                            'content' => $post->content,
                            'author' => 
                            [
                                'id' => $user->id,
                                'name' => $user->name,
                                'surname' => $user->surname,
                                'nickname' => $user->nickname,
                                'username' => $user->username
                            ]
                        ]
                    ]
                ]
            );
        $this->assertDatabaseHas('posts', array_merge(['admin_id' => $admin->id], $attributes));
    }

    public function testNewPostSavingWithInvalidInputDataIsUnsuccessful()
    {
        $user = User::factory()->create();
        $admin = $user->admin()->create();
        $token = $user->createToken('test-token');
        $subject = Factory::create()->paragraph();
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            postJson($this->api_save, ['subject' => $subject]);
        $response->assertStatus(HttpStatus::BadRequest)->assertJson(
                [
                    'message' => Creator::createFailureMessage('invalid_inputs'),
                    'errors' =>
                    [
                        'subject' => [
                            Creator::createValidationError('subject', 'max.string', null, true, ['max' => '64'])
                        ],
                        'content' => [
                            Creator::createValidationError('content', 'required', null, true)
                        ],
                    ]
                ]
            );
        $this->assertDatabaseMissing('posts', ['admin_id' => $admin->id, 'subject' => $subject]);
    }

    public function testNonAdminUserCanNotSavePost()
    {
        $user = User::factory()->create();
        $admin_user = User::factory()->create();
        $admin_user->admin()->create();
        $token = $user->createToken('test-token');
        $post = Post::factory()->make();
        $attributes = $post->toArray();
        unset($attributes['admin_id']);
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            postJson($this->api_save, $attributes);
        $response->assertForbidden()->assertJson(
            [
                'message' => Creator::createFailureMessage('unauthorized'),
                'errors' => []
            ]
        );
    }

    public function testSavingNewPostWithAnExistingSubjectIsUnsuccessful()
    {
        $user = User::factory()->create();
        $admin_1 = $user->admin()->create();
        $post_1 = Post::factory()->create();
        $user = User::factory()->create();
        $admin_2 = $user->admin()->create();
        $token = $user->createToken('test-token');
        $post_2 = Post::factory()->make();
        $post_2->subject = $post_1->subject;
        
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            postJson($this->api_save, $post_2->toArray());
        $response->assertStatus(HttpStatus::BadRequest)->assertJson(
            [
                'message' => Creator::createFailureMessage('invalid_inputs'),
                'errors' =>
                [
                    'subject' => [
                        Creator::createValidationError('subject', 'unique', null, true)
                    ]
                ]
            ]
        );
        $this->assertTrue(Post::where('subject', $post_1->subject)->count() == 1);
    }

    public function testDeletePostAndItsCommentsByAuthenticatedAdmin()
    {
        $user = User::factory()->create();
        $admin = $user->admin()->create();
        $post = Post::factory()->create();
        $token = $user->createToken('test-token');
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $factory = Factory::create();
        $comment1 = $post->comments()->create(['user_id' => $user1->id, 'content' => $factory->paragraph()]);
        $comment2 = $post->comments()->create(['user_id' => $user2->id, 'content' => $factory->paragraph()]);
        $this->assertDatabaseHas(
            'posts',
            ['admin_id' => $admin->id, 'subject' => $post->subject, 'content' => $post->content]
        );
        $this->assertDatabaseHas(
            'comments',
            ['user_id' => $user1->id, 'post_id' => $post->id, 'content' => $comment1->content]
        );
        $this->assertDatabaseHas(
            'comments',
            ['user_id' => $user2->id, 'post_id' => $post->id, 'content' => $comment2->content]
        );
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            postJson(str_replace('{post_id}', $post->id, $this->api_delete));
        $response->assertOk()->assertJson(['message' => Creator::createSuccessMessage('post_deleted'), 'data' => []]);
        $this->assertSoftDeleted($post)->
            assertSoftDeleted(
                'posts',
                ['admin_id' => $admin->id, 'subject' => $post->subject, 'content' => $post->content]
            );
        $this->assertSoftDeleted(
            'comments',
            ['user_id' => $user1->id, 'post_id' => $post->id, 'content' => $comment1->content]
        );
        $this->assertSoftDeleted(
            'comments',
            ['user_id' => $user2->id, 'post_id' => $post->id, 'content' => $comment2->content]
        );
    }

    public function testAuthenticatedNonAdminUserCanNotDeletePost()
    {
        $user = User::factory()->create();
        $admin = $user->admin()->create();
        $post = Post::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $factory = Factory::create();
        $comment = $post->comments()->create(['user_id' => $user1->id, 'content' => $factory->paragraph()]);
        $token = $user2->createToken('test-token');
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            postJson(str_replace('{post_id}', $post->id, $this->api_delete));
        $response->assertForbidden()->assertJson(['message' => Creator::createFailureMessage('unauthorized'), 'errors' => []]);
        $this->assertDatabaseHas(
            'posts',
            ['admin_id' => $admin->id, 'subject' => $post->subject, 'content' => $post->content]
        );
        $this->assertDatabaseHas(
            'comments',
            ['user_id' => $user1->id, 'post_id' => $post->id, 'content' => $comment->content]
        );
    }
}