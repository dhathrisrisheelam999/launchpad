@extends('layouts.app')
@section('title', 'Forgot Password')
@section('content')

<div class="auth-page">
    <div class="auth-card">

        {{-- Header --}}
        <div class="auth-header">
            <div class="auth-icon">🔐</div>
            <h2 class="serif">Forgot Password?</h2>
            <p style="font-size:13px;color:#9A9A92;margin-top:6px;line-height:1.6">
                No worries! Enter your email and we'll send you a reset link instantly.
            </p>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
        <div style="background:#EBF4EB;border:1px solid #BDD9BE;border-radius:10px;padding:16px;margin-bottom:20px;font-size:13px;color:#2D5A2E;line-height:1.6">
            <div>{{ session('success') }}</div>
            @if(session('reset_url') && config('mail.default') === 'log')
                <div style="margin-top:12px;padding-top:12px;border-top:1px dashed #BDD9BE">
                    <strong style="color:#1D3C1F">🛠️ Local Development Quick Link:</strong><br>
                    <a href="{{ session('reset_url') }}" style="color:#1976D2;font-weight:700;word-break:break-all;text-decoration:underline">{{ session('reset_url') }}</a>
                </div>
            @endif
        </div>
        @endif

        {{-- Error Messages --}}
        @if($errors->any())
        <div class="error-box">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('password.send') }}" method="POST">
            @csrf
            <div class="fg">
                <label class="flb">Your Email Address</label>
                <input class="finp"
                       name="email"
                       type="email"
                       value="{{ old('email') }}"
                       placeholder="rohan@startup.com"
                       autofocus
                       required/>
                @error('email')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="fbtn">
                📧 Send Reset Link
            </button>
        </form>

        {{-- Info box --}}
        <div style="background:#F5FAF5;border-radius:10px;padding:14px 16px;margin-top:20px;font-size:12px;color:#6B6B67;line-height:1.65">
            <strong style="color:#2D5A2E">📌 How it works:</strong><br>
            1. Enter your registered email above<br>
            2. Check your inbox for the reset link<br>
            3. Click the link and set a new password<br>
            4. Link expires in 60 minutes
        </div>

        <div class="flink" style="margin-top:20px">
            Remember your password?
            <a href="{{ route('auth.login') }}">Log In</a>
            ·
            <a href="{{ route('auth.register') }}">Create Account</a>
        </div>
    </div>
</div>
@endsection
