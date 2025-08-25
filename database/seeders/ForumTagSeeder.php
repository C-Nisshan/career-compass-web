<?php
namespace Database\Seeders;

use App\Models\ForumTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ForumTagSeeder extends Seeder
{
    public function run()
    {
        $tags = ['tech', 'career', 'education', 'mentorship', 'jobs'];
        foreach ($tags as $tag) {
            ForumTag::create([
                'uuid' => Str::uuid(),
                'name' => $tag,
            ]);
        }
    }
}