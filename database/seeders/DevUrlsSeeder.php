<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Website;
use App\Models\Url;

class DevUrlsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $urls = [
            '/',
            '/our-work/our-role-and-our-people',
            '/our-work/contact-us',
            '/early-childhood/running-a-service/starting-a-service',
        ];

        $website = Website::where('name', 'Ministry of Education')->firstOrFail();

        foreach ($urls as $u) {
            $url = new Url;
            $url->url = $u;
            $url->website_id = $website->id;
            $url->save();
        }
    }
}
