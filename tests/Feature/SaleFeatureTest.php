<?php

namespace Tests\Feature;

use App\Models\Role;
use Database\Seeders\OauthClientSeeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Sale;

class SaleFeatureTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_list_sales()
    {
        $this->seed(OauthClientSeeder::class);

        $user = User::factory()->create();
        $token = $user->createToken('test-token')->accessToken;

        $roles = Role::factory()->create(['name' => 'seller']);
        User::factory()
            ->count(1)
            ->sequence(fn (Sequence $sequence) => [
                'name' => 'Seller '. ($sequence->index + 1),
                'email' => sprintf("seller%s@seller.com.br", $sequence->index + 1),
                'password' => bcrypt('123456'),
            ])
            ->hasAttached($roles)
            ->hasAttached(Sale::factory()
                ->count(3)
                ->sequence(fn (Sequence $sequence) => [
                    'name' => 'Product '. ($sequence->index + 1),
                    'value' => fake()->randomFloat(2, 5, 100)
                ]))
            ->create();
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/api/v1/sales');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'list.data');
    }

    public function test_list_sales_with_seller_filter()
    {
        $this->seed(OauthClientSeeder::class);

        $user = User::factory()->create();
        $token = $user->createToken('test-token')->accessToken;

        $roles = Role::factory()->create(['name' => 'seller']);
        $seller = User::factory()
            ->count(1)
            ->sequence(fn (Sequence $sequence) => [
                'name' => 'Seller '. ($sequence->index + 1),
                'email' => sprintf("seller%s@seller.com.br", $sequence->index + 1),
                'password' => bcrypt('123456'),
            ])
            ->hasAttached($roles)
            ->hasAttached(Sale::factory()
                ->count(3)
                ->sequence(fn (Sequence $sequence) => [
                    'name' => 'Product '. ($sequence->index + 1),
                    'value' => fake()->randomFloat(2, 5, 100)
                ]))
            ->create();

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/api/v1/sales?seller_id='. $seller[0]->id);

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'list.data');
    }

    public function test_create_sale()
    {
        $this->seed(OauthClientSeeder::class);

        $user = User::factory()->create();
        $token = $user->createToken('test-token')->accessToken;

        $roles = Role::factory()->create(['name' => 'seller']);
        $seller = User::factory()->hasAttached($roles)->create();
        $data = [
            'name' => 'Product 1',
            'value' => 100.01,
            'seller_id' => $seller->id,
        ];

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/sales', $data);
        unset($data['seller_id']);

        $response->assertStatus(201);
        $response->assertJson([
            'name' => 'Product 1',
            'value' => 100.01,
        ]);

        $this->assertDatabaseHas('sales', $data);
    }
}
