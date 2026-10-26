<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'sale_number',
        'total',
        'payment_method',
        'payment_status',
        'notes',
        'status',
        'sold_at',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'sold_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
#relasi sale ke sale item
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }
}
