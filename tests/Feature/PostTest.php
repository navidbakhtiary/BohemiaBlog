<?php

namespace Tests\Feature;

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
                    'message' => [
                        'code' => 'E091',
                        'text' => 'Inputs are invalid.'
                    ],
                    'errors' =>
                    [
                        'subject' => [
                            [
                                'code' => '1902-57',
                                'message' => 'The subject must not have more than 64 characters.'
                            ]
                        ],
                        'content' => [
                            [
                                'code' => '0302-72',
                                'message' => 'The content field is required.'
                            ]
                        ],
                    ]
                ]
            );
        $this->assertDatabaseMissing('posts', ['admin_id' => $admin->id, 'subject' => $subject]);
    }
}