<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'code' => 'ART' . strtoupper(Str::random(6)),
                'name' => 'Cat Merah',
                'type' => 'Paint',
                'unit' => 'Liter',
                'customer' => 'PT ABC',
                'safety_stock' => 50,
                'min_package' => 20,
                'qr_code_path' => null,
                'status' => 'active',
                'note' => 'Untuk body kendaraan',
                'created_at' => now(),
                'created_by' => 1,
            ],
            [
                'code' => 'ART' . strtoupper(Str::random(6)),
                'name' => 'Cat Biru',
                'type' => 'Paint',
                'unit' => 'Liter',
                'customer' => 'PT DEF',
                'safety_stock' => 40,
                'min_package' => 10,
                'qr_code_path' => null,
                'status' => 'active',
                'note' => 'Digunakan untuk pengecatan umum',
                'created_at' => now(),
                'created_by' => 1,
            ],
        ];

        Article::insert($articles);
    }
}
