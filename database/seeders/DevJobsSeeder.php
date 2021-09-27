<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Website;
use App\Models\Job;

class DevJobsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $jobs = [
            [
                "token" => "dhNduqxrBYLLQ9evXRaqPYHavy4D4Y405pxpDjNKemqOm0nTFzh0YWFL6yfEjQ0u",
                "type" => "crawl",
                "data" => "{}",
                "status" => "queued",           
            ],
            [
                "token" => Job::createToken(),
                "type" => "crawl",
                "data" => "{}",
                "status" => "processing", 
            ],
            [
                "token" => Job::createToken(),
                "type" => "crawl",
                "data" => "{}",
                "status" => "success", 
            ],
            [
                "token" => Job::createToken(),
                "type" => "crawl",
                "data" => "{}",
                "status" => "failed", 
            ],
        ];

        $website = Website::where('name', 'Ministry of Education')->firstOrFail();

        foreach ($jobs as $j) {
            $job = new Job;
            $job->website_id = $website->id;

            $job->token = $j['token'];
            $job->type = $j['type'];
            $job->status = $j['status'];
            $job->data = $j['data'];

            $job->save();
        }
    }
}
