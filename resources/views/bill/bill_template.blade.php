<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bills</title>
    <style>
        body {
            font-family: 'DejaVuSans', sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            justify-content: center;
            align-items: flex-start;

        }
        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 100%;
        }
        h1 {
            text-align: center;
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #f9f9f9;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 1rem;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        /* .border-top {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
        } */
        .order-summary {
            text-align: right;
            margin-top: 20px;
        }
        .order-summary p {
            margin: 5px 0;
            font-size: 1rem;
        }
        .order-summary span {
            font-size: 1.1rem;
        }
        .order-summary .total {
            color: red;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .order-summary .fs-6, .order-summary .fs-md-5, .order-summary .fs-lg-4 {
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Danh sách hóa đơn</h1>
        @foreach($orders as $order)
        <table>
            <tr>
                <td><strong>Mã đơn hàng:</strong> {{ $order->id }}</td>
                <td><strong>Khách hàng:</strong> {{ $order->to_name }} - {{ $order->to_phone }}</td>
                <td><strong>Địa chỉ:</strong> {{ $order->to_address }}</td>
                <td><strong>Ngày tạo:</strong> {{ $order->created_at }}</td>
            </tr>
            <tr>
                <td colspan="4">
                    <table width="100%">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Đơn giá</th>
                                <th>Tổng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderDetails as $detail)
                            <tr>
                                <td>
                                    {{ $detail['product']->name }} <br>
                                    @if($detail->variant_id != null) 
                                        <br><small>sản phẩm: {{ $detail->variant->name }}</small>
                                    @endif
                                </td>
                                <td>{{ $detail->quantity }}</td>
                                <td>{{ number_format($detail->subtotal) }} VND</td>
                                <td>{{ number_format($detail->subtotal * $detail->quantity) }} VND</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        <div class="border-top">
            <div class="order-summary">
                <p>Tổng tiền hàng: <span>{{ number_format($subTotal ?? $order->total_amount) }} đ</span></p>
                <p>VAT: <span>{{ number_format($order->vat ?? 0) }} đ</span></p>
                <p>Phí vận chuyển: <span>{{ number_format($order->ship_fee ?? 0) }} đ</span></p>
                <p>Giảm giá thành viên: <span>{{ number_format($order->disscount_by_rank ?? 0) }} đ</span></p>
                <p>Mã giảm giá cửa hàng: <span>{{ number_format($order->voucher_shop_disscount ?? 0) }} đ</span></p>
                <p>Mã giảm giá VNSHOP: <span>{{ number_format($order->voucher_main_disscount ?? 0) }} đ</span></p>
                <p><strong>Tổng tiền:</strong> <span class="total">{{ number_format($order->total_amount ?? 0) }} đ</span></p>
            </div>
        </div>
        @endforeach
    </div>
</body>
</html>
