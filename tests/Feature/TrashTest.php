<?php

namespace Tests\Feature;

use Faker\Factory;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Classes\Creator;
use App\Classes\HttpStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrashTest extends TestCase
{
    use RefreshDatabase;

    private $bearer_prefix = 'Bearer ';
    
    private $api_posts_list = '/api/trash/post/list';
    private $api_show_post = '/api/trash/post/{post_id}';
    private $api_post_comments_list = '/api/trash/post/{post_id}/comment/list';
    private $api_restore_post = '/api/trash/post/{post_id}/restore';
    private $api_comments_list = '/api/trash/comment/list';

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
            getJson($this->api_posts_list);
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
            getJson($this->api_posts_list);
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
            getJson($this->api_posts_list);
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
            getJson(str_replace('{post_id}', $post->id, $this->api_show_post));
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
            getJson(str_replace('{post_id}', $post->id, $this->api_show_post));
        $response->assertNotFound()->
            assertJson(['message' => Creator::createFailureMessage('deleted_post_not_found'), 'errors' => []]);
    }

    public function testAdminCanGetListOfCommentsOfSpecificDeletedPost()
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
            getJson(str_replace('{post_id}', $post->id, $this->api_post_comments_list));
        $response->assertOk()->
            assertJsonStructure(['message', 'data' => ['deleted post' => [], 'comments' => []], 'pagination' => []])->
            assertJsonFragment(['message' => Creator::createSuccessMessage('deleted_post_comments_list')])->
            assertJsonFragment([
                'data' =>
                [
                    'deleted post' => 
                    [
                        'id' => $post->id, 
                        'subject' => $post->subject, 
                        'created at' => $post->created_at,
                        'deleted at' => $post->deleted_at
                    ],
                    'comments' =>
                    [
                        [
                            'id' => $comment1->id,
                            'content' => $comment1->content,
                            'created at' => $comment1->created_at,
                            'user' => [
                                'id' => $user1->id,
                                'name' => $user1->name,
                                'surname' => $user1->surname
                            ]
                        ],
                        [
                            'id' => $comment2->id,
                            'content' => $comment2->content,
                            'created at' => $comment2->created_at,
                            'user' => [
                                'id' => $user2->id,
                                'name' => $user2->name,
                                'surname' => $user2->surname
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function testNonAdminUserCanNotGetListOfCommentsOfSpecificDeletedPost()
    {
        $factory = Factory::create();
        $user = User::factory()->create();
        $admin = $user->admin()->create();
        $user = User::factory()->create();
        $token = $user->createToken('test-token');
        $post = Post::factory()->create();
        $comment = $post->comments()->create(['user_id' => $user->id, 'content' => $factory->paragraph()]);
        $post->delete();
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            getJson(str_replace('{post_id}', $post->id, $this->api_post_comments_list));
        $response->assertForbidden()->
            assertJsonFragment(['message' => Creator::createFailureMessage('unauthorized'), 'errors' => []]);
    }

    public function testAdminMustGetEmptyCommentsListForDeletedPostThatHasNotComments()
    {
        $factory = Factory::create();
        $user = User::factory()->create();
        $admin = $user->admin()->create();
        $token = $user->createToken('test-token');
        $post = Post::factory()->create();
        $post->delete();
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            getJson(str_replace('{post_id}', $post->id, $this->api_post_comments_list));
        $response->assertOk()->assertJson(['message' => Creator::createSuccessMessage('empty_comments_list'), 'data' => []]);
    }

    public function testUnauthenticatedAdminCanNotGetListOfCommentsOfSpecificDeletedPost()
    {
        $factory = Factory::create();
        $user = User::factory()->create();
        $admin = $user->admin()->create();
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = $post->comments()->create(['user_id' => $user->id, 'content' => $factory->paragraph()]);
        $post->delete();
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . hash('sha256', 'fake token')])->
            getJson(str_replace('{post_id}', $post->id, $this->api_post_comments_list));
        $response->assertUnauthorized()->assertJson(['message' => Creator::createFailureMessage('unauthenticated'), 'errors' => []]);
    }

    public function testAdminCanRestoreDeletedPostWithItsComments()
    {
        $factory = Factory::create();
        $user = User::factory()->create();
        $admin = $user->admin()->create();
        $token = $user->createToken('test-token');
        $post = Post::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $comment1 = $post->comments()->create(['user_id' => $user1->id, 'content' => $factory->paragraph()]);
        $comment2 = $post->comments()->create(['user_id' => $user2->id, 'content' => $factory->paragraph()]);
        $post->delete();
        $this->assertSoftDeleted(
            'posts',
            [
                'id' => $post->id,
                'admin_id' => $admin->id,
                'subject' => $post->subject
            ]
        );
        $this->assertSoftDeleted(
            'comments',
            [
                'id' => $comment1->id,
                'user_id' => $user1->id,
                'content' => $comment1->content
            ]
        );
        $this->assertSoftDeleted(
            'comments',
            [
                'id' => $comment2->id,
                'user_id' => $user2->id,
                'content' => $comment2->content
            ]
        );
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            postJson(str_replace('{post_id}', $post->id, $this->api_restore_post), ['with_comments' => true]);
        $response->assertOk()->
            assertJsonFragment([
            'message' => Creator::createSuccessMessage('deleted_post_restored'),
            'data' => [
                'restored post' => [
                    'id' => $post->id,
                    'subject' => $post->subject,
                    'content' => $post->content,
                    'created at' => $post->created_at,
                    'updated at' => $post->updated_at,
                    'author' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'surname' => $user->surname,
                        'nickname' => $user->nickname,
                        'username' => $user->username,
                    ],
                    'comments count' => 2,
                    'comments link' => Creator::createPostCommentsLink($post->id)
                ]
            ]
        ]);
        $this->assertDatabaseHas(
            'posts', 
            [
                'id' => $post->id, 
                'admin_id' => $admin->id, 
                'subject' => $post->subject, 
                'deleted_at' => null
            ]
        );
        $this->assertDatabaseHas(
            'comments',
            [
                'id' => $comment1->id, 
                'user_id' => $user1->id, 
                'content' => $comment1->content, 
                'created_at' => $comment1->created_at, 
                'deleted_at' => null
            ]
        );
        $this->assertDatabaseHas(
            'comments',
            [
                'id' => $comment2->id,
                'user_id' => $user2->id,
                'content' => $comment2->content,
                'created_at' => $comment2->created_at,
                'deleted_at' => null
            ]
        );
        
    }

    public function testAdminCanRestoreDeletedPostWithoutItsComments()
    {
        $factory = Factory::create();
        $user = User::factory()->create();
        $admin = $user->admin()->create();
        $token = $user->createToken('test-token');
        $post = Post::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $comment1 = $post->comments()->create(['user_id' => $user1->id, 'content' => $factory->paragraph()]);
        $comment2 = $post->comments()->create(['user_id' => $user2->id, 'content' => $factory->paragraph()]);
        $post->delete();
        $this->assertSoftDeleted(
            'posts',
            [
                'id' => $post->id,
                'admin_id' => $admin->id,
                'subject' => $post->subject
            ]
        );
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            postJson(str_replace('{post_id}', $post->id, $this->api_restore_post), ['with_comments' => false]);
        $response->assertOk()->assertJsonFragment([
                'message' => Creator::createSuccessMessage('deleted_post_restored'),
                'data' => [
                    'restored post' => [
                        'id' => $post->id,
                        'subject' => $post->subject,
                        'content' => $post->content,
                        'created at' => $post->created_at,
                        'updated at' => $post->updated_at,
                        'author' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'surname' => $user->surname,
                            'nickname' => $user->nickname,
                            'username' => $user->username,
                        ],
                        'comments count' => 0,
                        'comments link' => Creator::createPostCommentsLink($post->id)
                    ]
                ]
            ]);
        $this->assertDatabaseHas(
            'posts',
            [
                'id' => $post->id,
                'admin_id' => $admin->id,
                'subject' => $post->subject,
                'deleted_at' => null
            ]
        );
        $this->assertSoftDeleted(
            'comments',
            [
                'id' => $comment1->id,
                'post_id' => $post->id,
                'user_id' => $user1->id,
                'content' => $comment1->content
            ]
        );
        $this->assertSoftDeleted(
            'comments',
            [
                'id' => $comment2->id,
                'post_id' => $post->id,
                'user_id' => $user2->id,
                'content' => $comment2->content
            ]
        );
    }

    public function testAdminCanNotRestoreDeletedPostWithInvalidInput()
    {
        $factory = Factory::create();
        $user = User::factory()->create();
        $admin = $user->admin()->create();
        $token = $user->createToken('test-token');
        $post = Post::factory()->create();
        $user1 = User::factory()->create();
        $comment1 = $post->comments()->create(['user_id' => $user1->id, 'content' => $factory->paragraph()]);
        $post->delete();
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            postJson(str_replace('{post_id}', $post->id, $this->api_restore_post), ['with_comments' => 'string']);
        $response->assertStatus(HttpStatus::BadRequest)->
            assertJson([
                'message' => Creator::createFailureMessage('invalid_inputs'), 
                'errors' => [
                    'with_comments' => [
                        Creator::createValidationError('with_comments', 'boolean', null, true, ['attribute' => trans('validation.attributes.with_comments')]),
                    ]
                ]
            ]);
        $this->assertSoftDeleted(
            'posts',
            [
                'id' => $post->id,
                'admin_id' => $admin->id,
                'subject' => $post->subject,
            ]
        );
        $this->assertSoftDeleted(
            'comments',
            [
                'id' => $comment1->id,
                'post_id' => $post->id,
                'user_id' => $user1->id,
                'content' => $comment1->content
            ]
        );
    }

    public function testAdminCanGetListOfDeletedComments()
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
        $comment2 = $post2->comments()->create(['user_id' => $user2->id, 'content' => $factory->paragraph()]);
        $comment3 = $post1->comments()->create(['user_id' => $user2->id, 'content' => $factory->paragraph()]);
        $post1->delete();
        $post2->delete();
        $comment1->refresh();
        $comment2->refresh();
        $comment3->refresh();
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            getJson($this->api_comments_list);
        $response->assertOk()->
            assertJsonFragment(['message' => Creator::createSuccessMessage('deleted_comments_list')])->
            assertJsonStructure(['message', 'data' => ['deleted comments' => []], 'pagination' => []])->
            assertJsonFragment([
                'deleted comments' =>
                [
                    [
                        'id' => $comment1->id,
                        'content' => $comment1->content,
                        'created at' => $comment1->created_at,
                        'deleted at' => $comment1->deleted_at,
                        'user' => [
                            'id' => $user1->id,
                            'name' => $user1->name,
                            'surname' => $user1->surname
                        ],
                        'post' => [
                            'id' => $post1->id,
                            'subject' => $post1->subject,
                            'is deleted' => 'Yes'
                        ],
                    ],
                    [
                        'id' => $comment2->id,
                        'content' => $comment2->content,
                        'created at' => $comment2->created_at,
                        'deleted at' => $comment2->deleted_at,
                        'user' => [
                            'id' => $user2->id,
                            'name' => $user2->name,
                            'surname' => $user2->surname
                        ],
                        'post' => [
                            'id' => $post2->id,
                            'subject' => $post2->subject,
                            'is deleted' => 'Yes'
                        ],
                    ],
                    [
                        'id' => $comment3->id,
                        'content' => $comment3->content,
                        'created at' => $comment3->created_at,
                        'deleted at' => $comment3->deleted_at,
                        'user' => [
                            'id' => $user2->id,
                            'name' => $user2->name,
                            'surname' => $user2->surname
                        ],
                        'post' => [
                            'id' => $post1->id,
                            'subject' => $post1->subject,
                            'is deleted' => 'Yes'
                        ],
                    ]
                ]
            ]);
    }
}