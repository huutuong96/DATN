<?php

namespace App\Http\Controllers;

use App\Models\CommentsModel;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Predis\Response\Status;

class SearchController extends Controller
{
    public function searchShop(Request $rqt)
    {
        $columns = ["shop_name", "slug", "description"];
        
        $search = $rqt->input('search');
        $resultsByTable = [];

        foreach ($columns as $column) {
            $results = DB::table('shops')
                ->where($column, 'like', "%$search%")
                ->paginate(6);
            
            if (!$results->isEmpty()) {
                $resultsByTable[$column] = $results;
            }
        }

        return response()->json($resultsByTable);
    }



    public function search(Request $rqt)
    {
        $limit_shops = $rqt->limit_shop ?? 1;
        $limit_product = $rqt->limit_product ?? 6;

        $db = [
            "products" => ["name", "sku", "slug", "description"],
            "shops" => ["shop_name", "slug", "description"]
        ];

        $search = $rqt->input('search');
        $perPage = $rqt->input('per_page', 10); // Số lượng bản ghi mỗi trang, mặc định là 10
        $resultsByTable = [];

        foreach ($db as $table => $columns) {
            $tableResults = collect();

            foreach ($columns as $column) {
                $query = DB::table($table)
                    ->where($column, 'like', "%$search%");
                    
                if ($table == 'products') {
                    $results = $query->paginate($limit_product);
                    $resultsByTable[$table] = $results;
                    break; // Dừng lại sau khi phân trang bảng 'products'
                }
                // Phân trang riêng cho bảng 'shops'
                if ($table == 'shops') {
                    $results = $query->paginate($limit_shops);
                    $resultsByTable[$table] = $results;
                    break; // Dừng lại sau khi phân trang bảng 'shops'
                }
            }
        }

        // Trả về kết quả đã được phân trang theo từng bảng
        return response()->json($resultsByTable);
    }



    public function searchClient(Request $request)
    {
        
        $limit = $request->limit ?? 20;
        $query = Product::query();
            if ($request->has('min_price') && $request->has('max_price')) {
                $query->whereBetween(DB::raw('CASE WHEN show_price LIKE "% - %" THEN CAST(SUBSTRING_INDEX(show_price, " - ", 1) AS UNSIGNED) ELSE CAST(show_price AS UNSIGNED) END'), [$request->min_price, $request->max_price]);
            }
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }
            if ($request->has('updated_at')) {
                $query->orderby('updated_at', 'desc');
            }
            if ($request->has('sold_count')) {
                $query->orderby('sold_count', 'desc');
            }
            if ($request->has('view_count')) {
                $query->orderby('view_count', 'desc');
            }
            if ($request->has('shop_id')) {
                $query->where('shop_id', $request->shop_id);
            }
            if ($request->sort == 'price') {
                $query->orderByRaw('CASE WHEN show_price LIKE "% - %" THEN CAST(SUBSTRING_INDEX(show_price, " - ", 1) AS UNSIGNED) ELSE CAST(show_price AS UNSIGNED) END ASC');
            }
            if ($request->sort == '-price') {
                $query->orderByRaw('CASE WHEN show_price LIKE "% - %" THEN CAST(SUBSTRING_INDEX(show_price, " - ", 1) AS UNSIGNED) ELSE CAST(show_price AS UNSIGNED) END DESC');
            }
            if ($request->sort == 'updated_at') {
                $query->orderby('updated_at', 'asc');
            }
            if ($request->sort == '-updated_at') {
                $query->orderby('updated_at', 'desc');
            }
            if ($request->sort == 'sold_count') {
                $query->orderby('sold_count', 'asc');
            }
            if ($request->sort == '-sold_count') {
                $query->orderby('sold_count', 'desc');
            }
            if ($request->sort == 'view_count') {
                $query->orderby('view_count', 'asc');
            }
            if ($request->sort == '-view_count') {
                $query->orderby('view_count', 'desc');
            }
            if ($request->has('min_rate') && $request->has('max_rate')) {
                $query->whereHas('comments', function ($q) use ($request) {
                    $q->havingRaw('AVG(rate) BETWEEN ? AND ?', [$request->min_rate, $request->max_rate])
                      ->whereNotNull('rate');
                });
            }
            $products = $query->where('status', 2)->paginate($limit);
            foreach ($products as $product) {
                $product->rateAvg = rateAvg($product->id);
            }
            return response()->json([
                'status' => 200,
                'message' => 'Lấy dữ liệu thành công',
                'data' => $products
            ]);
        
    }

}
