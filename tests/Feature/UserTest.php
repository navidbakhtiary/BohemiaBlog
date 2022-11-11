<?php

namespace Tests\Feature;

use App\Classes\HttpStatus;
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

    public function testFailureInNewUserRegistrationWithInvalidInputData()
    {
        $attributes = [
            'name' => '',
            'surname' => 'bakhtiary9',
            'email' => 'navidbakhtiary',
            'phone' => '123456789',
            'address' => '',
            'city' => 'prague-',
            'state' => '',
            'zipcode' => '123',
            'password' => 'abc12345-'
        ];
        $response = $this->postJson($this->api_register, $attributes);
        $response->
            assertStatus(HttpStatus::BadRequest)->
            assertJsonStructure(['message' => [], 'errors' => []])->
            assertJsonFragment(['message' => ['code' => 'E091', 'text' => 'Inputs are invalid.']])->
            assertJsonFragment([
                'name' => [
                    [
                        'code' => '1401-71',
                        'message' => 'The name format is invalid.The name can contain only letters and space.'
                    ],
                    [
                        'code' => '1401-72',
                        'message' => 'The name field is required.'
                    ]
                ],
                'surname' => [
                    [
                        'code' => '1901-71',
                        'message' => 'The surname format is invalid.The surname can contain only letters and space.'
                    ]
                ],
                'email' => [
                    [
                        'code' => '0501-26',
                        'message' => 'The email must be a valid email address.'
                    ]
                ]
            ]);
            $this->assertDatabaseMissing(
                'users',
                [
                    'surname' => 'bakhtiary9',
                    'email' => 'navidbakhtiary',
                    'phone' => '123456789',
                    'city' => 'prague-',
                ]
            );
    }
}
