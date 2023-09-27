<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'value',
    ];

    protected $casts = [
        'value' => 'float',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_sales');
    }
}
