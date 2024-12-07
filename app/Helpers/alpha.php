<?php
use Exception;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

if (!function_exists('log_host')) {
    function testFunctions()
    {
        $controller = self::class; // Lấy tên controller hiện tại
        $methods = get_class_methods($controller); // Lấy tất cả các method của controller
        $excludedMethods = ['__construct', 'testFunctions']; // Loại trừ những method không cần test
        $results = [];

        foreach ($methods as $method) {
            if (!in_array($method, $excludedMethods)) {
                try {
                    // Gọi function với dữ liệu giả lập
                    $response = app()->call([$this, $method], [
                        'request' => new Request() // Tạo Request giả lập
                    ]);

                    // Ghi nhận kết quả thành công
                    $results[$method] = 'Success';
                } catch (Exception $e) {
                    // Gửi lỗi qua Telegram
                    log_debug("Function $method failed with error: " . $e->getMessage(), [
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]);

                    // Ghi nhận kết quả lỗi
                    $results[$method] = 'Failed: ' . $e->getMessage();
                }
            }
        }

        return response()->json($results);
    }
}
