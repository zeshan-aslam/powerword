<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        // \App\Models\PartnersAffiliate::factory(200)->create();
        \App\Models\PartnersJoinPgm::factory(2000)->create();
    }
}
