<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            DevAccountsSeeder::class,
            DevWebsitesSeeder::class,
            DevUrlsSeeder::class,
        ]);
    }
}
