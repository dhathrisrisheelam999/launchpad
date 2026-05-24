<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt #{{ $investment->payment_receipt_no }} — LaunchPad Market</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Cormorant+Garamond:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --w: #FFFFFF;
            --off: #FAFAF8;
            --ink: #111110;
            --ink2: #323230;
            --ink3: #6B6B67;
            --ink4: #A8A8A3;
            --green: #2D5A2E;
            --green5: #F5FAF5;
            --line: #E8E4DC;
            --line2: #D8D3C8;
        }
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: #fdfdfc;
            color: var(--ink);
            padding: 40px 20px;
            font-size: 14px;
            line-height: 1.5;
        }
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid var(--line2);
            border-radius: 8px;
            padding: 48px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            position: relative;
        }
        .print-btn-bar {
            max-width: 800px;
            margin: 0 auto 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-print {
            background: var(--green);
            color: #fff;
            padding: 10px 22px;
            border-radius: 100px;
            font-weight: 700;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 13.5px;
            transition: all .2s;
        }
        .btn-print:hover {
            background: #234624;
        }
        .btn-back {
            color: var(--ink3);
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
        }
        .receipt-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid var(--ink);
            padding-bottom: 24px;
            margin-bottom: 30px;
        }
        .brand-logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--ink);
        }
        .brand-logo span {
            color: var(--green);
        }
        .invoice-details {
            text-align: right;
        }
        .invoice-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .address-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        .address-block h4 {
            font-weight: 800;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: .08em;
            color: var(--ink4);
            margin-bottom: 8px;
        }
        .address-block p {
            color: var(--ink2);
            line-height: 1.6;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        .invoice-table th {
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--ink4);
            font-weight: 700;
            padding: 10px 16px;
            border-bottom: 2px solid var(--line2);
            background: var(--off);
        }
        .invoice-table td {
            padding: 16px;
            border-bottom: 1px solid var(--line);
            vertical-align: top;
        }
        .invoice-table tr.totals-row td {
            border: none;
            padding-top: 10px;
            padding-bottom: 10px;
        }
        .invoice-table tr.grand-total td {
            border-top: 2px solid var(--line2);
            border-bottom: 2px solid var(--line2);
            padding-top: 16px;
            padding-bottom: 16px;
        }
        .payment-meta {
            background: var(--green5);
            border: 1px solid var(--line);
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 40px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            font-size: 13px;
        }
        .meta-item strong {
            color: var(--ink);
        }
        .meta-item span {
            color: var(--ink3);
            display: block;
            margin-bottom: 4px;
        }
        .footer-note {
            text-align: center;
            font-size: 12px;
            color: var(--ink4);
            border-top: 1px solid var(--line);
            padding-top: 24px;
        }
        /* PRINT STYLES */
        @media print {
            body {
                background: #fff;
                padding: 0;
                color: #000;
            }
            .receipt-container {
                border: none;
                box-shadow: none;
                padding: 0;
            }
            .print-btn-bar {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="print-btn-bar">
        <a href="javascript:window.close();" class="btn-back">← Close Tab</a>
        <button onclick="window.print();" class="btn-print">Print Receipt 🖨️</button>
    </div>

    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <div>
                <div class="brand-logo">Launch<span>Pad</span></div>
                <p style="font-size:12px;color:var(--ink3);margin-top:4px">LaunchPad Marketplace, Inc.</p>
            </div>
            <div class="invoice-details">
                <h1 class="invoice-title">RECEIPT</h1>
                <p style="color:var(--ink2)">Receipt No: <strong>{{ $investment->payment_receipt_no }}</strong></p>
                <p style="color:var(--ink3);margin-top:2px">Date: {{ $investment->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        <!-- Address Grid -->
        <div class="address-grid">
            <div class="address-block">
                <h4>PLEDGED BY (INVESTOR)</h4>
                <p>
                    <strong>{{ $investment->user->name }}</strong><br>
                    @if($investment->user->company_name)
                        {{ $investment->user->company_name }}<br>
                    @endif
                    @if($investment->user->phone)
                        {{ $investment->user->phone }}<br>
                    @endif
                    {{ $investment->user->email }}
                </p>
            </div>
            <div class="address-block">
                <h4>PLEDGED TO (STARTUP)</h4>
                <p>
                    <strong>{{ $investment->startup->name }}</strong><br>
                    Founder: {{ $investment->startup->founder->name ?? 'Founder' }}<br>
                    Category: {{ $investment->startup->category->name ?? 'Uncategorized' }}<br>
                    Email: {{ $investment->startup->founder->email ?? 'N/A' }}
                </p>
            </div>
        </div>

        <!-- Itemized Table -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th style="text-align:right">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Investment Capital Pledge</strong><br>
                        <span style="font-size:12px;color:var(--ink3)">Simulated capital subscription pledge to back shares in {{ $investment->startup->name }}</span>
                    </td>
                    <td style="text-align:right;font-weight:700">${{ number_format($investment->amount) }}</td>
                </tr>
                <tr class="totals-row">
                    <td style="text-align:right;color:var(--ink3)">Capital Pledge Subtotal</td>
                    <td style="text-align:right;font-weight:600">${{ number_format($investment->amount) }}</td>
                </tr>
                <tr class="totals-row">
                    @php $fee = $investment->amount * 0.01; @endphp
                    <td style="text-align:right;color:var(--ink3)">Platform Administration Fee (1%)</td>
                    <td style="text-align:right;font-weight:600">${{ number_format($fee) }}</td>
                </tr>
                <tr class="grand-total">
                    <td style="text-align:right;font-weight:700">Total Transaction Amount</td>
                    <td style="text-align:right;font-weight:800;font-size:18px;color:var(--green)">${{ number_format($investment->amount + $fee) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Payment Meta -->
        <div class="payment-meta">
            <div class="meta-item">
                <span>SIMULATED METHOD</span>
                <strong>{{ strtoupper($investment->payment_method) }} Gateway</strong>
            </div>
            <div class="meta-item">
                <span>TRANSACTION REFERENCE</span>
                <strong style="word-break:break-all">{{ $investment->transaction_id }}</strong>
            </div>
            <div class="meta-item">
                <span>COMPLIANCE STATUS</span>
                @if($investment->status === 'approved')
                    <strong style="color:var(--green)">✅ APPROVED BY ADMIN</strong>
                @elseif($investment->status === 'pending')
                    <strong style="color:#8A6914">⏳ PENDING KYC/REGULATORY CHECKS</strong>
                @else
                    <strong style="color:#C0392B">❌ REJECTED</strong>
                @endif
            </div>
            <div class="meta-item">
                <span>SYSTEM STAMP</span>
                <strong>Demo Environment Audit Passed</strong>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-note">
            <p>This is a simulated investment invoice generated for academic assessment and system demonstration.</p>
            <p style="margin-top:6px;font-size:11px">Thank you for testing LaunchPad Market.</p>
        </div>
    </div>

</body>
</html>
