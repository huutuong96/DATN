<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Xác nhận đăng ký tài khoản</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <header style="background-color: #f4f4f4; padding: 20px; text-align: center;">
        <h1 style="color: #444; margin: 0;">Chào mừng bạn đến với VN Shop!</h1>
    </header>
    <main style="padding: 20px;">
        <p>Xin chào {{ $user->fullname }},</p>
        <p>Cảm ơn bạn đã đăng ký tài khoản. Vui lòng nhấn nút bên dưới để xác nhận đăng ký tài khoản:</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="#" style="background-color: #4CAF50; color: white; padding: 14px 20px; text-align: center; text-decoration: none; display: inline-block; border-radius: 4px; font-size: 16px;">Xác nhận đăng ký tài khoản</a>
        </div>
        <p>Nếu nút không hoạt động, bạn cũng có thể sao chép và dán đường dẫn sau vào trình duyệt của bạn:</p>
        <p style="word-break: break-all;">#</p>
        <p>Đường dẫn này sẽ hết hạn trong 24 giờ.</p>
        <p>Nếu bạn không tạo tài khoản, vui lòng bỏ qua email này.</p>
    </main>
    <footer style="background-color: #f4f4f4; padding: 20px; text-align: center; font-size: 12px;">
        <p>&copy; {{ date('Y') }} VN Shop. All rights reserved.</p>
    </footer>
</body>
</html>
