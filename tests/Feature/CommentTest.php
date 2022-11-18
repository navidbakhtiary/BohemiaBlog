<?php

namespace Tests\Feature;

use App\Classes\Creator;
use App\Classes\HttpStatus;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    private $bearer_prefix = 'Bearer ';
    
    private $api_save = '/api/post/{id}/comment/save';
    private $api_delete = '/api/post/{post_id}/comment/{comment_id}/delete';

    public function testSaveCommentOnPostByAuthenticatedUser()
    {
        $user = User::factory()->create();
        $admin = $user->admin()->create();
        $post = Post::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('test-token');
        $comment = Comment::factory()->make();
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            postJson(str_replace('{id}', $post->id, $this->api_save), $comment->toArray());
        $response->
            assertCreated()->
            assertJsonStructure(
                [
                    'message' => ['code', 'text'],
                    'data' => ['comment' => ['id', 'content', 'post' => ['id', 'subject']]]
                ]
            )->assertJsonFragment(['message' => Creator::createSuccessMessage('comment_saved')]);
        $this->assertDatabaseHas('comments', 
            ['user_id' => $user->id, 'post_id' => $post->id, 'content' => $comment->content]);
    }

    public function testNewCommentSavingWithInvalidInputDataIsUnsuccessful()
    {
        $user = User::factory()->create();
        $admin = $user->admin()->create();
        $post = Post::factory()->create();
        $token = $user->createToken('test-token');
        $content = Factory::create()->paragraph(40);
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            postJson(str_replace('{id}', $post->id, $this->api_save), ['content' => Factory::create()->sentences()]);
        $response->assertStatus(HttpStatus::BadRequest)->assertJson(
            [
                'message' => Creator::createFailureMessage('invalid_inputs'),
                'errors' =>
                [
                    'content' => [
                        Creator::createValidationError('content', 'string', null, true)
                    ],
                ]
            ]
        );
        $this->assertDatabaseMissing('comments', ['user_id' => $user->id, 'post_id' => $post->id]);

        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            postJson(str_replace('{id}', $post->id, $this->api_save), ['content' => $content]);
        $response->assertStatus(HttpStatus::BadRequest)->assertJson(
            [
                'message' => Creator::createFailureMessage('invalid_inputs'),
                'errors' =>
                [
                    'content' => [
                        Creator::createValidationError('content', 'max.string', null, true, ['max' => '500'])
                    ],
                ]
            ]
        );
        $this->assertDatabaseMissing('comments', ['user_id' => $user->id, 'post_id' => $post->id, 'content' => $content]);
    }

    public function testUnauthenticatedUserCanNotSaveComment()
    {
        $admin_user = User::factory()->create();
        $admin_user->admin()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->make();
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . hash('sha256', 'fake token')])->
            postJson(str_replace('{id}', $post->id, $this->api_save), $comment->toArray());
        $response->assertUnauthorized()->assertJson(
            [
                'message' => Creator::createFailureMessage('unauthenticated'),
                'errors' => []
            ]
        );
    }

    public function testUserCanNotSaveCommentOnUnexistedPost()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token');
        $comment = Comment::factory()->make();
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->postJson(str_replace('{id}', 456, $this->api_save), $comment->toArray());
        $response->assertNotFound()->assertJson([
                'message' => Creator::createFailureMessage('post_not_found'),
                'errors' => []
            ]);
    }

    public function testDeleteCommentOfPostByAuthenticatedAdmin()
    {
        $user = User::factory()->create();
        $admin = $user->admin()->create();
        $post = Post::factory()->create();
        $token = $user->createToken('test-token');
        $user = User::factory()->create();
        $comment = Comment::factory()->make();
        $comment = $post->comments()->create(['user_id' => $user->id, 'content' => $comment->content]);
        $this->assertDatabaseHas(
            'comments',
            ['user_id' => $user->id, 'post_id' => $post->id, 'content' => $comment->content]
        );
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            postJson(str_replace(['{post_id}', '{comment_id}'], [$post->id, $comment->id], $this->api_delete));
        $response->assertOk()->assertJson(['message' => Creator::createSuccessMessage('comment_deleted'), 'data' => []]);
        $this->assertSoftDeleted(
            'comments',
            ['user_id' => $user->id, 'post_id' => $post->id, 'content' => $comment->content]
        );
        $this->assertSoftDeleted($comment);
    }

    public function testAuthenticatedNonAdminUserCanNotDeleteComment()
    {
        $user = User::factory()->create();
        $admin = $user->admin()->create();
        $post = Post::factory()->create();
        $user = User::factory()->create();
        $comment = Comment::factory()->make();
        $comment = $post->comments()->create(['user_id' => $user->id, 'content' => $comment->content]);
        $token = $user->createToken('test-token');
        $this->assertDatabaseHas(
            'comments',
            ['user_id' => $user->id, 'post_id' => $post->id, 'content' => $comment->content]
        );
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            postJson(str_replace(['{post_id}', '{comment_id}'], [$post->id, $comment->id], $this->api_delete));
        $response->assertForbidden()->assertJson(['message' => Creator::createFailureMessage('unauthorized'), 'errors' => []]);
        $this->assertDatabaseHas(
            'comments',
            ['user_id' => $user->id, 'post_id' => $post->id, 'content' => $comment->content]
        );
    }

    public function testDeleteNonExistentCommentGetFail()
    {
        $user = User::factory()->create();
        $admin = $user->admin()->create();
        $post = Post::factory()->create();
        $token = $user->createToken('test-token');
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            postJson(str_replace(['{post_id}', '{comment_id}'], [$post->id, 1001], $this->api_delete));
        $response->assertNotFound()->assertJson(['message' => Creator::createFailureMessage('comment_not_found'), 'errors' => []]);
    }
}