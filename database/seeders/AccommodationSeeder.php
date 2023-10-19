<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccommodationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('accommodations')->insert([
            'name' => 'Sencilla'
        ]);

        DB::table('accommodations')->insert([
            'name' => 'Doble'
        ]);

        DB::table('accommodations')->insert([
            'name' => 'Triple'
        ]);

        DB::table('accommodations')->insert([
            'name' => 'Cuádruple'
        ]);
    }
}
