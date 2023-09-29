<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Sale;
use App\Models\User;
use App\Repositories\SaleRepository;
use App\Repositories\UserRepository;

class SaleTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        //$this->seed(); // Executar seeders, se necessário
    }

    public function test_create_sale()
    {
        $user = User::factory()->create();
        $saleData = [
            'name' => 'Product Name',
            'value' => 100.00,
        ];

        $sale = Sale::create($saleData);

        $this->assertInstanceOf(Sale::class, $sale);
        $this->assertEquals('Product Name', $sale->name);
        $this->assertEquals(100.00, $sale->value);
    }

    public function test_sale_user_relationship()
    {
        $user = User::factory()->create();
        $saleData = [
            'name' => 'Product Name',
            'value' => 100.00,
        ];

        $sale = Sale::create($saleData);

        $user->sales()->attach($sale);

        $this->assertTrue($user->sales->contains($sale));
        $this->assertEquals(1, $user->sales->count());
    }

    public function test_sale_repository_list()
    {
        Sale::factory()
            ->count(3)
            ->sequence(fn (Sequence $sequence) => [
                'name' => 'Product '. ($sequence->index + 1),
                'value' => fake()->randomFloat(2, 5, 100)
            ])
            ->create();

        $saleRepository = new SaleRepository(new UserRepository());
        $sales = $saleRepository->list(simplePaginate: false);

        $this->assertCount(3, $sales['data']);
    }

    public function test_sale_repository_commission()
    {
        Sale::factory()
            ->create([
                'name' => 'Product 1',
                'value' => 10.1,
            ]);
        Sale::factory()
            ->create([
                'name' => 'Product 2',
                'value' => 11.1,
            ]);
        Sale::factory()
            ->create([
                'name' => 'Product 2',
                'value' => 12.1,
            ]);

        $saleRepository = new SaleRepository(new UserRepository());
        $commission = $saleRepository->commission();

        $this->assertIsFloat(floatval($commission->commission));
        $this->assertGreaterThanOrEqual(0.0, floatval($commission->commission));
        $this->assertEquals(2.83, floatval($commission->commission));
    }
}
