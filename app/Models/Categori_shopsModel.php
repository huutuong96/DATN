<?php

namespace App\Models;


use App\Models\Shop;
use App\Models\CategoriesModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categori_shopsModel extends Model
{
    use HasFactory;

    protected $table = 'categori_shops';

    protected $fillable = [
        'index',
        'title',
        'slug',
        'image',
        'status',
        'parent_id',
        'create_by',
        'update_by',
        'category_id_main',
        'shop_id'
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
