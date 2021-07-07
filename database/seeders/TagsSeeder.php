<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\File;

class TagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get('database/data/rules.json');
        $data = json_decode($json, true);
        for ($i = 0; $i < count($data); $i++) {
            for ($t = 0; $t < count($data[$i]['tags']); $t++) {
                $tag = $data[$i]['tags'][$t];
                Tag::firstOrCreate(['name' => $tag]);
            }
        }
    }
}
