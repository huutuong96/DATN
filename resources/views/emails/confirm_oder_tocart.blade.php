<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Xác nhận đặt hàng thành công</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9;">
    <header style="background-color: #4CAF50; padding: 20px; text-align: center; border-radius: 5px 5px 0 0;">
        <h1 style="color: #ffffff; margin: 0; font-size: 24px;">Cảm ơn bạn đã đồng hành cùng VN Shop!</h1>
    </header>
    <main style="padding: 20px; background-color: #ffffff; border-radius: 0 0 5px 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <p style="font-size: 16px; margin-bottom: 15px;">Đơn hàng của bạn đã được đặt thành công.</p>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
            <tr>
                <th style="text-align: left; padding: 8px; border-bottom: 1px solid #ddd;">Mã đơn hàng</th>
                <th style="text-align: left; padding: 8px; border-bottom: 1px solid #ddd;">Sản phẩm</th>
                <th style="text-align: left; padding: 8px; border-bottom: 1px solid #ddd;">Số lượng</th>
                <th style="text-align: left; padding: 8px; border-bottom: 1px solid #ddd;">Đơn giá</th>
            </tr>
            @foreach ($allOrders as $index => $order)
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><span style="color: #4CAF50;">{{ $order->code }}</span></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><span style="color: #4CAF50;">{{ $allProduct[$index]->name }}</span></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><span style="color: #4CAF50;">{{ $allQuantity[$index] }}</span></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><span style="color: #4CAF50;">{{ number_format($allOrderDetails[$index]->subtotal) }} VNĐ</span></td>
                </tr>
            @endforeach
        </table>
        <p style="font-size: 14px; margin-bottom: 10px;"><strong>Tổng tiền:</strong> <span style="color: #e53935;">{{ number_format($grandTotalPrice) }} VNĐ</span></p>
        <p style="font-size: 14px; margin-bottom: 10px;"><strong>Ngày đặt hàng:</strong> {{ $allOrders[0]->created_at->format('d/m/Y H:i:s') }}</p>
    </main>
    <footer style="background-color: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; margin-top: 20px; border-radius: 5px;">
        <p style="margin: 0;">&copy; {{ date('Y') }} VN Shop. All rights reserved.</p>
    </footer>
</body>
</html>
