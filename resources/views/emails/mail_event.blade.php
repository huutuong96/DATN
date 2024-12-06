<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .header {
            background: linear-gradient(90deg, #1369fe, #6d93d3);
            color: white;
            text-align: center;
            padding: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
            line-height: 1.6;
            min-height: 300px;
        }
        .content h2 {
            color: #1369fe;
            font-size: 20px;
            margin-bottom: 10px;
        }
        .content p {
            margin: 10px 0;
        }
        .cta-button {
            display: inline-block;
            padding: 12px 20px;
            background: #1369fe;
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            margin-top: 20px;
        }
        .cta-button:hover {
            background: #feb47b;
        }
        .footer {
            background: #f4f4f4;
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #666;
        }
        .anh1{
            position: absolute;
            top: -198px;
            right: -179px;
        }
        .anh2{
            position: absolute;
            top: 65px;
            right: -30px;
        }
    </style>
</head>
<body>
    <div class="email-container" >
        <!-- Header Section -->
        <div class="header">
            <h1>{{$eventTitle ?? null}}</h1>
              <!-- <h1>chào mừng xuân 2025</h1> -->
        </div>

        <!-- Content Section -->
        <div class="content">
            <h2>Dear {{$user->fullname ?? "Bạn"}}</h2>
            <p>
                MÃ VOUCHER : {{$code}}
            </p>
            <img class="anh1" src="../../../public/assets/images/1.png" alt="">
            <!-- <img src="../assets/images/1.png" alt=""> -->
            <img class="anh2" src="../../../public/assets/images/2.png" alt="" width="400px">
            <a href="#" class="cta-button">Xem ngay</a>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            &copy; 2024 Your Company Name. All rights reserved. | <a href="#">Unsubscribe</a>
        </div>
    </div>
</body>
</html>
