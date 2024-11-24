<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LearnModel;

class Shop extends Model
{
    use HasFactory;
    protected $table = 'shops'; // Tên bảng trong cơ sở dữ liệu
    protected $primaryKey = 'id'; // Khóa chính của bảng
    protected $fillable = [
        'visits',
        'update_by',
        'updated_at',
        'tax_id',
        'status',
        'slug',
        'shop_name',
        'revenue',
        'rating',
        'pick_up_address',
        'owner_id',
        'location',
        'image',
        'email',
        'description',
        'create_by',
        'created_at',
        'contact_number',
        'cccd',
        'shopid_GHN',
        'province',
        'province_id',
        'district',
        'district_id',
        'ward',
        'ward_id',
        'vnp_TmnCode',
        'wallet',
        'account_number',
        'bank_name',
        'owner_bank',
    ];
    public function learns()
    {
        return $this->belongsToMany(LearnModel::class, 'Learning_seller', 'shop_id', 'learn_id');
    }
    public function user()
    {
        return $this->belongsToMany(User::class, 'shop_managers', 'shop_id', 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'shop_id', 'id');
    }

}
