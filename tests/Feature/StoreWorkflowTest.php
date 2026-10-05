<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_is_public_and_can_filter_by_category(): void
    {
        $category = Category::factory()->create(['slug' => 'audio']);
        Product::factory()->for($category)->create(['name' => 'Auriculares', 'active' => true]);
        Product::factory()->create(['name' => 'Portátil', 'active' => true]);

        $this->get('/productos?category=audio')
            ->assertOk()
            ->assertSee('Auriculares')
            ->assertDontSee('Portátil');
    }

    public function test_product_can_be_added_updated_and_removed_from_cart(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        $this->post(route('cart.store', $product), ['quantity' => 2])
            ->assertSessionHas('cart.'.$product->id, 2);

        $this->patch(route('cart.update', $product), ['quantity' => 4])
            ->assertSessionHas('cart.'.$product->id, 4);

        $this->delete(route('cart.destroy', $product))
            ->assertSessionMissing('cart.'.$product->id);
    }

    public function test_checkout_requires_authentication(): void
    {
        $this->get(route('orders.checkout'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_order_and_stock_is_reduced(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 50, 'stock' => 5]);

        $response = $this->actingAs($user)
            ->withSession(['cart' => [$product->id => 2]])
            ->post(route('orders.store'), [
                'shipping_name' => 'Cliente Demo',
                'shipping_address' => 'Calle Uno 1',
                'shipping_city' => 'Oviedo',
                'shipping_postal_code' => '33001',
            ]);

        $order = Order::firstOrFail();
        $response->assertRedirect(route('orders.show', $order));
        $this->assertEquals('100.00', $order->total);
        $this->assertEquals(3, $product->fresh()->stock);
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'product_id' => $product->id, 'quantity' => 2]);
    }

    public function test_user_cannot_view_another_users_order(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $order = Order::create([
            'user_id' => $owner->id,
            'status' => 'pending',
            'total' => 25,
            'shipping_name' => 'Cliente',
            'shipping_address' => 'Calle Uno',
            'shipping_city' => 'Oviedo',
            'shipping_postal_code' => '33001',
        ]);

        $this->actingAs($other)->get(route('orders.show', $order))->assertForbidden();
    }

    public function test_profile_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Nombre Actualizado',
            'email' => $user->email,
            'phone' => '600123123',
            'address' => 'Calle Nueva 5',
            'city' => 'Gijón',
            'postal_code' => '33201',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Nombre Actualizado', 'city' => 'Gijón']);
    }
}
