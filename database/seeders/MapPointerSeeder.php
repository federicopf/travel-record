<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MapPointerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('map_pointers')->insert([
            [
                'id' => 0,
                'name' => 'Segnaposto Azzurro',
                'url' => 'https://icons.iconarchive.com/icons/icons-land/vista-map-markers/256/Map-Marker-Ball-Azure-icon.png',
                'created_at' => '2025-03-02 21:31:44',
                'updated_at' => '2025-03-02 21:31:44',
            ],
            [
                'id' => 1,
                'name' => 'Segnaposto Classico',
                'url' => 'https://icons.iconarchive.com/icons/icons8/windows-8/256/Maps-Marker-icon.png',
                'created_at' => '2025-03-02 21:31:44',
                'updated_at' => '2025-03-02 21:31:44',
            ],
            [
                'id' => 2,
                'name' => 'Segnaposto Verde',
                'url' => 'https://icons.iconarchive.com/icons/icons-land/vista-map-markers/256/Map-Marker-Ball-Chartreuse-icon.png',
                'created_at' => '2025-03-02 21:31:44',
                'updated_at' => '2025-03-02 21:31:44',
            ],
            [
                'id' => 3,
                'name' => 'Segnaposto Verificato',
                'url' => 'https://icons.iconarchive.com/icons/pictogrammers/material-map/256/map-marker-check-icon.png',
                'created_at' => '2025-03-02 21:31:44',
                'updated_at' => '2025-03-02 21:31:44',
            ],
            [
                'id' => 4,
                'name' => 'Segnaposto Rosa',
                'url' => 'https://icons.iconarchive.com/icons/icons-land/vista-map-markers/256/Map-Marker-Ball-Pink-icon.png',
                'created_at' => '2025-03-02 21:31:44',
                'updated_at' => '2025-03-02 21:31:44',
            ],
            [
                'id' => 5,
                'name' => 'Segnaposto Rosso',
                'url' => 'https://icons.iconarchive.com/icons/paomedia/small-n-flat/256/map-marker-icon.png',
                'created_at' => '2025-03-02 21:31:44',
                'updated_at' => '2025-03-02 21:31:44',
            ],
        ]);
    }
}
