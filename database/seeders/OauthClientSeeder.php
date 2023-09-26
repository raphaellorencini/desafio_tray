<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OauthClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $id = DB::table('oauth_clients')->insertGetId(
            [
                'name' => 'Personal Client',
                'secret' => Str::uuid(),
                'personal_access_client' => 1,
                'password_client' => 0,
                'revoked' => 0,
                'redirect' => 'http://localhost',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        DB::table('oauth_personal_access_clients')->insert(
            [
                'client_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
