<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class organization_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          DB::table('positions')->insert([
            [
                'title' => 'CEO',
                'name' => 'Andi',
                'department' => 'Manajemen',
                'parent_id' => null,
                'filled_at' => Carbon::now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'HR Manager',
                'name' => 'Siti',
                'department' => 'HR',
                'parent_id' => 1,
                'filled_at' => Carbon::now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Recruitment Officer',
                'name' => null,
                'department' => 'HR',
                'parent_id' => 2,
                'filled_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
