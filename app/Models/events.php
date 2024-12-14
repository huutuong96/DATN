<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class events extends Model
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
        'product_apply',
        'shop_apply',
        'user_apply',
        'product_accept',
        'shop_accept',
        'user_accept',
        'visits',
        'click_to_cart',
        'top_product',
        'top_shop',
        'click_shop',
        'click_product',
    ];
    public $timestamps = false;
}
