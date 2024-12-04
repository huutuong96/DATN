<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận thay đổi mật khẩu</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 30px auto; background-color: #f9f9f9; padding: 0; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); overflow: hidden;">
    <!-- Header -->
    <header style="background: linear-gradient(90deg, #1369fe, #6d93d3); color: white; padding: 20px; text-align: center;">
        <h1 style="margin: 0; font-size: 24px;">Xác nhận thay đổi mật khẩu</h1>
    </header>

    <!-- Main Content -->
    <main style="padding: 20px; background: #ffffff;">
        <p style="margin-bottom: 20px;">Xin chào <strong>{{ $user->fullname }}</strong>,</p>
        <p style="margin-bottom: 20px;">Bạn đã yêu cầu thay đổi mật khẩu. Vui lòng nhấn nút bên dưới để xác nhận thay đổi mật khẩu:</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('confirm_mail_change_password', ['token' => $token, 'email' => $user->email]) }}"
                style="background-color: #1369fe; color: white; padding: 14px 20px; text-decoration: none; font-size: 16px; font-weight: bold; border-radius: 5px; display: inline-block;">
                Xác nhận thay đổi mật khẩu
            </a>
        </div>
        <p style="margin-bottom: 20px;">Mail này sẽ hết hạn trong <strong>24 giờ</strong>.</p>
        <p style="margin-bottom: 0;">Nếu bạn không thay đổi mật khẩu, vui lòng bỏ qua email này.</p>
    </main>

    <!-- Footer -->
    <footer style="background-color: #f4f4f4; padding: 20px; text-align: center; font-size: 12px; color: #666;">
        <p>&copy; {{ date('Y') }} VN Shop. All rights reserved.</p>
    </footer>
</body>
</html>
