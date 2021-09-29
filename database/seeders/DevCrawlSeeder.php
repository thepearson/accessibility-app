<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Website;
use App\Models\Crawl;

class DevCrawlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $crawls = [
            [
                "token" => "dhNduqxrBYLLQ9evXRaqPYHavy4D4Y405pxpDjNKemqOm0nTFzh0YWFL6yfEjQ0u",
                "data" => "{}",
                "messages" => "{}",
                "total" => null,
                "complete" => null,
                "status" => "queued",           
            ],
            [
                "token" => Crawl::createToken(),
                "data" => "{}",
                "messages" => "{}",
                "total" => 456,
                "complete" => 123,
                "status" => "processing", 
            ],
            [
                "token" => Crawl::createToken(),
                "data" => "{}",
                "messages" => "{}",
                "total" => 456,
                "complete" => 456,
                "status" => "success", 
            ],
            [
                "token" => Crawl::createToken(),
                "data" => "{}",
                "messages" => "{}",
                "total" => 456,
                "complete" => 452,
                "status" => "failed", 
            ],
        ];

        $website = Website::where('name', 'Ministry of Education')->firstOrFail();

        foreach ($crawls as $c) {
            $crawl = new Crawl;
            $crawl->website_id = $website->id;

            $crawl->token = $c['token'];
            $crawl->status = $c['status'];
            $crawl->data = $c['data'];

            $crawl->save();
        }
    }
}
