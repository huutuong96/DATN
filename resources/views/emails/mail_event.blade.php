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
        }
        .header {
            background: linear-gradient(90deg, #ff7e5f, #feb47b);
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
        }
        .content h2 {
            color: #ff7e5f;
            font-size: 20px;
            margin-bottom: 10px;
        }
        .content p {
            margin: 10px 0;
        }
        .cta-button {
            display: inline-block;
            padding: 12px 20px;
            background: #ff7e5f;
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
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header Section -->
        <div class="header">
            <h1>{{$eventTitle ?? null}}</h1>
        </div>

        <!-- Content Section -->
        <div class="content">
            <h2>Dear [đổ dữ liệu sau],</h2>
            <p>
                We are thrilled to welcome you to our grand celebration! This momentous occasion
                wouldn't be the same without your presence.
            </p>
            <p>
                Join us as we commemorate this incredible milestone with exciting activities, special
                announcements, and much more.
            </p>
            <a href="#" class="cta-button">Discover More</a>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            &copy; 2024 Your Company Name. All rights reserved. | <a href="#">Unsubscribe</a>
        </div>
    </div>
</body>
</html>
