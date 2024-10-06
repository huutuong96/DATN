<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

}
