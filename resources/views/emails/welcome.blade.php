<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <style>
        body { font-family: Inter, sans-serif; background: #F4F1EB; padding: 40px 20px; }
        .card { background: #fff; border-radius: 16px; padding: 40px; max-width: 560px; margin: 0 auto; }
        h1 { font-size: 28px; color: #111110; margin-bottom: 8px; }
        p  { color: #5A5A55; line-height: 1.7; }
        .btn { display:inline-block; background:#2D5A2E; color:#fff; padding:12px 28px;
               border-radius:100px; text-decoration:none; font-weight:700; margin-top:24px; }
        .footer { text-align:center; font-size:12px; color:#A8A8A3; margin-top:32px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🚀 Welcome to LaunchPad, {{ $user->name }}!</h1>
        <p>Your account has been created successfully as a <strong>{{ $user->role }}</strong>.</p>
        <p>You can now list your startup, connect with investors, and track inquiries from your dashboard.</p>
        <a href="{{ url('/dashboard') }}" class="btn">Go to Dashboard →</a>
        <div class="footer">
            LaunchPad Market · Built with Laravel 11<br>
            You received this because you registered at launchpadmarket.in
        </div>
    </div>
</body>
</html>
