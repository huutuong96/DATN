<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigModel extends Model
{
    use HasFactory;
    protected $table = 'config_main';
    protected $primaryKey = 'id';
    protected $fillable = [
        'logo_header',
        'logo_footer',
        'icon',
        'thumbnail',
        'main_color',
        'is_active',
        'create_by'
    ];
    public $timestamps = false;

}
