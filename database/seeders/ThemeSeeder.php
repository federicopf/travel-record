<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('themes')->insert([
            ['id' => 0, 'name' => 'Rosso', 'description' => 'Esplora il mondo con energia e passione', 'color_scheme' => 'red'],
            ['id' => 1, 'name' => 'Blu', 'description' => 'Spiagge dorate e mari cristallini', 'color_scheme' => 'blue'],
            ['id' => 2, 'name' => 'Verde', 'description' => 'Immergiti nella natura più selvaggia', 'color_scheme' => 'green'],
            ['id' => 3, 'name' => 'Rosa', 'description' => 'Atmosfere rilassanti e romantiche', 'color_scheme' => 'pink'],
        ]);
    }
}
