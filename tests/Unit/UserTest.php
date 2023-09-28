<?php

namespace Tests\Unit;

use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class UserTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    public function test_create_user_with_admin_role()
    {
        $role = Role::create(['name' => 'admin']);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@test.com.br',
            'password' => bcrypt('123456'),
        ]);

        $user->roles()->attach($role);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@test.com.br',
        ]);

        $this->assertTrue($user->roles->contains('name', 'admin'));
    }

    public function test_create_user_with_seller_role()
    {
        $role = Role::create(['name' => 'seller']);

        $user = User::create([
            'name' => 'Seller User',
            'email' => 'seller@test.com.br',
            'password' => bcrypt('123456'),
        ]);

        $user->roles()->attach($role);

        $this->assertDatabaseHas('users', [
            'name' => 'Seller User',
            'email' => 'seller@test.com.br',
        ]);

        $this->assertTrue($user->roles->contains('name', 'seller'));
    }

    public function test_attempt_to_create_user_with_duplicate_email()
    {
        User::create([
            'name' => 'Unique User',
            'email' => 'unique@test.com.br',
            'password' => bcrypt('password'),
        ]);

        $response = true;
        try {
            User::create([
                'name' => 'Duplicate User',
                'email' => 'unique@test.com.br', // Same email as the previous user
                'password' => bcrypt('password'),
            ]);
        } catch (UniqueConstraintViolationException $e) {
            $response = false;
        }

        $this->assertFalse($response);
    }

    public function test_update_user()
    {
        $user = User::create([
            'name' => 'Update User',
            'email' => 'update@test.com.br',
            'password' => bcrypt('password'),
        ]);

        $user->update(['email' => 'updated@test.com.br']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'updated@test.com.br',
        ]);
    }

    public function test_delete_user()
    {
        $user = User::create([
            'name' => 'Delete User',
            'email' => 'delete@test.com.br',
            'password' => bcrypt('password'),
        ]);

        $user->delete();

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }
}


