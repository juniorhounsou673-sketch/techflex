<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechflexBoutiqueTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_home_displays_the_seeded_catalogue(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Accessoires Apple')
            ->assertSee('AirPods Pro 2')
            ->assertSee('Montres de luxe');
    }

    public function test_admin_and_reseller_areas_are_protected(): void
    {
        $this->get('/admin')->assertRedirect('/connexion');
        $this->get('/revendeur')->assertRedirect('/connexion');

        $admin = User::where('email', 'admin@techflex.test')->firstOrFail();
        $reseller = User::where('email', 'revendeur1@techflex.test')->firstOrFail();

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($reseller)->get('/revendeur')->assertOk();
    }

    public function test_reseller_link_registers_the_seller_then_direct_mode_clears_it(): void
    {
        $this->get('/r/REV001')->assertRedirect('/');
        $this->assertSame('REV001', session('reseller_code'));

        $this->get('/direct')->assertRedirect('/');
        $this->assertNull(session('reseller_code'));
    }

    public function test_checkout_creates_an_order_and_reserves_stock(): void
    {
        $product = Product::where('name', 'AirPods Pro 2')->firstOrFail();
        $stockBefore = $product->stock;

        $this->post(route('cart.add', $product), ['quantity' => 1])->assertRedirect(route('cart'));

        $response = $this->post(route('checkout.store'), [
            'client_name' => 'Client Test',
            'client_phone' => '+229 01 02 03 04 05',
            'payment_method' => 'momo_site',
        ]);

        $response->assertRedirectContains('/commande/');
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
        $this->assertSame($stockBefore - 1, $product->fresh()->stock);
    }
}
