<?php

namespace App\Console\Commands;

use App\Mail\ComissionSendMail;
use App\Repositories\SaleRepository;
use App\Repositories\UserRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendCommissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-commissions';

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
        $salesRepository = new SaleRepository(new UserRepository());
        $users = DB::table('users as u')
            ->select('u.*')
            ->leftJoin('users_roles as ur', 'ur.user_id', '=','u.id')
            ->leftJoin('roles as r', 'r.id', '=', 'ur.role_id')
            ->whereNull('deleted_at')
            ->get();
        foreach ($users as $user) {
            $this->sendMail($salesRepository, $user);
        }

        $this->sendMail($salesRepository);
    }

    public function sendMail($salesRepository, $user = null)
    {
        $date = now();
        $datacommission = $salesRepository->commission(sellerId: $user?->id, date: $date);
        $commission = $datacommission->commission;
        $sales = $datacommission->sales;
        $data = [
            'subject' => 'Comissão - Data: '.$date->format('d/m/Y'),
            'name' => $user?->name,
            'email' => $user?->email,
            'commission' => number_format($commission, 2, ",", "."),
            'sales' => number_format($sales, 2, ",", "."),
            'date' => $date,
        ];

        $to = $user?->email ?? env('MAIL_TO');
        Mail::to($to)->send(new ComissionSendMail($data));
    }
}
