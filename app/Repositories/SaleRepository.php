<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class SaleRepository extends BaseRepository
{
    public function __construct(protected UserRepository $user) {
        $this->setModel('Sale');
    }

    public function paginate($sellerId = null)
    {
        $user = $this->user->getById($sellerId, true);
        $limit = $this->paginationLimit();
        $sales = $this->getModel()::when(filled($sellerId) && is_numeric($sellerId), function( Builder $query) use ($sellerId) {
            $query->whereHas('users', function(Builder $query) use ($sellerId) {
                $query
                    ->withTrashed()
                    ->where('user_id', $sellerId);
            });
        })->simplePaginate($limit);

        $salesList = $sales->toArray();
        $salesList['data'] = collect($sales->items())->map(function($value) use ($user) {
            $value->user = $user;
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
