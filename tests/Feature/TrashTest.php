<?php

namespace Tests\Feature;

use Faker\Factory;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Classes\Creator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrashTest extends TestCase
{
    use RefreshDatabase;

    private $bearer_prefix = 'Bearer ';
    
    private $api_post_list = '/api/trash/post/list';
    private $api_show = '/api/trash/post/{post_id}';

    public function testAdminCanGetListOfDeletedPosts()
    {
        $factory = Factory::create();
        $user = User::factory()->create();
        $admin = $user->admin()->create();
        $token = $user->createToken('test-token');
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $post1 = Post::factory()->create();
        $post2 = Post::factory()->create();
        $comment1 = $post1->comments()->create(['user_id' => $user1->id, 'content' => $factory->paragraph()]);
        $comment2 = $post2->comments()->create(['user_id' => $user1->id, 'content' => $factory->paragraph()]);
        $comment3 = $post1->comments()->create(['user_id' => $user2->id, 'content' => $factory->paragraph()]);
        $comment4 = $post2->comments()->create(['user_id' => $user2->id, 'content' => $factory->paragraph()]);
        $comment5 = $post1->comments()->create(['user_id' => $user2->id, 'content' => $factory->paragraph()]);
        $post1->delete();
        $post2->delete();
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            getJson($this->api_post_list);
        $response->assertOk()->
            assertJsonFragment(['message' => Creator::createSuccessMessage('deleted_posts_list')])->
            assertJsonStructure(['message', 'data' => ['deleted posts' => []], 'pagination' => []])->
            assertJsonFragment([
                'deleted posts' =>
                [
                    [
                        'id' => $post1->id,
                        'subject' => $post1->subject,
                        'created at' => $post1->created_at,
                        'deleted at' => $post1->deleted_at,
                        'summary' => substr($post1->content, 0, 250),
                        'comments count' => 3,
                        'author' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'surname' => $user->surname,
                            'nickname' => $user->nickname,
                            'username' => $user->username,
                        ]
                    ],
                    [
                        'id' => $post2->id,
                        'subject' => $post2->subject,
                        'created at' => $post1->created_at,
                        'deleted at' => $post1->deleted_at,
                        'summary' => substr($post2->content, 0, 250),
                        'comments count' => 2,
                        'author' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'surname' => $user->surname,
                            'nickname' => $user->nickname,
                            'username' => $user->username,
                        ]
                    ]
                ]
            ]);
    }

    public function testNonAdminUserCanNotGetListOfDeletedPosts()
    {
        $factory = Factory::create();
        $user = User::factory()->create();
        $admin = $user->admin()->create();
        $post = Post::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('test-token');
        $post->delete();
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            getJson($this->api_post_list);
        $response->assertForbidden()->
            assertJson(['message' => Creator::createFailureMessage('unauthorized'), 'errors' => []]);
    }

    public function testAdminGetEmptyDeletedPostsListWhenNoPostHasBeenDeleted()
    {
        $factory = Factory::create();
        $user = User::factory()->create();
        $admin = $user->admin()->create();
        $token = $user->createToken('test-token');
        $post = Post::factory()->create();
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            getJson($this->api_post_list);
        $response->assertOk()->
            assertJson(['message' => Creator::createSuccessMessage('empty_deleted_posts_list'), 'data' => []]);
    }

    public function testAdminCanGetSpecificDeletedPostInformation()
    {
        $factory = Factory::create();
        $user = User::factory()->create();
        $admin = $user->admin()->create();
        $token = $user->createToken('test-token');
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $post = Post::factory()->create();
        $comment1 = $post->comments()->create(['user_id' => $user1->id, 'content' => $factory->paragraph()]);
        $comment2 = $post->comments()->create(['user_id' => $user2->id, 'content' => $factory->paragraph()]);
        $post->delete();
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            getJson(str_replace('{post_id}', $post->id, $this->api_show));
        $response->assertOk()->assertJsonFragment([
            'message' => Creator::createSuccessMessage('deleted_post_got'),
            'data' => [
                'deleted post' => [
                    'id' => $post->id,
                    'subject' => $post->subject,
                    'content' => $post->content,
                    'created at' => $post->created_at,
                    'updated at' => $post->updated_at,
                    'deleted at' => $post->deleted_at,
                    'author' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'surname' => $user->surname,
                        'nickname' => $user->nickname,
                        'username' => $user->username,
                    ],
                    'comments count' => 2,
                    'comments link' => Creator::createDeletedPostCommentsLink($post->id)
                ]
            ]
        ]);
    }

    public function testGetNonExistentDeletedPostWillFail()
    {
        $user = User::factory()->create();
        $admin = $user->admin()->create();
        $post = Post::factory()->create();
        $token = $user->createToken('test-token');
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            getJson(str_replace('{post_id}', $post->id, $this->api_show));
        $response->assertNotFound()->
            assertJson(['message' => Creator::createFailureMessage('deleted_post_not_found'), 'errors' => []]);
    }
}