<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Inter, Arial, sans-serif; background: #F4F1EB; padding: 40px 20px; }
        .wrapper { max-width: 580px; margin: 0 auto; }
        .header { background: linear-gradient(135deg, #111110, #1E3A1E); border-radius: 16px 16px 0 0; padding: 36px; text-align: center; }
        .logo { font-family: Georgia, serif; font-size: 26px; font-weight: 700; color: #fff; margin-bottom: 6px; }
        .logo span { color: #BDD9BE; }
        .header p { color: #6B6B67; font-size: 13px; }
        .body { background: #fff; padding: 40px; }
        .greeting { font-family: Georgia, serif; font-size: 26px; font-weight: 700; color: #111; margin-bottom: 14px; }
        .text { font-size: 15px; color: #5A5A55; line-height: 1.75; margin-bottom: 20px; }
        .btn-wrap { text-align: center; margin: 32px 0; }
        .btn { display: inline-block; background: #2D5A2E; color: #fff; padding: 14px 36px; border-radius: 100px; text-decoration: none; font-size: 15px; font-weight: 700; letter-spacing: .02em; }
        .divider { height: 1px; background: #E8E4DC; margin: 28px 0; }
        .small { font-size: 12px; color: #9A9A92; line-height: 1.65; }
        .url-box { background: #F4F1EB; border-radius: 8px; padding: 12px 16px; font-size: 12px; color: #6B6B67; word-break: break-all; margin-top: 10px; font-family: monospace; }
        .warning { background: #FDF8ED; border: 1px solid #F5E6C0; border-radius: 10px; padding: 14px 18px; font-size: 13px; color: #8A6914; margin: 20px 0; }
        .footer { background: #111110; border-radius: 0 0 16px 16px; padding: 24px; text-align: center; }
        .footer p { font-size: 12px; color: #4A4A45; line-height: 1.7; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <div class="logo">Launch<span>Pad</span> Market</div>
        <p>Startup Marketplace Platform</p>
    </div>

    <div class="body">
        <div class="greeting">Reset Your Password 🔐</div>

        <p class="text">Hi <strong>{{ $user->name }}</strong>,</p>

        <p class="text">
            We received a request to reset the password for your LaunchPad Market account
            associated with <strong>{{ $user->email }}</strong>.
        </p>

        <p class="text">
            Click the button below to reset your password. This link will expire in
            <strong>60 minutes</strong> for security.
        </p>

        <div class="btn-wrap">
            <a href="{{ $resetUrl }}" class="btn">Reset My Password →</a>
        </div>

        <div class="warning">
            ⚠️ <strong>Didn't request this?</strong> If you did not request a password reset,
            you can safely ignore this email. Your password will remain unchanged.
        </div>

        <div class="divider"></div>

        <div class="small">
            If the button above doesn't work, copy and paste this link into your browser:
            <div class="url-box">{{ $resetUrl }}</div>
        </div>
    </div>

    <div class="footer">
        <p>
            © {{ date('Y') }} LaunchPad Market. All rights reserved.<br>
            This email was sent because a password reset was requested for {{ $user->email }}
        </p>
    </div>
</div>
</body>
</html>
