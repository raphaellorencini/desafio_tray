<?php

namespace Tests\Feature;

use Database\Seeders\OauthClientSeeder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class UserFeatureTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    public function test_create_user_with_role_admin()
    {
        $this->seed(OauthClientSeeder::class);
        Role::create(['name' => 'admin']);

        $user = User::factory()->create();
        $token = $user->createToken('test-token')->accessToken;

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/users', [
            'name' => 'Admin User',
            'email' => 'admin@test.com.br',
            'password' => '123456',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'name' => 'Admin User',
            'email' => 'admin@test.com.br',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Admin User',
            'email' => 'admin@test.com.br',
        ]);

        $user = User::where('email', 'admin@test.com.br')->whereHas('roles', function (Builder $query) {
            $query->where('name', 'admin');
        })->first();
        $role = (bool)$user->roles;

        $this->assertTrue($role);
    }

    public function test_create_user_with_role_seller()
    {
        $this->seed(OauthClientSeeder::class);
        Role::create(['name' => 'seller']);

        $user = User::factory()->create();
        $token = $user->createToken('test-token')->accessToken;

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/sellers', [
            'name' => 'Seller User',
            'email' => 'seller@test.com.br',
            'password' => '123456',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'name' => 'Seller User',
            'email' => 'seller@test.com.br',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Seller User',
            'email' => 'seller@test.com.br',
        ]);

        $user = User::where('email', 'seller@test.com.br')->whereHas('roles', function (Builder $query) {
            $query->where('name', 'seller');
        })->first();
        $role = (bool)$user->roles;

        $this->assertTrue($role);
    }

    public function test_list_users()
    {
        $this->seed();

        $user = User::factory()->create();
        $token = $user->createToken('test-token')->accessToken;

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', '/api/v1/users');

        $response->assertStatus(200);
    }

    public function test_update_user()
    {
        $this->seed(OauthClientSeeder::class);
        $roles = Role::factory()->create(['name' => 'admin']);

        // Create a user to update
        $user = User::factory()
            ->hasAttached($roles)
            ->create([
            'name' => 'Updated User Name',
            'email' => 'updated@test.com.br',
            'password' => bcrypt('123456'),
        ]);

        $authUser = User::factory()->create();
        $token = $authUser->createToken('test-token')->accessToken;

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/v1/users/' . $user->id, [
            'name' => 'Updated User Name1',
            'email' => 'updated1@test.com.br',
            'password' => '456456',
        ]);

        $response->assertStatus(200);
    }

    public function test_delete_user()
    {
        $this->seed(OauthClientSeeder::class);
        Role::create(['name' => 'admin']);

        $user = User::factory()->create();

        $authUser = User::factory()->create();
        $token = $authUser->createToken('test-token')->accessToken;

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->json('DELETE', '/api/v1/users/' . $user->id);

        $response->assertStatus(204);
    }
}
