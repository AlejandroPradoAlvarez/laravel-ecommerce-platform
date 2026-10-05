<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_access_admin_panel(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_can_create_product(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Producto profesional',
            'slug' => 'producto-profesional',
            'description' => 'Descripción del producto para el catálogo.',
            'price' => 99.90,
            'stock' => 8,
            'active' => 1,
        ])->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', ['slug' => 'producto-profesional', 'stock' => 8]);
    }

    public function test_admin_can_update_order_status(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create();
        $order = Order::create([
            'user_id' => $customer->id,
            'status' => 'pending',
            'total' => 40,
            'shipping_name' => 'Cliente',
            'shipping_address' => 'Dirección',
            'shipping_city' => 'Oviedo',
            'shipping_postal_code' => '33001',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.orders.update', $order), ['status' => 'shipped'])
            ->assertSessionHasNoErrors();

        $this->assertSame('shipped', $order->fresh()->status);
    }
}
