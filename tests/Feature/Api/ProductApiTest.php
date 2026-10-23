<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    // PNG 1x1 valid (GD tidak tersedia di mesin ini, jadi tidak pakai UploadedFile::fake()->image())
    private const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->forceFill(['role' => 'admin'])->save();

        return $u;
    }

    private function product(array $overrides = []): Product
    {
        $category = Category::firstOrCreate(['category' => 'Hot Wheels']);

        return Product::create(array_merge([
            'name' => 'Hot Wheels Civic',
            'slug' => 'hot-wheels-civic',
            'category_id' => $category->category_id,
            'category_name' => 'Hot Wheels',
            'brand' => 'Hot Wheels',
            'price' => 150000,
            'stock' => 10,
        ], $overrides));
    }

    public function test_public_can_list_filter_and_show_products(): void
    {
        $this->product();
        $this->product(['name' => 'Mini GT GTR', 'slug' => 'mini-gt-gtr', 'brand' => 'Mini GT']);

        $this->getJson('/api/products')->assertOk()->assertJsonCount(2, 'data');
        $this->getJson('/api/products?brand=Mini%20GT')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/products?search=civic')->assertOk()->assertJsonCount(1, 'data');

        $id = Product::where('slug', 'hot-wheels-civic')->value('product_id');
        $this->getJson("/api/products/{$id}")
            ->assertOk()
            ->assertJsonPath('data.id', $id)
            ->assertJsonPath('data.price', 150000)
            ->assertJsonPath('data.category_name', 'Hot Wheels');
    }

    public function test_guest_and_customer_cannot_write_products(): void
    {
        $payload = ['name' => 'X', 'category_name' => 'Y', 'price' => 1, 'stock' => 1];

        $this->postJson('/api/products', $payload)->assertUnauthorized();

        $customer = User::factory()->create();
        $this->actingAs($customer, 'sanctum')->postJson('/api/products', $payload)->assertForbidden();
    }

    public function test_admin_creates_product_with_image_and_category_label_is_resolved(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin(), 'sanctum')->post('/api/products', [
            'name' => 'Tomica Premium Supra',
            'category_name' => 'Tomica Premium',
            'brand' => 'Tomica',
            'condition' => 'Loose (Bekas)',
            'price' => 185000,
            'stock' => 4,
            'img' => UploadedFile::fake()->createWithContent('supra.png', base64_decode(self::PNG)),
        ], ['Accept' => 'application/json']);

        $response->assertCreated()
            ->assertJsonPath('data.slug', 'tomica-premium-supra')
            ->assertJsonPath('data.condition', 'Loose (Bekas)')
            ->assertJsonPath('data.category.name', 'Tomica Premium');

        $product = Product::firstWhere('slug', 'tomica-premium-supra');
        Storage::disk('public')->assertExists($product->img);
        $this->assertStringContainsString('/storage/products/', $response->json('data.img_url'));
    }

    public function test_duplicate_names_get_unique_slugs(): void
    {
        $admin = $this->admin();
        $payload = ['name' => 'Same Name', 'category_name' => 'Hot Wheels', 'price' => 1, 'stock' => 1];

        $this->actingAs($admin, 'sanctum')->postJson('/api/products', $payload)->assertCreated();
        $this->actingAs($admin, 'sanctum')->postJson('/api/products', $payload)
            ->assertCreated()->assertJsonPath('data.slug', 'same-name-2');
    }

    public function test_admin_updates_with_method_override_and_deletes_product_and_image(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        Storage::disk('public')->put('products/old.png', 'x');
        $product = $this->product(['img' => 'products/old.png']);

        // Update + upload gambar baru lewat POST + _method=PUT (multipart)
        $this->actingAs($admin, 'sanctum')->post("/api/products/{$product->product_id}", [
            '_method' => 'PUT',
            'name' => 'Renamed Civic',
            'stock' => 3,
            'img' => UploadedFile::fake()->createWithContent('new.png', base64_decode(self::PNG)),
        ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Renamed Civic')
            ->assertJsonPath('data.stock', 3)
            ->assertJsonPath('data.slug', 'renamed-civic');

        Storage::disk('public')->assertMissing('products/old.png');
        $newPath = $product->refresh()->img;
        Storage::disk('public')->assertExists($newPath);

        $this->actingAs($admin, 'sanctum')->deleteJson("/api/products/{$product->product_id}")->assertOk();
        $this->assertModelMissing($product);
        Storage::disk('public')->assertMissing($newPath);
    }

    public function test_validation_errors_are_returned_as_422(): void
    {
        $this->actingAs($this->admin(), 'sanctum')
            ->postJson('/api/products', ['name' => '', 'price' => -5, 'stock' => 'abc'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'price', 'stock', 'category_name']);
    }

    public function test_unknown_product_returns_json_404(): void
    {
        $this->getJson('/api/products/999999')->assertNotFound();
    }
}
