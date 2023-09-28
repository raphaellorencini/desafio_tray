<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class SaleRepository extends BaseRepository
{
    public function __construct(protected UserRepository $user) {
        $this->setModel('Sale');
    }

    public function list($sellerId = null, $date = null, $simplePaginate = false, $paginateLimit = 15)
    {
        $data = [];
        $query = DB::table('sales as s')
            ->selectRaw('s.*, u.id as user_id, u.name as user_name, u.email as user_email, ROUND(s.value * 0.085, 2) as commission')
            ->leftJoin('users_sales as us', 'us.sale_id', '=', 's.id')
            ->leftJoin('users as u', 'u.id', '=', 'us.user_id')
            ->when(filled($sellerId) && is_numeric($sellerId), function (Builder $query) use ($sellerId) {
                $query->where('u.id', $sellerId);
            })
            ->when(filled($date), function (Builder $query) use ($date) {
                $dt = Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d');
                $query->whereBetween('s.created_at', ["{$dt} 00:00:00", "{$dt} 23:59:59"]);
            });

        if ($simplePaginate) {
            $data = $query->paginate($paginateLimit);
        } else {
            $data['data'] = $query->get();
        }
        return $data;
    }

    public function commission($sellerId = null, $date = null)
    {
        return DB::table('sales as s')
            ->selectRaw('SUM(ROUND(s.value * 0.085, 2)) as commission')
            ->when(filled($sellerId) && is_numeric($sellerId), function (Builder $query) use ($sellerId) {
                $query->leftJoin('users_sales as us', 'us.sale_id', '=', 's.id')
                    ->leftJoin('users as u', 'u.id', '=', 'us.user_id')
                    ->where('u.id', $sellerId);
            })
            ->when(filled($date), function (Builder $query) use ($date) {
                $dt = Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d');
                $query->whereBetween('s.created_at', ["{$dt} 00:00:00", "{$dt} 23:59:59"]);
            })
        ->first()?->commission ?? 0;
    }
}
