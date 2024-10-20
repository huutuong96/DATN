<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ship_companies extends Model
{
    use HasFactory;
    protected $table = 'ship_companies';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'code',
        'status',
    ];

    public $timestamps = false;
}
