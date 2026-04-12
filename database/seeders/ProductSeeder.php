<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name'        => 'Fathul Qarib',
                'description' => 'Kitab fiqih dasar yang membahas hukum-hukum ibadah dan muamalah dalam Islam.',
                'price'       => 25000,
                'stock'       => 500,
                'is_active'   => true,
            ],
            [
                'name'        => 'Ta\'lim Muta\'allim',
                'description' => 'Kitab tentang adab dan etika belajar bagi para penuntut ilmu.',
                'price'       => 18000,
                'stock'       => 300,
                'is_active'   => true,
            ],
            [
                'name'        => 'Safinah an-Najah',
                'description' => 'Kitab ringkasan fiqih madzhab syafi\'i yang populer di pesantren.',
                'price'       => 15000,
                'stock'       => 200,
                'is_active'   => true,
            ],
            [
                'name'        => 'Bulughul Maram',
                'description' => 'Kumpulan hadis-hadis hukum yang menjadi rujukan dalam fiqih islami.',
                'price'       => 45000,
                'stock'       => 150,
                'is_active'   => true,
            ],
            [
                'name'        => 'Riyadhus Shalihin',
                'description' => 'Kumpulan hadis-hadis tentang akhlak dan perilaku seorang Muslim.',
                'price'       => 50000,
                'stock'       => 100,
                'is_active'   => true,
            ],
            [
                'name'        => 'Jurumiyah',
                'description' => 'Kitab nahwu dasar untuk mempelajari tata bahasa Arab.',
                'price'       => 12000,
                'stock'       => 400,
                'is_active'   => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
