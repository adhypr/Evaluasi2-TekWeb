<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    private function product(string $slug, float $price, int $stock): Product
    {
        $category = Category::firstOrCreate(['category' => 'Hot Wheels']);

        return Product::create([
            'name' => "Product {$slug}",
            'slug' => $slug,
            'category_id' => $category->category_id,
            'category_name' => 'Hot Wheels',
            'brand' => 'Hot Wheels',
            'price' => $price,
            'stock' => $stock,
        ]);
    }

    private function payload(array $items, array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Adhy',
            'email' => 'adhy@example.com',
            'phone' => '081234567890',
            'address' => 'Jl. Dhyecast No. 1',
            'shipping' => 'standard',
            'payment_method' => 'bank_transfer',
            'items' => $items,
        ], $overrides);
    }

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->forceFill(['role' => 'admin'])->save();

        return $u;
    }

    public function test_guest_checkout_calculates_totals_on_server_and_reduces_stock(): void
    {
        $a = $this->product('a', 150000, 5);
        $b = $this->product('b', 175000, 3);

        // Client mencoba memalsukan harga/total — harus diabaikan
        $payload = $this->payload([
            ['product_id' => $a->product_id, 'qty' => 2, 'price' => 1],
            ['product_id' => $b->product_id, 'qty' => 1],
        ], ['total' => 1, 'subtotal' => 1]);

        $response = $this->postJson('/api/orders', $payload)->assertCreated();

        $response->assertJsonPath('data.subtotal', 475000)
            ->assertJsonPath('data.shipping', 25000)
            ->assertJsonPath('data.total', 500000)
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.customer.name', 'Adhy')
            ->assertJsonPath('data.customer.courier', 'Standard Delivery')
            ->assertJsonPath('data.customer.paymentMethod', 'Bank Transfer')
            ->assertJsonCount(2, 'data.items');

        $this->assertStringStartsWith('ORD-', $response->json('data.id'));
        $this->assertSame(3, $a->refresh()->stock);
        $this->assertSame(2, $b->refresh()->stock);
    }

    public function test_express_shipping_cost_and_duplicate_lines_are_merged(): void
    {
        $a = $this->product('a', 100000, 10);

        $this->postJson('/api/orders', $this->payload([
            ['product_id' => $a->product_id, 'qty' => 1],
            ['product_id' => $a->product_id, 'qty' => 2],
        ], ['shipping' => 'express']))
            ->assertCreated()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.qty', 3)
            ->assertJsonPath('data.total', 350000);

        $this->assertSame(7, $a->refresh()->stock);
    }

    public function test_insufficient_stock_is_rejected_and_nothing_is_saved(): void
    {
        $a = $this->product('a', 100000, 5);
        $b = $this->product('b', 100000, 1);

        $this->postJson('/api/orders', $this->payload([
            ['product_id' => $a->product_id, 'qty' => 2],
            ['product_id' => $b->product_id, 'qty' => 5],
        ]))->assertUnprocessable()->assertJsonValidationErrors('items');

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(5, $a->refresh()->stock); // rollback total
    }

    public function test_checkout_validation(): void
    {
        $this->postJson('/api/orders', [])->assertUnprocessable()
            ->assertJsonValidationErrors(['full_name', 'email', 'phone', 'address', 'shipping', 'payment_method', 'items']);

        $this->postJson('/api/orders', $this->payload([['product_id' => 9999, 'qty' => 1]]))
            ->assertUnprocessable()->assertJsonValidationErrors('items.0.product_id');
    }

    public function test_admin_endpoints_are_protected_and_listable(): void
    {
        $a = $this->product('a', 100000, 5);
        $this->postJson('/api/orders', $this->payload([['product_id' => $a->product_id, 'qty' => 1]]))->assertCreated();

        $this->getJson('/api/admin/orders')->assertUnauthorized();
        $this->actingAs(User::factory()->create(), 'sanctum')->getJson('/api/admin/orders')->assertForbidden();

        $admin = $this->admin();
        $this->actingAs($admin, 'sanctum')->getJson('/api/admin/orders')
            ->assertOk()->assertJsonCount(1, 'data');
        $this->actingAs($admin, 'sanctum')->getJson('/api/admin/stats')
            ->assertOk()
            ->assertJsonPath('total_orders', 1)
            ->assertJsonPath('total_revenue', 125000)
            ->assertJsonPath('total_products', 1)
            ->assertJsonPath('orders_by_status.pending', 1);
    }

    public function test_cancelling_order_restocks_and_cannot_be_reactivated(): void
    {
        $a = $this->product('a', 100000, 5);
        $number = $this->postJson('/api/orders', $this->payload([['product_id' => $a->product_id, 'qty' => 2]]))
            ->json('data.id');
        $this->assertSame(3, $a->refresh()->stock);

        $admin = $this->admin();
        $this->actingAs($admin, 'sanctum')->patchJson("/api/admin/orders/{$number}/status", ['status' => 'cancelled'])
            ->assertOk()->assertJsonPath('data.status', 'cancelled');
        $this->assertSame(5, $a->refresh()->stock);

        // Revenue tidak menghitung order yang dibatalkan
        $this->actingAs($admin, 'sanctum')->getJson('/api/admin/stats')->assertJsonPath('total_revenue', 0);

        $this->actingAs($admin, 'sanctum')->patchJson("/api/admin/orders/{$number}/status", ['status' => 'paid'])
            ->assertUnprocessable()->assertJsonValidationErrors('status');
        $this->actingAs($admin, 'sanctum')->patchJson("/api/admin/orders/{$number}/status", ['status' => 'bogus'])
            ->assertUnprocessable();
    }

    public function test_customer_only_sees_own_orders(): void
    {
        $a = $this->product('a', 100000, 10);
        $mine = User::factory()->create();
        $other = User::factory()->create();

        $myOrder = $this->actingAs($mine, 'sanctum')
            ->postJson('/api/orders', $this->payload([['product_id' => $a->product_id, 'qty' => 1]]))
            ->json('data.id');
        $this->actingAs($other, 'sanctum')
            ->postJson('/api/orders', $this->payload([['product_id' => $a->product_id, 'qty' => 1]]));

        $this->assertSame($mine->id, Order::where('order_number', $myOrder)->value('user_id'));

        $this->actingAs($mine, 'sanctum')->getJson('/api/orders')->assertOk()->assertJsonCount(1, 'data');
        $this->actingAs($mine, 'sanctum')->getJson("/api/orders/{$myOrder}")->assertOk();
        $this->actingAs($other, 'sanctum')->getJson("/api/orders/{$myOrder}")->assertForbidden();
    }
}
