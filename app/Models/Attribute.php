<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;
    protected $table = 'attributes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'display_name',
        'type',
        'deleted_at',
        'deleted_by',
        'is_deleted',
        'image',
    ];

    protected $hidden = [
        'display_name',
        'type',
        'deleted_at',
        'deleted_by',
        'is_deleted',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_variants');
    }

    // Nếu bạn có bảng riêng cho giá trị thuộc tính, hãy thêm quan hệ này
    public function values()
    {
        return $this->hasMany(attributevalue::class);
    }
}
