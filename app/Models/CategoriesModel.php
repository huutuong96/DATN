<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriesModel extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'title',
        'slug',
        'index',
        'image',
        'status',
        'parent_id',
        'create_by',
        'update_by'
    ];

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'categoryattribute', 'category_id', 'attribute_id');
    }
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
