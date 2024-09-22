<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_prices extends Model
{
    use HasFactory;
    protected $table = 'product_prices';
    protected $primaryKey = 'id';
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'price',
        'price_sale',
        'effective_date',
        'start_date',
        'end_date',
        'is_active'
    ];

    // protected $casts = [
    //     'price' => 'decimal:2',
    //     'price_sale' => 'decimal:2',
    //     'effective_date' => 'datetime',
    // ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(product_variants::class);
    }
}
