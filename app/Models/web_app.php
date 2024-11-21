<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class web_app extends Model
{
    use HasFactory;
    protected $table = 'web_apps';

    // Các trường có thể gán giá trị hàng loạt
    protected $fillable = [
        'id',
        'name',
        'icon',
        'url',
    ];

    public $timestamps = false;
}
