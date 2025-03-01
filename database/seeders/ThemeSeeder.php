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
            ['name' => 'Romantico', 'description' => 'Un tema elegante e delicato', 'color_scheme' => 'pink'],
            ['name' => 'Fire', 'description' => 'Un tema acceso e vibrante', 'color_scheme' => 'red'],
            ['name' => 'Nature', 'description' => 'Un tema fresco e rilassante', 'color_scheme' => 'green'],
            ['name' => 'Sea', 'description' => 'Un tema calmo e profondo', 'color_scheme' => 'blue'],
        ]);
    }
}
