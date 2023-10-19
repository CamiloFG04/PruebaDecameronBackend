<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('types')->insert([
            'name' => 'Estándar'
        ]);

        DB::table('types')->insert([
            'name' => 'Junior'
        ]);

        DB::table('types')->insert([
            'name' => 'Suite'
        ]);
    }
}
