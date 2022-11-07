<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // TODO: Remove this after deployment
        try {
            DB::beginTransaction();
            $this->call([
                UserSeeder::class
            ]);
            DB::commit();
        } catch (Exception $exc) {
            DB::rollBack();
            echo (json_encode($exc));
        }
    }
}
