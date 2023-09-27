<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;

class UserRepository extends BaseRepository
{
    public function __construct() {
        $this->setModel('User');
    }

    public function paginate($userRole = null)
    {
        $limit = $this->paginationLimit();

        $users = $this->getModel()::when(in_array($userRole, ['admin', 'seller']), function( Builder $query) use ($userRole) {
            $query->whereHas('roles', function(Builder $query) use ($userRole) {
                $query->where('name', $userRole);
            });
        })
        ->simplePaginate($limit);

        $userList = $users->toArray();
        $userList['data'] = collect($users->items())->map(function($value) {
            $role = $value->roles->toArray()[0];
            $value->role = $role['name'];
            unset($value->roles);
            return $value;
        })->toArray();
        return $userList;
    }

    public function adminsUsersList()
    {
        return $this->paginate('admin');
    }

    public function sellersUsersList()
    {
        return $this->paginate('seller');
    }
}
