<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products'; // Thay đổi tên bảng nếu cần

    // Các trường có thể được gán hàng loạt
    protected $fillable = [
        'name',
        'slug',
        'description',
        'infomation',
        'price',
        'sale_price',
        'image',
        'quantity',
        'sold_count',
        'view_count',
        'parent_id',
        'create_by',
        'update_by',
        'create_at',
        'update_at',
        'category_id',
        'brand_id',
        'shop_id',
        'color_id',
        'sku',
        'height',
        'length',
        'weight',
        'width',
        'version',
        'show_price',
        'status',
        'is_delete',
        'admin_note',
        'json_variants',
    ];


    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function colors()
    {
        return $this->hasMany(ColorsModel::class);
    }
    public function variants()
    {
        return $this->hasMany(product_variants::class);
    }
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'variantattribute', 'product_id', 'attribute_id')
                    ->withPivot('value_id', 'shop_id', 'variant_id');
    }
    public function orderDetails()
    {
        return $this->hasMany(OrderDetailsModel::class);
    }
    public function orders()
    {
        return $this->hasManyThrough(OrdersModel::class, OrderDetailsModel::class);
    }
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    public function category()
    {
        return $this->belongsTo(CategoriesModel::class);
    }
    public function brand()
    {
        return $this->belongsTo(BrandsModel::class);
    }

    public function scopeSearch(Builder $query, array $filters)
    {
        if (isset($filters['name']) && !empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%')
            ->orWhere('slug', 'like', '%' . $filters['name'] . '%')
            ->orWhere('description', 'like', '%' . $filters['name'] . '%')
            ->orWhere('infomation', 'like', '%' . $filters['name'] . '%');
        }
        if (isset($filters['sku']) && !empty($filters['sku'])) {
            $query->where('sku', 'like', '%' . $filters['sku'] . '%');
        }
        if (isset($filters['min_price']) && is_numeric($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price']) && is_numeric($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }
        if (isset($filters['min_sold']) && is_numeric($filters['min_sold'])) {
            $query->where('sold_count', '>=', $filters['min_sold']);
        }
        if (isset($filters['max_sold']) && is_numeric($filters['max_sold'])) {
            $query->where('sold_count', '<=', $filters['max_sold']);
        }
        if (isset($filters['min_views']) && is_numeric($filters['min_views'])) {
            $query->where('view_count', '>=', $filters['min_views']);
        }
        if (isset($filters['max_views']) && is_numeric($filters['max_views'])) {
            $query->where('view_count', '<=', $filters['max_views']);
        }
        if (isset($filters['sort_by']) && in_array($filters['sort_by'], ['newest', 'oldest'])) {
            $direction = $filters['sort_by'] === 'newest' ? 'desc' : 'asc';
            $query->orderBy('created_at', $direction);
        }
        if (isset($filters['attributes']) && is_array($filters['attributes'])) {
            $query->whereHas('variants', function ($variantQuery) use ($filters) {
                foreach ($filters['attributes'] as $attributeName => $attributeValue) {
                    $variantQuery->whereHas('attributes', function ($attributeQuery) use ($attributeName, $attributeValue) {
                        $attributeQuery->where('attributes.name', $attributeName)
                            ->whereHas('values', function ($valueQuery) use ($attributeValue) {
                                $valueQuery->where('attributevalue.value', 'like', '%' . $attributeValue . '%');
                            });
                    });
                }
            });
        }
        return $query;
    }


}

