<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $catId = DB::table('categories')->insertGetId([
            'name' => 'Skincare',
            'slug' => 'skincare',
            'description' => 'Glow-up essentials',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        foreach ([
            ['Rose Glow Serum', 799.00],
            ['Lavender Night Cream', 899.00],
            ['Blossom Lip Balm', 199.00],
        ] as [$name, $price]) {
            DB::table('products')->insert([
                'category_id' => $catId,
                'name' => $name,
                'slug' => Str::slug($name) . '-' . Str::random(5),
                'short_description' => 'A must-have for your self-care ritual.',
                'description' => 'Beautiful, gentle formula for daily use.',
                'price' => $price,
                'stock' => 50,
                'image_path' => null,
                'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }
}
