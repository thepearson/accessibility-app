<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Website;
use App\Models\User;

class DevWebsitesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $user = User::where('email', 'thepearson@gmail.com')->firstOrFail();

        $website = new Website;
        $website->name = "Ministry of Education";
        $website->base_url = "https://education.govt.nz/";
        $user->personalTeam()->websites()->save($website);        
    }
}
