<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class variantattribute extends Model
{
    use HasFactory;
    protected $table = 'variantattribute';
    protected $primaryKey = 'id';
    protected $fillable = [
        'variant_id',
        'attribute_id',
        'value_id',
        'shop_id',
        'product_id',
    ];

    public $timestamps = false;

    public function variant()
    {
        return $this->belongsTo(product_variants::class);
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function values()
    {
        return $this->hasMany(attributevalue::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
