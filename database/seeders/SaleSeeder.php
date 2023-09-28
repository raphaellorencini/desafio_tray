<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Role::factory()->create(['name' => 'seller']);

        User::factory()
            ->count(10)
            ->sequence(fn (Sequence $sequence) => [
                'name' => 'Seller '. ($sequence->index + 1),
                'email' => sprintf("seller%s@seller.com.br", $sequence->index + 1),
                'password' => bcrypt('123456'),
            ])
            ->hasAttached($roles)
            ->hasAttached(Sale::factory()
                ->count(5)
                ->sequence(fn (Sequence $sequence) => [
                    'name' => 'Product '. ($sequence->index + 1),
                    'value' => fake()->randomFloat(2, 5, 100)
                ]))
            ->create();

    }
}
