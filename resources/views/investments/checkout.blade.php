@extends('layouts.app')

@section('title', 'Secure Investment Checkout — LaunchPad Market')

@section('content')
<style>
    .checkout-wrap {
        max-width: 1000px;
        margin: 50px auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 40px;
    }
    .checkout-main {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--r3);
        padding: 36px;
        box-shadow: var(--sh2);
    }
    .checkout-summary {
        background: var(--paper);
        border: 1px solid var(--line2);
        border-radius: var(--r3);
        padding: 30px;
        height: fit-content;
    }
    .checkout-header {
        margin-bottom: 28px;
    }
    .checkout-header h2 {
        font-family: 'Cormorant Garamond', serif;
        font-size: 28px;
        font-weight: 700;
        color: var(--ink);
    }
    .payment-tabs {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        border-bottom: 1px solid var(--line);
        padding-bottom: 12px;
    }
    .pay-tab {
        flex: 1;
        padding: 12px;
        border: 1.5px solid var(--line2);
        border-radius: var(--r1);
        background: var(--off);
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all .2s;
    }
    .pay-tab.active {
        border-color: var(--green);
        background: var(--green5);
        color: var(--green);
    }
    .payment-section {
        display: none;
    }
    .payment-section.active {
        display: block;
    }
    /* QR Code styles */
    .qr-box {
        text-align: center;
        background: #fff;
        border: 1.5px solid var(--line2);
        border-radius: var(--r2);
        padding: 24px;
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }
    .qr-code {
        width: 160px;
        height: 160px;
        background: #ecebeb;
        border: 4px solid var(--paper2);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--r1);
        position: relative;
    }
    .qr-spinner {
        position: absolute;
        width: 140px;
        height: 140px;
        border: 3px solid transparent;
        border-top-color: var(--green);
        border-radius: 50%;
        animation: spin 3s linear infinite;
    }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    /* Processing Overlay */
    .overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(17, 17, 16, 0.85);
        backdrop-filter: blur(8px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-align: center;
    }
    .overlay-content {
        max-width: 400px;
        padding: 30px;
    }
    .loader-circle {
        width: 60px;
        height: 60px;
        border: 4px solid rgba(255,255,255,0.1);
        border-top-color: var(--green3);
        border-radius: 50%;
        margin: 0 auto 24px;
        animation: spin 1s linear infinite;
    }
    .loader-status {
        font-family: 'Cormorant Garamond', serif;
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 12px;
        height: 36px;
    }
    .loader-step {
        font-size: 13.5px;
        color: var(--green3);
    }
    .checkout-alert {
        padding: 16px;
        border-radius: var(--r2);
        font-size: 13.5px;
        line-height: 1.6;
        margin-bottom: 24px;
    }
    .alert-warning {
        background: #FDF8ED;
        border: 1px solid var(--gold3);
        color: var(--gold);
    }
    .alert-success {
        background: var(--green5);
        border: 1px solid var(--green3);
        color: var(--green);
    }
</style>

<!-- Processing Overlay -->
<div id="processing-overlay" class="overlay">
    <div class="overlay-content">
        <div class="loader-circle"></div>
        <div id="status-text" class="loader-status">Connecting to Gateway...</div>
        <div id="step-text" class="loader-step">Step 1 of 4: Initiating handshake</div>
    </div>
</div>

<div class="checkout-wrap">
    <!-- Main Payment Form -->
    <div class="checkout-main">
        <div class="checkout-header">
            <h2>Secure Checkout</h2>
            <p style="font-size:13.5px;color:var(--ink3)">Pledge your investment support safely via simulated API endpoints.</p>
        </div>

        @if($user->kyc_status !== 'approved')
            <div class="checkout-alert alert-warning">
                <strong>⚠️ KYC Verification Pending:</strong> Your account is not yet accredited. You can proceed to test this mock payment, but your investment will remain in <strong>Pending</strong> status until you submit credentials in <a href="{{ route('profile.index', ['tab' => 'kyc']) }}" style="color:inherit;font-weight:700">Settings</a> and the admin approves them.
            </div>
        @else
            <div class="checkout-alert alert-success">
                <strong>✅ Accredited Investor:</strong> Your profile is verified. Approved pledges will be confirmed instantly.
            </div>
        @endif

        <form id="payment-form" action="{{ route('investments.pay', $startup) }}" method="POST">
            @csrf
            
            <!-- Amount Selection -->
            <div class="fg" style="margin-bottom:28px">
                <label class="flb" for="amount">Investment Pledge Amount ($)</label>
                <div style="position:relative">
                    <span style="position:absolute;left:16px;top:11px;font-weight:700;color:var(--ink3)">$</span>
                    <input type="number" class="finp @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', 5000) }}" min="1000" style="padding-left:32px;font-size:18px;font-weight:700;color:var(--green)" required>
                </div>
                <span style="display:block;font-size:11px;color:var(--ink4);margin-top:6px">Minimum investment threshold is $1,000. Maximum limit is $1,000,000.</span>
                @error('amount')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            <!-- Payment Methods Select -->
            <label class="flb">Select Payment Method</label>
            <div class="payment-tabs">
                <div class="pay-tab active" data-tab="stripe">
                    <span>💳 Credit / Debit Card (Stripe)</span>
                </div>
                <div class="pay-tab" data-tab="razorpay">
                    <span>📱 UPI QR Scan (Razorpay)</span>
                </div>
            </div>
            <input type="hidden" name="payment_method" id="payment_method_input" value="stripe">

            <!-- STRIPE FORM CONTENT -->
            <div id="stripe-section" class="payment-section active">
                <div class="fg">
                    <label class="flb" for="card_name">Cardholder Name</label>
                    <input type="text" class="finp" id="card_name" name="card_name" value="{{ old('card_name', $user->name) }}" placeholder="Arjun Mehta">
                </div>
                <div class="fg">
                    <label class="flb" for="card_number">Card Number</label>
                    <input type="text" class="finp" id="card_number" name="card_number" placeholder="4111 2222 3333 4444" maxlength="19">
                </div>
                <div class="frow">
                    <div class="fg">
                        <label class="flb" for="card_expiry">Expiry Date (MM/YY)</label>
                        <input type="text" class="finp" id="card_expiry" name="card_expiry" placeholder="12/28" maxlength="5">
                    </div>
                    <div class="fg">
                        <label class="flb" for="card_cvc">CVC / CVV</label>
                        <input type="text" class="finp" id="card_cvc" name="card_cvc" placeholder="123" maxlength="4">
                    </div>
                </div>
            </div>

            <!-- RAZORPAY FORM CONTENT -->
            <div id="razorpay-section" class="payment-section">
                <div class="qr-box">
                    <div class="qr-code">
                        <div class="qr-spinner"></div>
                        <span style="font-size:24px;z-index:10">📱</span>
                    </div>
                    <div style="font-size:12.5px;color:var(--ink3);font-weight:600">Scan QR Code via GPay, PhonePe, or BHIM</div>
                    <div style="font-size:11px;color:var(--ink4)">Scan simulates instant verification handshakes.</div>
                </div>
                <div class="fg">
                    <label class="flb" for="upi_id">Virtual Payment Address (VPA) / UPI ID</label>
                    <input type="text" class="finp" id="upi_id" name="upi_id" placeholder="investor@upi">
                </div>
            </div>

            <button type="button" id="submit-pledge-btn" class="fbtn" style="margin-top:24px">Submit Investment Pledge</button>
        </form>
    </div>

    <!-- Right Summary Column -->
    <div class="checkout-summary">
        <h3 style="font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:700;margin-bottom:20px;border-bottom:1px solid var(--line);padding-bottom:10px">Investment Summary</h3>
        <div style="display:flex;gap:16px;align-items:center;margin-bottom:24px">
            <div style="width:52px;height:52px;border-radius:12px;background:var(--green);color:#fff;display:flex;align-items:center;justify-content:center;font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:700">
                {{ strtoupper(substr($startup->name, 0, 1)) }}
            </div>
            <div>
                <strong style="font-size:16px;color:var(--ink)">{{ $startup->name }}</strong>
                <span style="display:block;font-size:12px;color:var(--ink3)">{{ $startup->industry }} &bull; {{ $startup->stage }}</span>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:12px;font-size:14px;border-bottom:1px solid var(--line);padding-bottom:16px">
            <div style="display:flex;justify-content:space-between">
                <span style="color:var(--ink3)">Pledge Capital</span>
                <strong id="summary-pledge">$5,000</strong>
            </div>
            <div style="display:flex;justify-content:space-between">
                <span style="color:var(--ink3)">Platform Admin Fee (1%)</span>
                <strong id="summary-fee">$50</strong>
            </div>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:16px;font-size:16px">
            <span style="color:var(--ink);font-weight:700">Total Transaction Amount</span>
            <strong id="summary-total" style="font-size:22px;color:var(--green)">$5,050</strong>
        </div>

        <div style="margin-top:30px;background:#fff;border:1px solid var(--line);padding:18px;border-radius:var(--r1);font-size:12px;color:var(--ink3)">
            <strong>🛡️ Safe Harbor Guarantee</strong>
            <p style="margin-top:6px;line-height:1.5">
                Pledges are fully structured via simulated protocols. Under no circumstances will your physical banking account details be processed.
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const tabs = document.querySelectorAll(".pay-tab");
    const sections = document.querySelectorAll(".payment-section");
    const methodInput = document.getElementById("payment_method_input");

    // Handle switching payment methods
    tabs.forEach(tab => {
        tab.addEventListener("click", function() {
            tabs.forEach(t => t.classList.remove("active"));
            sections.forEach(s => s.classList.remove("active"));

            this.classList.add("active");
            const selectedMethod = this.getAttribute("data-tab");
            document.getElementById(selectedMethod + "-section").classList.add("active");
            methodInput.value = selectedMethod;
        });
    });

    // Handle updating calculations
    const amountInput = document.getElementById("amount");
    const sumPledge = document.getElementById("summary-pledge");
    const sumFee = document.getElementById("summary-fee");
    const sumTotal = document.getElementById("summary-total");

    function updateCalculations() {
        let value = parseFloat(amountInput.value);
        if (isNaN(value) || value < 0) {
            value = 0;
        }

        const fee = value * 0.01;
        const total = value + fee;

        sumPledge.textContent = "$" + value.toLocaleString();
        sumFee.textContent = "$" + fee.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0});
        sumTotal.textContent = "$" + total.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }

    amountInput.addEventListener("input", updateCalculations);
    updateCalculations(); // run initially

    // Handle Simulated loading screen
    const submitBtn = document.getElementById("submit-pledge-btn");
    const overlay = document.getElementById("processing-overlay");
    const statusText = document.getElementById("status-text");
    const stepText = document.getElementById("step-text");
    const form = document.getElementById("payment-form");

    submitBtn.addEventListener("click", function(e) {
        // Simple client side validation check before starting animation
        if (!amountInput.value || parseFloat(amountInput.value) < 1000) {
            alert("Please enter a valid investment pledge amount of at least $1,000.");
            return;
        }

        if (methodInput.value === 'stripe') {
            if (!document.getElementById("card_name").value || !document.getElementById("card_number").value) {
                alert("Please fill out your card details to run the simulation.");
                return;
            }
        } else {
            if (!document.getElementById("upi_id").value) {
                alert("Please fill out your UPI VPA Address to run the simulation.");
                return;
            }
        }

        // Show overlay
        overlay.style.display = "flex";

        const steps = [
            { text: "Contacting payment gateway...", step: "Step 1 of 4: Initializing token handshake" },
            { text: "Verifying secure credentials...", step: "Step 2 of 4: Validating security signature" },
            { text: "Authorizing pledge amount...", step: "Step 3 of 4: Syncing ledger registers" },
            { text: "Transaction confirmed!", step: "Step 4 of 4: Finalizing receipts..." }
        ];

        let index = 0;
        const timer = setInterval(() => {
            if (index < steps.length) {
                statusText.textContent = steps[index].text;
                stepText.textContent = steps[index].step;
                index++;
            } else {
                clearInterval(timer);
                form.submit(); // submit form
            }
        }, 1000);
    });
});
</script>
@endsection
