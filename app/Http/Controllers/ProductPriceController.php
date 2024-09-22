<?php
// app/Http/Controllers/ProductPriceController.php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductPrice;
use App\Models\PriceHistory;
use Illuminate\Http\Request;

class ProductPriceController extends Controller
{
    public function updatePrice(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_variant' => 'required|boolean',
        ]);

        if ($request->is_variant) {
            $item = ProductVariant::findOrFail($id);
        } else {
            $item = Product::findOrFail($id);
        }

        // Deactivate current active price
        $item->prices()->where('is_active', true)->update(['is_active' => false]);

        // Create new price
        $newPrice = $item->prices()->create([
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => true,
        ]);

        return response()->json(['message' => 'Cập nhật giá thành công', 'price' => $newPrice]);
    }

    public function getPriceHistory($id, Request $request)
    {
        $request->validate([
            'is_variant' => 'required|boolean',
        ]);

        if ($request->is_variant) {
            $item = ProductVariant::findOrFail($id);
        } else {
            $item = Product::findOrFail($id);
        }

        $priceHistory = $item->priceHistories()->orderBy('changed_at', 'desc')->get();
        return response()->json($priceHistory);
    }

    public function getCurrentPrice($id, Request $request)
    {
        $request->validate([
            'is_variant' => 'required|boolean',
        ]);

        if ($request->is_variant) {
            $item = ProductVariant::findOrFail($id);
        } else {
            $item = Product::findOrFail($id);
        }

        $currentPrice = $item->prices()
            ->where('start_date', '<=', now())
            ->where(function ($query) {
                $query->where('end_date', '>=', now())
                      ->orWhereNull('end_date');
            })
            ->where('is_active', true)
            ->orderBy('start_date', 'desc')
            ->first();

        return response()->json($currentPrice);
    }
}
