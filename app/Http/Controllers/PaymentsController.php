<?php

namespace App\Http\Controllers;

use App\Models\OrdersModel;
use Illuminate\Http\Request;
use App\Models\PaymentsModel;
use App\Http\Requests\PaymentRequest;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $payments = PaymentsModel::all();

        if ($payments->isEmpty()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Không tồn tại Payment nào",
                ]
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công',
            'data' => $payments
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentRequest $request)
    {

        $dataInsert = [
            "name" => $request->name,
            "description" => $request->description,
            "status" => $request->status,
        ];

        try {
            $payments = PaymentsModel::create($dataInsert);
            $dataDone = [
                'status' => true,
                'message' => "Thêm Payment thành công",
                'data' => $payments
            ];
            return response()->json($dataDone, 200);
        } catch (\Throwable $th) {
            $dataDone = [
                'status' => false,
                'message' => "Thêm Payment không thành công",
                'error' => $th->getMessage()
            ];
            return response()->json($dataDone);
        }


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $payments = PaymentsModel::find($id);

        if (is_null($payments)) {
            return response()->json([
                'status' => false,
                'message' => "Payment không tồn tại"
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $payments
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PaymentRequest $request, string $id)
    {

        $payments = PaymentsModel::find($id);

        if (!$payments) {
            return response()->json([
                'status' => false,
                'message' => "Payment không tồn tại"
            ], 404);
        }

        $dataUpdate = [
            "name" => $request->name ?? $payments->name,
            "description" => $request->description ?? $payments->description,
            "status" => $request->status ?? $payments->status,
        ];

        try {
            $payments->update($dataUpdate);
            return response()->json(
                [
                    'status' => true,
                    'message' => "Payment đã được cập nhật",
                    'data' => $payments
                ],
                200
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Cập nhật Payment không thành công",
                    'error' => $th->getMessage()
                ]
            );
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $paymens = PaymentsModel::find($id);

        try {
            if (!$paymens) {
                return response()->json([
                    'status' => false,
                    'message' => "Paymen không tồn tại"
                ], 404);
            }

            $paymens->delete();

            return response()->json([
                'status' => true,
                'message' => "Paymen đã được xóa"
            ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa Paymen không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
    // THANH TOÁN KHI NHẬN
    public function cod_payment(Request $request)
    {
        $orders = OrdersModel::where('group_order_id', $request->group_order_id)->where('status', 1)->get();
        if ($orders) {
            foreach ($orders as $order) {
                $order->status = 2;
                $order->save();
            }
            return response()->json([
                'Order' => $orders,
                'message' => 'Mua hàng thành công'
            ]);
        }
    }
    // THANH TOÁN BẰNG CỔNG THANH TOÁN

    // Hàm này được tạo ra để cho VNPay có chỗ để return về, sẽ được thay thế bằng hàm view trang Checkout SuccessFul
    public function checkoutdone(Request $request){
        $this->vnpay_return($request);
        echo "<< Giao diện checkout thành công >>";
    }
    public function vnpay_payment(Request $request, $total_amount, $groupOrderIds)
    {
        $grandTotalPrice = 0;
        $orders = OrdersModel::where('group_order_id', $groupOrderIds)->where('status', 1)->get();
        if ($orders->isEmpty()) {
            return response()->json([
                'status' => 'false',
                'message' => 'Không tìm thấy đơn hàng.'
            ]);
        }
        foreach($orders as $order){
            $grandTotalPrice += $order->total_amount;
        }

        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://127.0.0.1:8000/api/checkoutdone"; // Đổi đường dẫn này thành đường dẫn đến trang Checkout SuccessFul
        $vnp_TmnCode = "TIGDFWL4"; //Mã website tại VNPAY
        $vnp_HashSecret = "W09DJQ9Y0K214BWC48SNRZR7UWVE8OPT"; //Chuỗi bí mật

        $vnp_TxnRef = $order->group_order_id;

        $vnp_OrderInfo = 'Thanh toán đơn hàng';
        $vnp_OrderType = 'Bill';

        $vnp_Amount = $grandTotalPrice * 100;

        $vnp_Locale = 'vn';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret); //
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        if (isset($_POST['vnpay_payment'])) {

            echo $vnp_Url;
            // header('Location: ' . $vnp_Url);
            die();
        }
    }

    public function vnpay_return(Request $request)
    {
        $vnp_HashSecret = "W09DJQ9Y0K214BWC48SNRZR7UWVE8OPT"; // Chuỗi bí mật

        // Lấy các tham số trả về từ VNPAY
        $vnp_TxnRef = $request->input('vnp_TxnRef'); // Mã đơn hàng
        $vnp_ResponseCode = $request->input('vnp_ResponseCode'); // Mã phản hồi từ VNPAY
        $vnp_SecureHash = $request->input('vnp_SecureHash'); // Mã bảo mật

        // Kiểm tra checksum (bảo mật dữ liệu trả về)
        $inputData = $request->all();
        unset($inputData['vnp_SecureHash']); // Xóa mã bảo mật khỏi dữ liệu để tính checksum

        ksort($inputData); // Sắp xếp lại các tham số theo thứ tự bảng chữ cái
        $hashData = "";
        foreach ($inputData as $key => $value) {
            $hashData .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $hashData = rtrim($hashData, '&');

        // Tạo mã bảo mật để kiểm tra tính toàn vẹn dữ liệu
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // So sánh mã bảo mật trả về từ VNPAY với mã bảo mật tự tính toán
        if ($secureHash === $vnp_SecureHash) {
            // Nếu mã bảo mật hợp lệ
            if ($vnp_ResponseCode == '00') {
                // Thanh toán thành công
                // Tìm đơn hàng theo mã đơn hàng (vnp_TxnRef)
                $orders = OrdersModel::where('group_order_id', $vnp_TxnRef)->where('status', 1)->get();
                if ($orders) {
                    foreach($orders as $order){
                        $order->status = 2;
                        $order->save();
                    }
                    return response()->json([
                        'code' => '00',
                        'message' => 'Đơn hàng của bạn đã được thanh toán thành công'
                    ]);
                } else {
                    return response()->json([
                        'code' => '404',
                        'message' => 'Không tìm thấy đơn hàng.'
                    ]);
                }
            } else {
                // Thanh toán không thành công
                return response()->json([
                    'code' => $vnp_ResponseCode,
                    'message' => 'Thanh toán không thành công.'
                ]);
            }
        } else {
            // Mã bảo mật không hợp lệ
            return response()->json([
                'code' => '97',
                'message' => 'Chữ ký không hợp lệ.'
            ]);
        }
    }
}
