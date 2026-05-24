<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <style>
        body { font-family: Inter, sans-serif; background: #F4F1EB; padding: 40px 20px; }
        .card { background: #fff; border-radius: 16px; padding: 40px; max-width: 560px; margin: 0 auto; }
        h1 { font-size: 24px; color: #111110; margin-bottom: 8px; }
        p  { color: #5A5A55; line-height: 1.7; }
        .meta { background: #F4F1EB; border-radius: 12px; padding: 18px; margin: 20px 0; }
        .meta-row { display:flex; justify-content:space-between; margin-bottom:8px; font-size:14px; }
        .meta-row:last-child { margin-bottom:0; }
        .meta-label { color:#A8A8A3; font-weight:600; }
        .meta-val   { color:#111110; font-weight:600; }
        .message-box { border-left:3px solid #2D5A2E; padding:12px 18px; background:#EBF4EB;
                        border-radius:0 8px 8px 0; margin:20px 0; font-style:italic; color:#323230; }
        .btn { display:inline-block; background:#2D5A2E; color:#fff; padding:12px 28px;
               border-radius:100px; text-decoration:none; font-weight:700; margin-top:24px; }
        .footer { text-align:center; font-size:12px; color:#A8A8A3; margin-top:32px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>📩 New Investor Inquiry for {{ $startup->name }}</h1>
        <p>You have received a new inquiry from an investor on LaunchPad Market.</p>

        <div class="meta">
            <div class="meta-row">
                <span class="meta-label">Investor</span>
                <span class="meta-val">{{ $inquiry->investor_name }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Organisation</span>
                <span class="meta-val">{{ $inquiry->organisation }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Interest</span>
                <span class="meta-val">{{ ucfirst($inquiry->interest_type) }}</span>
            </div>
        </div>

        <p><strong>Their message:</strong></p>
        <div class="message-box">{{ $inquiry->message }}</div>

        <a href="{{ url('/dashboard/inquiries') }}" class="btn">View in Dashboard →</a>

        <div class="footer">
            LaunchPad Market · This email was sent because someone inquired about {{ $startup->name }}
        </div>
    </div>
</body>
</html>
