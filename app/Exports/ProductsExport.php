<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductsExport implements FromCollection
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
    public function collection()
    {
        if ($this->request->status == 101) {
            return Product::where('status', 101)->select('name','slug', 'sku','description','infomation',
            'price','show_price','image','quantity','sold_count',
            'view_count','create_by','update_by',
            'updated_at','category_id','shop_id','status','height',
            'length','weight','width','update_version','is_delete',
            )->get();
        }
        if ($this->request->status == 2) {
            return Product::where('status', 2)->select('name','slug', 'sku','description','infomation',
            'price','show_price','image','quantity','sold_count',
            'view_count','create_by','update_by',
            'updated_at','category_id','shop_id','status','height',
            'length','weight','width','update_version','is_delete',
            )->get();
        }
        if ($this->request->status == 1) {
            return Product::where('status', 1)->select('name','slug', 'sku','description','infomation',
            'price','show_price','image','quantity','sold_count',
            'view_count','create_by','update_by',
            'updated_at','category_id','shop_id','status','height',
            'length','weight','width','update_version','is_delete',
            )->get();
        }
        if ($this->request->role == 1) {
            return Product::where('role_id', 1)->select('name','slug', 'sku','description','infomation',
            'price','show_price','image','quantity','sold_count',
            'view_count','create_by','update_by',
            'updated_at','category_id','shop_id','status','height',
            'length','weight','width','update_version','is_delete',
            )->get();
        }
        if ($this->request->role == 2) {
            return Product::where('role_id', 2)->select('name','slug', 'sku','description','infomation',
            'price','show_price','image','quantity','sold_count',
            'view_count','create_by','update_by',
            'updated_at','category_id','shop_id','status','height',
            'length','weight','width','update_version','is_delete',
            )->get();
        }
        if ($this->request->role == 3) {
            return Product::where('role_id', 3)->select('name','slug', 'sku','description','infomation',
            'price','show_price','image','quantity','sold_count',
            'view_count','create_by','update_by',
            'updated_at','category_id','shop_id','status','height',
            'length','weight','width','update_version','is_delete',
            )->get();
        }
        if ($this->request->role == 4) {
            return Product::where('role_id', 4)->select('name','slug', 'sku','description','infomation',
            'price','show_price','image','quantity','sold_count',
            'view_count','create_by','update_by',
            'updated_at','category_id','shop_id','status','height',
            'length','weight','width','update_version','is_delete',
            )->get();
        }
        return 'Lỗi xuất data';
    }
}
