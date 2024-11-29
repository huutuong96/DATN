<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class log_delete extends Model
{
    use HasFactory;
    protected $table = 'log_deletes'; // Khai báo tên bảng

    protected $fillable = [
        'id',
        'user',
        'do_action',
        'code',
        'created_at',
        'updated_at',
    ];
}
