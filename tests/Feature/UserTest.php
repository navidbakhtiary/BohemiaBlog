<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private $api_register = '/api/user/register';

    public function testRegisterNewUser()
    {
        $user = User::factory()->make();
        $attributes = $user->toArray();
        $attributes['email'] = 'navidbakhtiary@gmail.com'; //Validation dns checker does not allow fake emails.
        $attributes['password'] = 'Ab123456';//The password mutator hashes the password and make it contains invalid characters.
        $response = $this->postJson($this->api_register, $attributes);
        $response->
            assertCreated()->
            assertJsonStructure(['message' => [], 'data' => ['user' => []]])->
            assertJsonFragment(
                [
                    'message' => ['code' => 'S211', 'text' => 'The user was registered successfully.'],
                ]
            );
        $this->assertDatabaseHas('users', 
            [
                'name' => $user->name, 
                'surname' => $user->surname, 
                'email' => $attributes['email'], 
                'phone' => $user->phone
            ]
        );
    }
}
