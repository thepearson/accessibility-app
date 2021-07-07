<?php

namespace Database\Seeders;

use App\Models\Rule;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\File;

class RulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Rule::query()->delete();
        $json = File::get('database/data/rules.json');
        $data = json_decode($json, true);
        for ($i = 0; $i < count($data); $i++) {
            $r = $data[$i];
            $rule = new Rule;
            $rule->axe_id = $r['id'];
            $rule->description = $r['metadata']['description'];
            $rule->help = $r['metadata']['help'];
            $rule->save();

            $tags = [];
            for ($t = 0; $t < count($r['tags']); $t++) {
                $tag = Tag::where('name', $r['tags'][$t])->firstOrFail();
                if ($tag) {
                    $tags[] = $tag->id;
                }
            }
            if (!empty($tags)) {
                $rule->tags()->attach($tags);
            }
        }
    }
}
