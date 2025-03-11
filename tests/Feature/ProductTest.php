<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Currency;

use App\Models\User;


class ProductTest extends TestCase
{


     /**
     * Test the index method.
     *
     * @return void
     */
    public function test_index()
    {

        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/products');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'currency_id',
                    'tax_cost',
                    'manufacturing_cost',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    /**
     * Test the store method.
     *
     * @return void
     */
    public function test_store()
    {
        $data = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'currency_id' => 1,
            'tax_cost' => 5.00,
            'manufacturing_cost' => 50.00,
        ];

        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/products', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', $data);
    }

    /**
     * Test the show method.
     *
     * @return void
     */
    public function test_show()
    {

        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/products/' . 1);

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => 1]);
    }

    /**
     * Test the update method.
     *
     * @return void
     */
    public function test_update()
    {
        $data = [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'price' => 89.99,
            'currency_id' => 1,
            'tax_cost' => 4.50,
            'manufacturing_cost' => 45.00,
        ];

        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->put('/api/v1/products/' . 1, $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', $data);
    }

    /**
     * Test the destroy method.
     *
     * @return void
     */
    public function test_destroy()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('/api/v1/products/' . 1);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('products', ['id' => 1]);
    }

    /**
     * Test the getPrices method.
     *
     * @return void
     */
    public function test_get_prices()
    {

        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/products/' . 2 . '/prices');

        $response->assertStatus(200);
    }

    /**
     * Test the storePrice method.
     *
     * @return void
     */
    public function test_store_price()
    {

        $dolar=Currency::where('name', 'US Dollar')->first();

        $data = [
            'price' => 100.00,
            'currency_id' => $dolar->id,
        ];

        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/products/' . 2 . '/prices', $data);

        $response->assertStatus(201);
    }
}
