<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColorsModel extends Model
{
    use HasFactory;

    protected $table = 'colors';

    protected $fillable = [
        'title',
        'product_id',
        'index',
        'image',
        'status',
        'create_by',
        'update_by'
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
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
