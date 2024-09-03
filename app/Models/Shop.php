<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LearnModel;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'Owner_id', 
        'shop_name',
        'slug',
        'pick_up_address',
        'image',
        'cccd',
        'status',
        'create_by',
        'update_by',
        'tax_id',
    ];
    public function learns()
        {
            return $this->belongsToMany(LearnModel::class, 'Learning_seller', 'shop_id', 'learn_id');
        }
}
