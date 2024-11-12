<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;

    protected $table = 'taxs';

    protected $fillable = [
        'title',
        'type',
        'tax_number',
        'rate',
        'status',
        'create_by',
        'update_by',
        'create_at',
        'update_at',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'create_by'); 
    }
}
