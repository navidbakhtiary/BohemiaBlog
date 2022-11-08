<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ModeratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::inRandomOrder()->limit(5)->get();
        foreach($users as $user)
        {
            $user->password = 'Mod2022';
            $user->save();
            $user->moderators()->create();
        }
    }
}
