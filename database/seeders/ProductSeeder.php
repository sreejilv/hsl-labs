<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'code' => 'MED001',
                'name' => 'Digital Stethoscope',
                'description' => 'High-quality digital stethoscope with noise cancellation and recording capabilities. Perfect for accurate diagnosis and patient care.',
                'price' => 299.99,
                'stock' => 15,
                'is_active' => true,
                'images' => ['products/stethoscope1.jpg', 'products/stethoscope2.jpg'],
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'code' => 'MED002',
                'name' => 'Blood Pressure Monitor',
                'description' => 'Automatic digital blood pressure monitor with large LCD display and memory storage for multiple readings.',
                'price' => 89.99,
                'stock' => 25,
                'is_active' => true,
                'images' => ['products/bp_monitor1.jpg'],
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'code' => 'SUR001',
                'name' => 'Surgical Scissors Set',
                'description' => 'Professional grade stainless steel surgical scissors set. Includes various sizes for different surgical procedures.',
                'price' => 159.99,
                'stock' => 8,
                'is_active' => true,
                'images' => ['products/scissors1.jpg', 'products/scissors2.jpg', 'products/scissors3.jpg'],
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'code' => 'LAB001',
                'name' => 'Digital Thermometer',
                'description' => 'Fast and accurate digital thermometer with fever alert and memory recall function.',
                'price' => 24.99,
                'stock' => 50,
                'is_active' => true,
                'images' => ['products/thermometer1.jpg'],
                'created_at' => Carbon::now()->subDays(18),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'code' => 'MED003',
                'name' => 'Pulse Oximeter',
                'description' => 'Fingertip pulse oximeter for measuring blood oxygen saturation and pulse rate. Compact and portable design.',
                'price' => 49.99,
                'stock' => 30,
                'is_active' => true,
                'images' => ['products/oximeter1.jpg', 'products/oximeter2.jpg'],
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'SUR002',
                'name' => 'Surgical Gloves (Box)',
                'description' => 'Latex-free, powder-free surgical gloves. Box of 100 pieces. Excellent tactile sensitivity and barrier protection.',
                'price' => 19.99,
                'stock' => 120,
                'is_active' => true,
                'images' => ['products/gloves1.jpg'],
                'created_at' => Carbon::now()->subDays(12),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'LAB002',
                'name' => 'Microscope Slides (Pack)',
                'description' => 'High-quality glass microscope slides for laboratory use. Pack of 50 slides with ground edges.',
                'price' => 12.99,
                'stock' => 75,
                'is_active' => true,
                'images' => ['products/slides1.jpg'],
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'MED004',
                'name' => 'ECG Machine (Portable)',
                'description' => 'Portable 12-lead ECG machine with built-in printer and interpretation software. Perfect for bedside examinations.',
                'price' => 2499.99,
                'stock' => 3,
                'is_active' => true,
                'images' => ['products/ecg1.jpg', 'products/ecg2.jpg'],
                'created_at' => Carbon::now()->subDays(8),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'DIS001',
                'name' => 'Hand Sanitizer (500ml)',
                'description' => 'Medical grade hand sanitizer with 70% alcohol content. Kills 99.9% of germs and bacteria.',
                'price' => 8.99,
                'stock' => 0,
                'is_active' => true,
                'images' => ['products/sanitizer1.jpg'],
                'created_at' => Carbon::now()->subDays(6),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'OLD001',
                'name' => 'Vintage Stethoscope',
                'description' => 'Classic vintage stethoscope - discontinued model. Collector item only, not for medical use.',
                'price' => 199.99,
                'stock' => 2,
                'is_active' => false,
                'images' => ['products/vintage_steth1.jpg'],
                'created_at' => Carbon::now()->subDays(60),
                'updated_at' => Carbon::now()->subDays(30),
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}
