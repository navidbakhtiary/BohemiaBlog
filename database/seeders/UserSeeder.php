<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'navid',
            'surname' => 'bakhtiary',
            'email' => 'navidbakhtiary@gmail.com',
            'phone' => '+909371659085',
            'password' => 'Nb123456'
        ]);
        $user->admin()->create();
        User::factory()->count(50)->create();
    }
}
