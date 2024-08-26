<?php

namespace App\Models;


use App\Models\Shop;
use App\Models\CategoriesModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categori_shopsModel extends Model
{
    use HasFactory;

    protected $table = 'colors';

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

    public function category()
    {
        return $this->belongsTo(CategoriesModel::class, 'category_id_main');
    }

    // Quan hệ với bảng shops
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
