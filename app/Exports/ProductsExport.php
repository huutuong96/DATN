<?php

namespace App\Exports;
use App\Models\product_variants;
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
        
        if ($this->request->shop_id && $this->request->status == 0) {
            $producs = Product::where('shop_id', $this->request->shop_id)->where('status', 0)->select('id','name','slug', 'sku','description','infomation',
            'price','show_price','image','quantity','sold_count',
            'view_count','create_by','update_by',
            'updated_at','category_id','shop_id','status','height',
            'length','weight','width','update_version','is_delete',
            )->get();

            $producs->each(function($product) {
                $variant = product_variants::where('product_id', $product->id)->select('product_id','name', 'sku','stock','price','images')->first();
                if ($variant) {
                    $product->product_id = $variant->product_id;
                    $product->variant_name = $variant->name;
                    $product->variant_sku = $variant->sku;
                    $product->variant_stock = $variant->stock;
                    $product->variant_price = $variant->price;
                    $product->variant_images = $variant->images;
                } else {
                    $product->product_id = null;
                    $product->variant_name = null;
                    $product->variant_sku = null;
                    $product->variant_stock = null;
                    $product->variant_price = null;
                    $product->variant_images = null;
                }
            });
            return $producs;
        }
        if ($this->request->shop_id && $this->request->status == 1) {
            $producs = Product::where('shop_id', $this->request->shop_id)->where('status', 1)->select('id','name','slug', 'sku','description','infomation',
            'price','show_price','image','quantity','sold_count',
            'view_count','create_by','update_by',
            'updated_at','category_id','shop_id','status','height',
            'length','weight','width','update_version','is_delete',
            )->get();
            $producs->each(function($product) {
                $variant = product_variants::where('product_id', $product->id)->select('product_id','name', 'sku','stock','price','images')->first();
                if ($variant) {
                    $product->product_id = $variant->product_id;
                    $product->variant_name = $variant->name;
                    $product->variant_sku = $variant->sku;
                    $product->variant_stock = $variant->stock;
                    $product->variant_price = $variant->price;
                    $product->variant_images = $variant->images;
                } else {
                    $product->product_id = null;
                    $product->variant_name = null;
                    $product->variant_sku = null;
                    $product->variant_stock = null;
                    $product->variant_price = null;
                    $product->variant_images = null;
                }
            });
            return $producs;
        }
        if ($this->request->shop_id && $this->request->status == 2) {
            $producs = Product::where('shop_id', $this->request->shop_id)->where('status', 2)->select('id','name','slug', 'sku','description','infomation',
            'price','show_price','image','quantity','sold_count',
            'view_count','create_by','update_by',
            'updated_at','category_id','shop_id','status','height',
            'length','weight','width','update_version','is_delete',
            )->get();
            $producs->each(function($product) {
                $variant = product_variants::where('product_id', $product->id)->select('product_id','name', 'sku','stock','price','images')->first();
                if ($variant) {
                    $product->product_id = $variant->product_id;
                    $product->variant_name = $variant->name;
                    $product->variant_sku = $variant->sku;
                    $product->variant_stock = $variant->stock;
                    $product->variant_price = $variant->price;
                    $product->variant_images = $variant->images;
                } else {
                    $product->product_id = null;
                    $product->variant_name = null;
                    $product->variant_sku = null;
                    $product->variant_stock = null;
                    $product->variant_price = null;
                    $product->variant_images = null;
                }
            });
            return $producs;
        }
        if ($this->request->shop_id && $this->request->status == 3) {
            $producs = Product::where('shop_id', $this->request->shop_id)->where('status', 3)->select('id','name','slug', 'sku','description','infomation',
            'price','show_price','image','quantity','sold_count',
            'view_count','create_by','update_by',
            'updated_at','category_id','shop_id','status','height',
            'length','weight','width','update_version','is_delete',
            )->get();
            $producs->each(function($product) {
                $variant = product_variants::where('product_id', $product->id)->select('product_id','name', 'sku','stock','price','images')->first();
                if ($variant) {
                    $product->product_id = $variant->product_id;
                    $product->variant_name = $variant->name;
                    $product->variant_sku = $variant->sku;
                    $product->variant_stock = $variant->stock;
                    $product->variant_price = $variant->price;
                    $product->variant_images = $variant->images;
                } else {
                    $product->product_id = null;
                    $product->variant_name = null;
                    $product->variant_sku = null;
                    $product->variant_stock = null;
                    $product->variant_price = null;
                    $product->variant_images = null;
                }
            });
            return $producs;
        }
        if ($this->request->shop_id && $this->request->status == 4) {
            $producs = Product::where('shop_id', $this->request->shop_id)->where('status', 4)->select('id','name','slug', 'sku','description','infomation',
            'price','show_price','image','quantity','sold_count',
            'view_count','create_by','update_by',
            'updated_at','category_id','shop_id','status','height',
            'length','weight','width','update_version','is_delete',
            )->get();
            $producs->each(function($product) {
                $variant = product_variants::where('product_id', $product->id)->select('product_id','name', 'sku','stock','price','images')->first();
                if ($variant) {
                    $product->product_id = $variant->product_id;
                    $product->variant_name = $variant->name;
                    $product->variant_sku = $variant->sku;
                    $product->variant_stock = $variant->stock;
                    $product->variant_price = $variant->price;
                    $product->variant_images = $variant->images;
                } else {
                    $product->product_id = null;
                    $product->variant_name = null;
                    $product->variant_sku = null;
                    $product->variant_stock = null;
                    $product->variant_price = null;
                    $product->variant_images = null;
                }
            });
            return $producs;
        }
        if ($this->request->shop_id && $this->request->status == 5) {
            $producs = Product::where('shop_id', $this->request->shop_id)->where('status', 5)->select('id','name','slug', 'sku','description','infomation',
            'price','show_price','image','quantity','sold_count',
            'view_count','create_by','update_by',
            'updated_at','category_id','shop_id','status','height',
            'length','weight','width','update_version','is_delete',
            )->get();
            $producs->each(function($product) {
                $variant = product_variants::where('product_id', $product->id)->select('product_id','name', 'sku','stock','price','images')->first();
                if ($variant) {
                    $product->product_id = $variant->product_id;
                    $product->variant_name = $variant->name;
                    $product->variant_sku = $variant->sku;
                    $product->variant_stock = $variant->stock;
                    $product->variant_price = $variant->price;
                    $product->variant_images = $variant->images;
                } else {
                    $product->product_id = null;
                    $product->variant_name = null;
                    $product->variant_sku = null;
                    $product->variant_stock = null;
                    $product->variant_price = null;
                    $product->variant_images = null;
                }
            });
            return $producs;
        }
        if (isset($this->request->status)) {
            $producs = Product::where('status', $this->request->status)->select('id','name','slug', 'sku','description','infomation',
            'price','show_price','image','quantity','sold_count',
            'view_count','create_by','update_by',
            'updated_at','category_id','shop_id','status','height',
            'length','weight','width','update_version','is_delete',
            )->get();
            $producs->each(function($product) {
                $variant = product_variants::where('product_id', $product->id)->select('product_id','name', 'sku','stock','price','images')->first();
                if ($variant) {
                    $product->product_id = $variant->product_id;
                    $product->variant_name = $variant->name;
                    $product->variant_sku = $variant->sku;
                    $product->variant_stock = $variant->stock;
                    $product->variant_price = $variant->price;
                    $product->variant_images = $variant->images;
                } else {
                    $product->product_id = null;
                    $product->variant_name = null;
                    $product->variant_sku = null;
                    $product->variant_stock = null;
                    $product->variant_price = null;
                    $product->variant_images = null;
                }
            });
            return $producs;
        }
        return 'Lỗi xuất data';
    }
}
