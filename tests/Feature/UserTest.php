<?php

namespace Tests\Feature;

use App\Classes\Creator;
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
                    'message' => Creator::createSuccessMessage('user_registered')
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
            assertJsonFragment(
                [
                    'message' => Creator::createFailureMessage('invalid_inputs')
                ]
            )->
            assertJsonFragment([
                'name' => [
                    Creator::createValidationError('name', 'regex', ['name_regex'], true, ['name' => 'name']),
                    Creator::createValidationError('name', 'required', null, true)
                ],
                'surname' => [
                    Creator::createValidationError('surname', 'regex', ['surname_regex'], true)
                ],
                'email' => [
                    Creator::createValidationError('email', 'email', null, true)
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
            assertJsonFragment(
                [
                    'message' => Creator::createFailureMessage('invalid_inputs')
                ]
            )->
            assertJsonFragment([
                'email' => [
                    Creator::createValidationError('email', 'unique', null, true)
                ],
                'phone' => [
                    Creator::createValidationError('phone', 'unique', null, true)
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
                    'message' => Creator::createSuccessMessage('user_logged_in')
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

    public function testGetBadRequestResponseForUserLoginWithInvalidInputData()
    {
        $user = User::factory()->make();
        $user->password = 'Ab123456';
        $user->save();
        $response = $this->postJson($this->api_login, ['login' => $user->username]);
        $response->assertStatus(HttpStatus::BadRequest)->
            assertExactJson(
                [
                    'message' => Creator::createFailureMessage('invalid_inputs'),
                    'errors' => [
                        'device_name' => [
                            Creator::createValidationError('device_name', 'required', null, true, ['attribute' => 'device name'])
                        ],
                        'password' => [
                            Creator::createValidationError('password', 'required', null, true)
                        ],
                    ]
                ]
            );
        $this->assertDatabaseHas(
            'users',
            [
                'name' => $user->name,
                'surname' => $user->surname,
                'email' => $user->email,
                'phone' => $user->phone
            ]
        );
    }

    public function testGetUnauthorizedResponseForUserLoginWithNonExistenceAccount()
    {
        $user = User::factory()->make();
        $user->password = 'Ab123456';
        $response = $this->postJson($this->api_login, 
            ['login' => $user->email, 'password' => 'Ab123456', 'device_name' => 'laptop']);
        $response->assertUnauthorized()->
            assertExactJson(
                [
                    'message' => Creator::createFailureMessage('non_existent_user'),
                    'errors' => []
                ]
            );
    }


    public function testUserCanLoginWithUsernameOrPhoneNumber()
    {
        $user = User::factory()->make();
        $user->password = 'Ab123456';
        $user->save();
        $response = $this->postJson(
            $this->api_login,
            ['login' => $user->username, 'password' => 'Ab123456', 'device_name' => 'laptop']
        );
        $response->assertOk()->
            assertJsonStructure(
                [
                    'message' => [],
                    'data' => ['user' => [], 'access token' => ['token', 'device name']]
                ]
            )->assertJsonFragment(
                [
                    'message' => Creator::createSuccessMessage('user_logged_in')
                ]
            );
        $response = $this->postJson(
            $this->api_login,
            ['login' => $user->phone, 'password' => 'Ab123456', 'device_name' => 'macbook']
        );
        $response->assertOk()->
            assertJsonStructure(
                [
                    'message' => [],
                    'data' => ['user' => [], 'access token' => ['token', 'device name']]
                ]
            )->assertJsonFragment(
                [
                    'message' => Creator::createSuccessMessage('user_logged_in')
                ]
            );
        $this->assertDatabaseHas(
            'personal_access_tokens',
            [
                'name' => 'laptop',
                'tokenable_type' => User::class,
                'tokenable_id' => $user->id
            ]
        )->assertDatabaseHas(
            'personal_access_tokens',
            [
                'name' => 'macbook',
                'tokenable_type' => User::class,
                'tokenable_id' => $user->id
            ]
        );
    }
}
