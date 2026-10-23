<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductWebTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
    }

    public function test_can_view_product_list_blade(): void
    {
        Product::create([
            'category_id' => 1,
            'name' => 'Nissan Skyline R34 Test',
            'brand' => 'Hot Wheels',
            'scale' => '1:64',
            'price' => 150000,
            'stock' => 5,
            'item_condition' => 'New',
            'rating' => 4.9,
            'img' => 'https://example.com/r34.jpg',
            'slug' => 'nissan-skyline-r34-test',
        ]);

        $response = $this->get(route('admin.products.index'));

        $response->assertStatus(200);
        $response->assertSee('Product Management');
        $response->assertSee('Nissan Skyline R34 Test');
    }

    public function test_can_filter_and_search_products_blade(): void
    {
        Product::create([
            'category_id' => 1,
            'name' => 'Hot Wheels Batmobile',
            'brand' => 'Hot Wheels',
            'scale' => '1:64',
            'price' => 75000,
            'stock' => 10,
            'item_condition' => 'New',
            'rating' => 4.8,
            'img' => 'https://example.com/batmobile.jpg',
            'slug' => 'hw-batmobile',
        ]);

        Product::create([
            'category_id' => 2,
            'name' => 'Mini GT Porsche 911 GT3',
            'brand' => 'Mini GT',
            'scale' => '1:64',
            'price' => 210000,
            'stock' => 3,
            'item_condition' => 'New',
            'rating' => 5.0,
            'img' => 'https://example.com/porsche.jpg',
            'slug' => 'minigt-porsche',
        ]);

        $searchResponse = $this->get(route('admin.products.index', ['s' => 'Batmobile']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Hot Wheels Batmobile');
        $searchResponse->assertDontSee('Mini GT Porsche 911 GT3');

        $filterResponse = $this->get(route('admin.products.index', ['category_id' => 2]));
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('Mini GT Porsche 911 GT3');
        $filterResponse->assertDontSee('Hot Wheels Batmobile');
    }

    public function test_can_view_create_product_form(): void
    {
        $response = $this->get(route('admin.products.create'));
        $response->assertStatus(200);
        $response->assertSee('Add New Product');
    }

    public function test_can_store_product_via_blade_form(): void
    {
        $payload = [
            'name' => 'Toyota Supra MK4 1994',
            'category_id' => 1,
            'brand' => 'Hot Wheels',
            'scale' => '1:64',
            'price' => 125000,
            'stock' => 8,
            'item_condition' => 'New',
            'rating' => 4.9,
            'description' => 'Fast and Furious edition',
        ];

        $response = $this->post(route('admin.products.store'), $payload);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'name' => 'Toyota Supra MK4 1994',
            'price' => 125000,
        ]);
    }

    public function test_can_edit_and_update_product_via_blade_form(): void
    {
        $product = Product::create([
            'category_id' => 1,
            'name' => 'Old Product Name',
            'brand' => 'Hot Wheels',
            'scale' => '1:64',
            'price' => 50000,
            'stock' => 5,
            'item_condition' => 'New',
            'rating' => 4.0,
            'img' => 'https://example.com/old.jpg',
            'slug' => 'old-product-name',
        ]);

        $editResponse = $this->get(route('admin.products.edit', $product->product_id));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Old Product Name');

        $updateResponse = $this->put(route('admin.products.update', $product->product_id), [
            'name' => 'Updated Product Name',
            'category_id' => 1,
            'brand' => 'Hot Wheels Premium',
            'scale' => '1:64',
            'price' => 85000,
            'stock' => 12,
            'item_condition' => 'New',
            'rating' => 4.5,
            'description' => 'Updated description',
        ]);

        $updateResponse->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'product_id' => $product->product_id,
            'name' => 'Updated Product Name',
            'price' => 85000,
        ]);
    }

    public function test_can_delete_product_via_blade(): void
    {
        $product = Product::create([
            'category_id' => 1,
            'name' => 'Product To Delete',
            'brand' => 'Hot Wheels',
            'scale' => '1:64',
            'price' => 60000,
            'stock' => 2,
            'item_condition' => 'Used',
            'rating' => 3.5,
            'img' => 'https://example.com/del.jpg',
            'slug' => 'product-to-delete',
        ]);

        $response = $this->delete(route('admin.products.destroy', $product->product_id));

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseMissing('products', [
            'product_id' => $product->product_id,
        ]);
    }
}
