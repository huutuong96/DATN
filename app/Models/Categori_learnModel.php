<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categori_learnModel extends Model
{
    use HasFactory;
    protected $table = 'categori_learns'; // Khai báo tên bảng

    protected $fillable = [
        'title',
        'status',
        'create_by',
        'update_by',
    ];
}
