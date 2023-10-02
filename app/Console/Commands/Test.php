<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Test '.rand()."\n");

//        $roleAdmin = Role::where('name', 'admin')->first();
//        $admin = new User();
//        $admin->name = 'Admin'.rand();
//        $admin->email = 'admin'.rand().'@admin.com.br';
//        $admin->password = bcrypt('123456');
//        $admin->save();
//        $admin->roles()->attach($roleAdmin);
    }
}
