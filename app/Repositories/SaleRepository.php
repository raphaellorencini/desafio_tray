<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class SaleRepository extends BaseRepository
{
    public function __construct(protected UserRepository $user) {
        $this->setModel('Sale');
    }

    public function listQuery($sellerId = null)
    {
        return $this->getModel()::when(filled($sellerId) && is_numeric($sellerId), function( Builder $query) use ($sellerId) {
            $query->whereHas('users', function(Builder $query) use ($sellerId) {
                $query
                    ->withTrashed()
                    ->where('user_id', $sellerId);
            });
        });
    }

    public function comissionList($sellerId = null): array
    {
        $sales = $this->listQuery($sellerId)->get();
        $salesList = [];
        $salesList['data'] = $sales->toArray();
        $salesList['data'] = Arr::map($salesList['data'], function($value) {
            $value['commission'] = floatval($value['commission']);
            return $value;
        });

        $salesList['total_commission'] = floatval(sprintf('%.2f', collect($salesList['data'])->sum('commission')));

        return $salesList;
    }

    public function paginate($sellerId = null)
    {
        $user = $sellerId ? $this->user->getById($sellerId, true) : null;
        $limit = $this->paginationLimit();
        $sales = $this->listQuery($sellerId)
            ->simplePaginate($limit);

        $salesList = $sales->toArray();
        $salesList['data'] = collect($sales->items())->map(function($value) use ($user) {
            $value->user = $user ?? $value->users->toArray()[0];
            unset($value->users);
            return $value;
        })->toArray();
        $salesList['data'] = Arr::map($salesList['data'], function($value) {
            $value['commission'] = floatval($value['commission']);
            return $value;
        });

        return $salesList;
    }
}
