<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tax_category extends Model
{
    use HasFactory;

    protected $table = 'tax_category';

    protected $fillable = [
        'tax_id',
        'category_id',
    ];

    public $timestamps = false;
}
