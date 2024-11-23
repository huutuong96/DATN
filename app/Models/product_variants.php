<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_variants extends Model
{
    use HasFactory;
    protected $table = 'product_variants';
    protected $primaryKey = 'id';
    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'stock',
        'price',
        'images',
        'deleted_at',
        'deleted_by',
        'is_deleted',
        'created_at',
        'updated_at',
        'attribute_id',
        'value_id',
        'id_fe',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'variantattribute', 'variant_id', 'attribute_id', 'value_id')
                    ->withPivot('variant_id', 'attribute_id', 'value_id');
    }
    public function images()
    {
        return $this->hasMany(Image::class, 'product_variant_id');
    }
    public function variant()
    {
        return $this->hasMany(product_variants::class);
    }


}
