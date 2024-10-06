<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdersModel extends Model
{
    use HasFactory;

    protected $table = 'orders';

    // Define the relationship with OrderDetailsModel
    public function orderDetails()
    {
        return $this->hasMany(OrderDetailsModel::class, 'order_id');
    }

    protected $fillable = [
        'payment_id',
        'ship_id',
        'voucher_id',
        'user_id',
        'shop_id',
        'status',
        'net_amount',
        'delivery_address',
        'create_at',
        'update_at',
        'total_amount',
        'height',
        'length',
        'weight',
        'width',
        'note',
        'required_note',
        'return_phone',
        'return_address',
        'return_district_id',
        'return_ward_code',
        'from_name',
        'from_phone',
        'from_address',
        'from_ward_name',
        'from_district_name',
        'from_province_name',
        'to_name',
        'to_phone',
        'to_address',
        'to_ward_name',
        'to_district_name',
        'to_province_name',
        'cod_amount',
        'content',
        'cod_failed_amount',
        'pick_station_id',
        'deliver_station_id',
        'insurance_value',
        'service_id',
        'service_type_id',
        'coupon',
        'pickup_time',
        'contact_number',
        'client_order_code',
        'order_infomation'
    ];


     /**
     * Các trường sẽ được tự động chuyển đổi sang kiểu dữ liệu tương ứng.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public const STATUS_PENDING_CONFIRMATION = 1;
    public const STATUS_PENDING_PICKUP = 2;
    public const STATUS_PROCESSING = 3;
    public const STATUS_SHIPPED = 4;
    public const STATUS_COMPLETED = 5;
    public const STATUS_CANCELLED = 6;
    public const STATUS_REFUND_CONFIRM = 7;
    public const STATUS_REFUNDING = 8;
    public const STATUS_REFUNDED = 9;

    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING_CONFIRMATION => 'Chờ xác nhận',
            self::STATUS_PENDING_PICKUP => 'Chờ lấy hàng',
            self::STATUS_PROCESSING => 'Đang xử lý',
            self::STATUS_SHIPPED => 'Đã giao hàng',
            self::STATUS_COMPLETED => 'Hoàn thành',
            self::STATUS_CANCELLED => 'Đã hủy',
            self::STATUS_REFUND_CONFIRM => 'Chờ hoàn tiền',
            self::STATUS_REFUNDING => 'Đang hoàn tiền',
            self::STATUS_REFUNDED => 'Đã hoàn tiền',
        ];
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatusOptions()[$this->status] ?? 'Unknown Status';
    }

    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
