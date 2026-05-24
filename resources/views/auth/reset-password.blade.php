@extends('layouts.app')
@section('title', 'Reset Password')
@section('content')

<div class="auth-page">
    <div class="auth-card">

        {{-- Header --}}
        <div class="auth-header">
            <div class="auth-icon">🔑</div>
            <h2 class="serif">Set New Password</h2>
            <p style="font-size:13px;color:#9A9A92;margin-top:6px">
                Enter your new password below for <strong>{{ $email }}</strong>
            </p>
        </div>

        {{-- Errors --}}
        @if($errors->any())
        <div class="error-box">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('password.reset') }}" method="POST">
            @csrf

            {{-- Hidden fields --}}
            <input type="hidden" name="token" value="{{ $token }}"/>
            <input type="hidden" name="email" value="{{ $email }}"/>

            <div class="fg">
                <label class="flb">New Password</label>
                <input class="finp"
                       name="password"
                       type="password"
                       placeholder="Min. 8 characters"
                       required/>
                @error('password')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="fg">
                <label class="flb">Confirm New Password</label>
                <input class="finp"
                       name="password_confirmation"
                       type="password"
                       placeholder="Repeat your new password"
                       required/>
            </div>

            {{-- Password strength tips --}}
            <div style="background:#F5FAF5;border-radius:10px;padding:12px 16px;margin-bottom:18px;font-size:12px;color:#6B6B67;line-height:1.7">
                <strong style="color:#2D5A2E">Strong password tips:</strong><br>
                ✅ At least 8 characters<br>
                ✅ Mix of letters and numbers<br>
                ✅ At least one special character (!@#$)
            </div>

            <button type="submit" class="fbtn">
                🔐 Reset Password
            </button>
        </form>

        <div class="flink" style="margin-top:16px">
            <a href="{{ route('password.forgot') }}">Request a new link</a>
            ·
            <a href="{{ route('auth.login') }}">Back to Login</a>
        </div>
    </div>
</div>
@endsection
