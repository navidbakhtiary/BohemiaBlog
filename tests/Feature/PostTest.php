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
}