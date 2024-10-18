<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class configModel extends Model
{
    use HasFactory;
    protected $table = 'config_main';
    protected $primaryKey = 'id';
    protected $fillable = [
        'logo_header',
        'logo_footer',
        'main_color',
        'icon',
        'thumbnail',
    ];
}
