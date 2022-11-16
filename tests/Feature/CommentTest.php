<?php

namespace Tests\Feature;

use App\Classes\Creator;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    private $bearer_prefix = 'Bearer ';
    
    private $api_save = '/api/post/{id}/comment/save';

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
}