<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private $api_register = '/user/register';

    public function testRegisterNewUser()
    {
        $user = User::factory()->make();
        $response = $this->postJson($this->api_register, $user->toArray());
        $response->
            assertCreated()->
            assertJsonFragment(
                [
                    'message' => ['text' => 'The user was registered successfully.'],
                    'data' => 
                    [
                        'user' => 
                        [
                            'name' => $user->name,
                            'surname' => $user->surname,
                            'email' => $user->email,
                            'phone' => $user->phone
                        ]
                    ]
                ]
            );
        $this->assertDatabaseHas('users', 
            [
                'name' => $user->name, 
                'surname' => $user->surname, 
                'email' => $user->email, 
                'phone' => $user->phone
            ]
        );
    }
}
