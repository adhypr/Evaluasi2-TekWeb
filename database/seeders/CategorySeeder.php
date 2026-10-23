<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Kategori sesuai data awal frontend (category: 1..4) — tidak diubah.
     * Label per-produk (mis. "Hot Wheels Neon Speeders") tetap tersimpan di products.category_name.
     */
    public function run(): void
    {
        $categories = [
            1 => 'Hot Wheels',
            2 => 'Mini GT',
            3 => 'Tomica',
            4 => 'Loose',
        ];

        foreach ($categories as $id => $name) {
            $category = Category::firstOrNew(['category_id' => $id]);
            $category->category = $name;
            $category->save();
        }
    }
}
