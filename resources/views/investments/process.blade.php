@extends('layouts.app')

@section('title', 'Investment Process & Workflow — LaunchPad Market')

@section('content')
<style>
    .process-hero {
        background: linear-gradient(135deg, var(--paper) 0%, var(--w) 100%);
        border-bottom: 1px solid var(--line);
        padding: 80px 0 60px;
        text-align: center;
    }
    .process-hero h1 {
        font-family: 'Cormorant Garamond', serif;
        font-size: clamp(36px, 5vw, 54px);
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 16px;
    }
    .process-hero p {
        font-size: 16px;
        color: var(--ink3);
        max-width: 600px;
        margin: 0 auto;
    }
    .process-sec {
        max-width: 900px;
        margin: 60px auto;
        padding: 0 20px;
    }
    .step-list {
        display: flex;
        flex-direction: column;
        gap: 40px;
        margin-bottom: 60px;
        position: relative;
    }
    .step-list::before {
        content: '';
        position: absolute;
        top: 24px;
        bottom: 24px;
        left: 24px;
        width: 2px;
        background: var(--line);
        z-index: 1;
    }
    .step-item {
        display: flex;
        gap: 24px;
        position: relative;
        z-index: 2;
    }
    .step-num {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--green);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Cormorant Garamond', serif;
        font-size: 24px;
        font-weight: 700;
        box-shadow: 0 4px 10px rgba(45,90,46,.2);
        flex-shrink: 0;
    }
    .step-content {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--r2);
        padding: 24px 30px;
        box-shadow: var(--sh1);
        flex: 1;
    }
    .step-title {
        font-family: 'Cormorant Garamond', serif;
        font-size: 22px;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 8px;
    }
    .step-text {
        font-size: 14.5px;
        color: var(--ink3);
        line-height: 1.65;
    }
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 60px;
    }
    .info-card {
        background: var(--paper);
        border: 1px solid var(--line2);
        border-radius: var(--r2);
        padding: 28px;
    }
    .info-card h3 {
        font-family: 'Cormorant Garamond', serif;
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 12px;
        color: var(--ink);
    }
    .info-card p, .info-card li {
        font-size: 13.5px;
        color: var(--ink3);
        line-height: 1.6;
    }
    .terms-box {
        background: var(--off);
        border: 1px solid var(--line);
        border-radius: var(--r2);
        padding: 30px;
        font-size: 13px;
        color: var(--ink3);
        line-height: 1.65;
    }
    .terms-box h3 {
        font-family: 'Cormorant Garamond', serif;
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 12px;
        color: var(--ink);
    }
</style>

<div class="process-hero">
    <div class="container">
        <h1>How Investment Works</h1>
        <p>A transparent, structured, and legally compliant guide to backing innovative startups on LaunchPad Market.</p>
    </div>
</div>

<div class="process-sec">
    <!-- Steps -->
    <div class="step-list">
        <div class="step-item">
            <div class="step-num">1</div>
            <div class="step-content">
                <h3 class="step-title">Discover Active Startups</h3>
                <p class="step-text">
                    Browse our curated marketplace of startups seeking funding. Filter by industry, growth stage, MRR growth, or valuation. View founder pitches, reviews, and favorites.
                </p>
            </div>
        </div>

        <div class="step-item">
            <div class="step-num">2</div>
            <div class="step-content">
                <h3 class="step-title">Submit KYC Verification</h3>
                <p class="step-text">
                    Compliance is vital. Before investing, navigate to your <a href="{{ route('profile.index', ['tab' => 'kyc']) }}">Profile -> KYC Verification</a> and upload proof of accreditation (Passport, DL, or Accreditation certificate). This certifies your status as an authorized investor.
                </p>
            </div>
        </div>

        <div class="step-item">
            <div class="step-num">3</div>
            <div class="step-content">
                <h3 class="step-title">Place Your Pledge</h3>
                <p class="step-text">
                    Once you find a startup, click "Invest Now" on their profile page. Enter your desired investment pledge (minimum $1,000 limit) and proceed to the secure mock payment gateway.
                </p>
            </div>
        </div>

        <div class="step-item">
            <div class="step-num">4</div>
            <div class="step-content">
                <h3 class="step-title">Complete Checkout (Stripe / Razorpay)</h3>
                <p class="step-text">
                    Choose between a credit card check out simulating Stripe or scanner payment simulating Razorpay QR interface. Our mock transaction logs generate realistic payment confirmation.
                </p>
            </div>
        </div>

        <div class="step-item">
            <div class="step-num">5</div>
            <div class="step-content">
                <h3 class="step-title">Admin Review & Approval</h3>
                <p class="step-text">
                    Once paid, your pledge enters "Pending Approval". The admin verifies that your KYC accreditation matches regulations. Once approved, you receive an in-app notification and your invoice changes status.
                </p>
            </div>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="info-grid">
        <div class="info-card">
            <h3>Investment Guidelines</h3>
            <ul style="padding-left: 18px; display: flex; flex-direction: column; gap: 8px;">
                <li><strong>Minimum Investment:</strong> $1,000 per startup to ensure professional commitment levels.</li>
                <li><strong>Platform Fees:</strong> 1% administration fee is added to invoice receipts for platform maintenance.</li>
                <li><strong>Verification Required:</strong> Standard regulatory compliance limits transactions to verified accounts.</li>
            </ul>
        </div>
        <div class="info-card">
            <h3>Supported Payment Methods</h3>
            <ul style="padding-left: 18px; display: flex; flex-direction: column; gap: 8px;">
                <li><strong>Stripe Simulation:</strong> Supports mock credit cards (Visa, MasterCard, Amex) with realistic checking dialogues.</li>
                <li><strong>Razorpay Simulation:</strong> Supports QR-code scanning and instant virtual UPI address resolution.</li>
                <li><strong>No Real Monies:</strong> Safe environments sandbox all transactions for demo purposes.</li>
            </ul>
        </div>
    </div>

    <!-- Terms and Conditions -->
    <div class="terms-box">
        <h3>Standard Investment Disclaimers</h3>
        <p style="margin-bottom: 12px;">
            Please read these terms carefully. By placing an investment pledge on LaunchPad Market, you acknowledge and agree that:
        </p>
        <p style="margin-bottom: 12px;">
            1. Startup investing is high risk. You may lose the entirety of your pledged amount. Past performances of founders do not represent future outcomes.
        </p>
        <p style="margin-bottom: 12px;">
            2. The platform is designed strictly for demo/academic assessment purposes. No real currency is charged, processed, or distributed to startups.
        </p>
        <p>
            3. All user listings, inquiries, replies, and investments are logged for system audit trails and grading.
        </p>
    </div>
</div>
@endsection
