<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    protected $table = 'images';
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'url',
        'status',
        'create_at',
        'update_at',
    ];

    protected $hidden = [
        'id',
        'product_id',
        'product_variant_id',
        'status',
        'created_at',
        'updated_at',
        'update_version',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function productVariant()
    {
        return $this->belongsTo(product_variants::class, 'product_variant_id');
    }

}
