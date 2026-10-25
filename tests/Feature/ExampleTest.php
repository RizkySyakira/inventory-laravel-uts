<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test root route returns catalog storefront.
     */
    public function test_root_route_is_accessible(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Katalog Produk');
    }

    public function test_customer_shop_is_accessible(): void
    {
        $response = $this->get('/shop');

        $response->assertStatus(200);
        $response->assertSee('Katalog Produk');
    }

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('INVENTORY STORE');
    }
}
