<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class subdomains extends Model
{
    use HasFactory;
    protected $table = 'sub_domains'; // Khai báo tên bảng

    protected $fillable = [
        'id',
        'sub_domain',
        'main_domain',
        'descriptions',
        'created_at',
        'updated_at',
        'status',
        'icon_sub',
    ];
}
