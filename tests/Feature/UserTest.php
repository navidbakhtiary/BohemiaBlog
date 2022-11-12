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
    private $api_login = '/api/user/login';

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
        $this->assertTrue(
            User::where([
                ['name', $user->name],
                ['surname', $user->surname],
                ['email', $attributes['email']],
                ['username', 'like', strtolower($user->surname . substr($user->name, 0, 3)) . '%']
            ])->count() == 1
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

    public function testFailureInNewUserRegistrationWithSavedEmailAndPhone()
    {
        $user1 = User::factory()->create();
        $user1->email = 'navidbakhtiary@gmail.com';
        $user1->save();
        $user2 = User::factory()->make();
        $user2->phone = $user1->phone;
        $user2->email = $user1->email;
        $attributes = $user2->toArray();
        $attributes['password'] = 'Ab123456';
        $response = $this->postJson($this->api_register, $attributes);
        $response->
            assertStatus(HttpStatus::BadRequest)->
            assertJsonStructure(['message' => [], 'errors' => []])->
            assertJsonFragment(['message' => ['code' => 'E091', 'text' => 'Inputs are invalid.']])->
            assertJsonFragment([
                'email' => [
                    [
                        'code' => '0501-90',
                        'message' => 'The email has already been taken.'
                    ]
                ],
                'phone' => [
                    [
                        'code' => '1603-90',
                        'message' => 'The phone has already been taken.'
                    ]
                ]
            ]
        );
        $this->assertDatabaseHas(
            'users',
            [
                'name' => $user1->name,
                'surname' => $user1->surname,
                'email' => $user1->email,
                'phone' => $user1->phone,
            ]
        );
        $this->assertDatabaseMissing(
            'users',
            [
                'name' => $user2->name,
                'surname' => $user2->surname,
                'email' => $user2->email,
                'phone' => $user2->phone,
            ]
        );
    }

    public function testUserLogin()
    {
        $user = User::factory()->make();
        $user->password = 'Ab123456';
        $user->save();
        $response = $this->postJson($this->api_login, 
            ['login' => $user->email, 'password' => 'Ab123456', 'device_name' => 'laptop']);
        $response->
            assertOk()->
            assertJsonStructure(
                [
                    'message' => [],
                    'data' => ['user' => [], 'access token' => ['token', 'device name']]
                ]
            )->
            assertJsonFragment(
                [
                    'message' => ['code' => 'S212', 'text' => 'Logging was successful. This device was saved to the user account.'] 
                ]
            );
        $this->assertDatabaseHas(
            'personal_access_tokens',
            [
                'name' => 'laptop',
                'tokenable_type' => User::class,
                'tokenable_id' => $user->id
            ]
        );
    }
}
