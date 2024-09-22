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
    ];

    public function variant()
    {
        return $this->belongsTo(product_variants::class);
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function value()
    {
        return $this->belongsTo(Value::class);
    }
}
