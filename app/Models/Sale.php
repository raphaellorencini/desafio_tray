<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'value',
    ];

    protected $casts = [
        'value' => 'float',
        'commission' => 'float',
    ];

    protected $appends = [
        'commission',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_sales');
    }

    public function commission(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => sprintf('%.2f', $attributes['value'] * 0.085),
        );
    }
}
