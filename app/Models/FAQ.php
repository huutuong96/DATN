<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    use HasFactory;

    protected $table = 'faq'; // Thay đổi để connect tới table

    protected $fillable = [
        'title',
        'content',
        'status',
        'index',
        'create_by',
        'update_by',
        'create_at',
        'update_at',
    ];
}
