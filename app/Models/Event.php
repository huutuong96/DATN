<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $fillable = [
        'event_title',
        'event_day',
        'event_month',
        'event_year',
        'qualifier',
        'voucher_apply',
        'is_mail',
        'point',
        'is_share_facebook',
        'is_share_zalo',
        'where_order',
        'where_price',
        'date',
        'from',
        'to',
        'status',
        'description',
        'images',
    ];
    public $timestamps = false;
}
