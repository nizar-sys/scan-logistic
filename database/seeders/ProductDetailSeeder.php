<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::all();
        $sizes = ['S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL'];
        $productDetails = [];

        foreach ($products as $product) {
            foreach ($sizes as $size) {
                $productDetails[] = [
                    'product_id' => $product->id,
                    'size' => $size,
                    'quantity' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Batch insert product details
        ProductDetail::insert($productDetails);
    }
}
