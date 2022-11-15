<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', '<>', 'navidbakhtiary@gmail.com')->inRandomOrder()->first();
        $user->password = 'Admin2022';
        $user->save();
        $user->admin()->create();
    }
}
