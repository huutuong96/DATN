<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo: Đơn hàng đã bị hủy</title>
</head>
<body>
    <h1>Đơn hàng của bạn đã bị hủy</h1>
    <p>Đơn hàng #{{ $order->id }} đã bị hủy vì cửa hàng không xác nhận đơn hàng trong thời gian quy định.</p>
    
    <h2>Chi tiết đơn hàng:</h2>
    <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Tổng cộng</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderDetails as $detail)
                <tr>
                    <td>{{ $detail->product->name }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td>{{ number_format($detail->price, 0, ',', '.') }} VNĐ</td>
                    <td>{{ number_format($detail->quantity * $detail->price, 0, ',', '.') }} VNĐ</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <p><strong>Tổng tiền:</strong> {{ number_format($order->total_price, 0, ',', '.') }} VNĐ</p>
    <p>Số tiền sẽ được hoàn lại trong vòng 5 ngày làm việc.</p>
    
    <p>Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!</p>
</body>
</html>

