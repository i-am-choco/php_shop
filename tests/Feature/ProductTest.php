<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use WithFaker, DatabaseTransactions;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_batch_create(): void
    {
        Bus::fake();
        Sanctum::actingAs(User::factory()->create());

        $response = $this->post(
            "/api/products/batchCreate",
            [
                "products" => [
                    1 => [
                        "name" => 'test',
                        "description" => "test description",
                        "price" => '4.23'
                    ],
                ],
            ],
        );

        $response->assertOk()
            ->assertJson([
                "message" => 'Success'
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'test',
            'description' => 'test description',
            'price' => 4.23,
        ]);

    }
}
