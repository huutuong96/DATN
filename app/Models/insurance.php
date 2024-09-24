<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class insurance extends Model
{
    use HasFactory;
    protected $table = 'insurance';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'price',
        'code',
        'status',
        'ship_company_id'
    ];

    public $timestamps = false;

}
