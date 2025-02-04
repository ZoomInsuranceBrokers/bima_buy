<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP for Password Reset</title>
    <style>
        /* Global Styles */
        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            color: #333;
            line-height: 1.6;
        }

        /* Ensuring full height container */
        .email-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            padding: 20px;
        }

        .email-container {
            width: 100%;
            max-width: 600px;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin: 0 auto;
        }

        .email-header {
            margin-bottom: 20px;
        }

        .email-header h1 {
            font-size: 24px;
            color: #333;
            margin: 0;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .email-body {
            margin-bottom: 30px;
            font-size: 16px;
            color: #555;
        }

        .otp-link {
            display: inline-block;
            padding: 12px 30px;
            margin-top: 20px;
            background-color: #6C5CE7;
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .otp-link:hover {
            background-color: #5a4bd7;
        }

        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #aaa;
        }

        .footer p {
            margin: 0;
        }

        .footer a {
            color: #6C5CE7;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

    </style>
</head>

<body>
    <!-- Flexbox Wrapper to Vertically and Horizontally Center the Email Container -->
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <h1>OTP for Password Reset</h1>
            </div>

            <!-- Body -->
            <div class="email-body">
                <p>Hello,</p>
                <p>We received a request to reset your password. To proceed, please enter the following One-Time Password (OTP):</p>
                <h2>{{ $otp }}</h2>
                <p>This OTP is valid for the next 10 minutes. Please make sure to enter it soon.</p>
                <p>If you did not request a password reset, please ignore this email.</p>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>&copy; {{ date('Y') }} Your Company Name. All rights reserved.</p>
                <p><a href="#">Unsubscribe</a> | <a href="#">Contact Support</a></p>
            </div>
        </div>
    </div>
</body>

</html>
