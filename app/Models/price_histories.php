<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class price_histories extends Model
{
    use HasFactory;
    protected $table = 'price_histories';
    protected $primaryKey = 'id';
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'price',
        'old_price',
        'new_price',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(product_variants::class);
    }
}
