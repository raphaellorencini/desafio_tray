<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleAdmin = Role::where('name', 'admin')->first();

        $admin = new User();
        $admin->name = 'Admin';
        $admin->email = 'admin@admin.com.br';
        $admin->password = bcrypt('123456');
        $admin->save();
        $admin->roles()->attach($roleAdmin);


    }
}
