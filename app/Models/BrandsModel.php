<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandsModel extends Model
{
    use HasFactory;

    protected $table = 'brands';

    protected $fillable = [
        'title',
        'slug',
        'image',
        'status',
        'parent_id',
        'create_by',
        'update_by',
    ];

     /**
     * Các trường sẽ được tự động chuyển đổi sang kiểu dữ liệu tương ứng.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
