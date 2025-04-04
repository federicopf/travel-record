<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hashtag;

class HashtagSeeder extends Seeder
{
    public function run(): void
    {
        $hashtags = [
            ['name' => 'natura',    'color' => '#4CAF50'],
            ['name' => 'cultura',   'color' => '#3F51B5'],
            ['name' => 'relax',     'color' => '#FF9800'],
            ['name' => 'avventura', 'color' => '#F44336'],
        ];

        foreach ($hashtags as $hashtag) {
            Hashtag::firstOrCreate(['name' => $hashtag['name']], [
                'color' => $hashtag['color'],
            ]);
        }
    }
}
