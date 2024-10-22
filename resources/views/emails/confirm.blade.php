<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Xác nhận đăng ký tài khoản</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 500px;
            margin: 0 auto;
            padding: 0;
            background-color: #f9f9f9;
        }
        header {
            background-color: #0E6AFF;
            padding: 30px 20px;
            text-align: center;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        header h1 {
            color: white;
            margin: 0;
            font-size: 24px;
        }
        main {
            padding: 30px 20px;
            background-color: white;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        main p {
            margin: 10px 0;
            font-size: 16px;
        }
        .confirmation-button {
            text-align: center;
            margin: 30px 0;
        }
        .confirmation-button a {
            background-color: #4CAF50;
            color: white;
            padding: 14px 25px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .confirmation-button a:hover {
            background-color: #388e3c;
        }
        footer {
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            margin-top: 30px;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }
        footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <header>
        <h1>Chào mừng bạn đến với VN Shop!</h1>
    </header>
    <main>
        <p>Xin chào <b>{{ $user->fullname }}</b>,</p>
        <p>Cảm ơn bạn đã đăng ký tài khoản để truy cập và sử dụng các dịch vụ của website chúng tôi. Vui lòng nhấn nút bên dưới để xác nhận đăng ký tài khoản của bạn:</p>
        <div class="confirmation-button">
            <a href="http://localhost:3000/auth/verify_email?token={{ $token }}">Xác nhận đăng ký tài khoản</a>

        </div>
        <p>Mail này sẽ hết hạn trong 24 giờ.</p>
        <p>Nếu bạn không tạo tài khoản, vui lòng bỏ qua email này.</p>
    </main>
    <footer>
        <p>&copy; {{ date('Y') }} VN Shop. All rights reserved.</p>
    </footer>
</body>
</html>
