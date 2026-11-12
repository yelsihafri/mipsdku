
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'customer_name',
        'product_name',
        'quantity',
        'total_price',
        'total_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'quantity' => 'integer',
            'total_price' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
