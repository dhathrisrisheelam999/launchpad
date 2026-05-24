@extends('layouts.app')

@section('title', 'Investment Pledged Successfully — LaunchPad Market')

@section('content')
<style>
    .success-container {
        max-width: 600px;
        margin: 80px auto;
        padding: 0 20px;
        text-align: center;
    }
    .success-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--r4);
        padding: 48px;
        box-shadow: var(--sh2);
    }
    .success-badge {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: var(--green5);
        color: var(--green);
        border: 2px solid var(--green3);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        margin: 0 auto 24px;
        box-shadow: 0 6px 20px rgba(45,90,46,0.1);
    }
    .success-title {
        font-family: 'Cormorant Garamond', serif;
        font-size: 36px;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 12px;
    }
    .success-desc {
        font-size: 15px;
        color: var(--ink3);
        line-height: 1.6;
        margin-bottom: 32px;
    }
    .txn-details {
        background: var(--off);
        border: 1px solid var(--line);
        border-radius: var(--r2);
        padding: 24px;
        margin-bottom: 36px;
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 12px;
        font-size: 13.5px;
    }
    .txn-row {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px dashed var(--line2);
        padding-bottom: 8px;
    }
    .txn-row:last-child {
        border: none;
        padding-bottom: 0;
    }
    .txn-label {
        color: var(--ink3);
    }
    .txn-value {
        font-weight: 700;
        color: var(--ink);
    }
    .success-btns {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
</style>

<div class="success-container">
    <div class="success-card">
        <div class="success-badge">✔</div>
        <h1 class="success-title">Investment Placed!</h1>
        <p class="success-desc">
            Your investment pledge in <strong>{{ $investment->startup->name }}</strong> has been successfully registered. A simulated charge of <strong>{{ $investment->formatted_amount }}</strong> was processed via {{ ucfirst($investment->payment_method) }}.
        </p>

        <div class="txn-details">
            <div class="txn-row">
                <span class="txn-label">Transaction ID</span>
                <span class="txn-value">{{ $investment->transaction_id }}</span>
            </div>
            <div class="txn-row">
                <span class="txn-label">Receipt Number</span>
                <span class="txn-value">{{ $investment->payment_receipt_no }}</span>
            </div>
            <div class="txn-row">
                <span class="txn-label">Pledge Amount</span>
                <span class="txn-value" style="color:var(--green)">{{ $investment->formatted_amount }}</span>
            </div>
            <div class="txn-row">
                <span class="txn-label">Payment Method</span>
                <span class="txn-value">{{ ucfirst($investment->payment_method) }}</span>
            </div>
            <div class="txn-row">
                <span class="txn-label">Compliance Status</span>
                <span class="txn-value" style="color:var(--gold)">Pending Admin Approval</span>
            </div>
            <div class="txn-row">
                <span class="txn-label">Date & Time</span>
                <span class="txn-value">{{ $investment->created_at->format('M d, Y h:i A') }}</span>
            </div>
        </div>

        <div class="success-btns">
            <a href="{{ route('investments.receipt', $investment) }}" target="_blank" class="btn btn-green" style="justify-content:center">Download Printable Receipt 🧾</a>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <a href="{{ route('profile.index', ['tab' => 'investments']) }}" class="btn btn-outline-dark" style="justify-content:center">My Investments</a>
                <a href="{{ route('home') }}" class="btn btn-outline-dark" style="justify-content:center">Home Market</a>
            </div>
        </div>
    </div>
</div>
@endsection
