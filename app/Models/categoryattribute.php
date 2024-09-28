<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CategoriesModel;

class categoryattribute extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'categoryattribute';
    protected $primaryKey = 'id';
    protected $fillable = [
        'category_id',
        'attribute_id',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(CategoriesModel::class, 'category_id', 'id');
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id', 'id');
    }
}
