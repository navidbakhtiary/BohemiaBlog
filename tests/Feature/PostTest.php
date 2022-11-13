<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    private $bearer_prefix = 'Bearer ';
    
    private $api_save = '/api/post/save';

    public function testCreatePostByAdmin()
    {
        $user = User::factory()->create();
        $admin = $user->admins()->create();
        $token = $user->createToken('test-token');
        $post = Post::factory()->make();
        $attributes = $post->toArray();
        unset($attributes['admin_id']);
        $response = $this->withHeaders(['Authorization' => $this->bearer_prefix . $token->plainTextToken])->
            postJson($this->api_save, $attributes);
        $response->
            assertCreated()->
            assertExactJson(
                [
                    'message' => [
                        'code' => 'S161',
                        'text' => 'Post was saved successfully.'
                    ],
                    'data' => 
                    [
                        'post' => 
                        [
                            'subject' => $post->subject,
                            'content' => $post->content,
                            'created_at' => $post->created_at,
                            'updated_at' => $post->updated_at,
                            'user' => 
                            [
                                'id' => $user->id,
                                'name' => $user->name,
                                'surname' => $user->surname,
                                'nickname' => $user->nickname,
                                'email' => $user->email
                            ]
                        ]
                    ]
                ]
            );
        $this->assertDatabaseHas('posts', array_merge(['admin_id' => $admin->id], $attributes));
    }
}