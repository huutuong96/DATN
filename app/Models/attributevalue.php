<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class attributevalue extends Model
{
    use HasFactory;
    protected $table = 'attributevalue';

    protected $primaryKey = 'id';
    protected $fillable = [
        'value',
        'attribute_id',
        'image',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
