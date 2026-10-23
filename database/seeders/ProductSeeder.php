<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    /**
     * Data berasal dari database/seeders/data/products.json (hasil ekspor initialProducts.js).
     * Gambar disalin dari database/seeders/images ke storage/app/public/products.
     */
    public function run(): void
    {
        $rows = json_decode(File::get(database_path('seeders/data/products.json')), true);

        foreach ($rows as $row) {
            $image = $row['img'];
            $source = database_path('seeders/images/'.$image);
            if (File::exists($source) && ! Storage::disk('public')->exists('products/'.$image)) {
                Storage::disk('public')->put('products/'.$image, File::get($source));
            }

            Product::updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'category_id' => $row['category_id'],
                    'category_name' => $row['category_name'],
                    'brand' => $row['brand'],
                    'scale' => $row['scale'],
                    'rating' => $row['rating'],
                    'price' => $row['price'],
                    'stock' => $row['stock'],
                    'img' => 'products/'.$image,
                ]
            );
        }
    }
}
