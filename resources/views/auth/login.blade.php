@extends('layouts.app')
@section('title','Log In')
@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon">🔐</div>
            <h2 class="serif">Welcome Back</h2>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div style="background:#EBF4EB;border:1px solid #BDD9BE;border-radius:10px;padding:14px 16px;margin-bottom:18px;font-size:13px;color:#2D5A2E">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="error-box">{{ session('error') }}</div>
        @endif

        {{-- Validation Errors --}}
        @if($errors->any())
        <div class="error-box">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif

        <form action="{{ route('auth.login.store') }}" method="POST">
            @csrf
            <div class="fg">
                <label class="flb">Email Address</label>
                <input class="finp"
                       name="email"
                       type="email"
                       value="{{ old('email') }}"
                       placeholder="you@startup.com"
                       autofocus/>
                @error('email')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            <div class="fg">
                <label class="flb">
                    Password
                    {{-- Forgot password link --}}
                    <a href="{{ route('password.forgot') }}"
                       style="float:right;font-size:12px;color:#2D5A2E;font-weight:600;text-decoration:none">
                        Forgot password?
                    </a>
                </label>
                <input class="finp"
                       name="password"
                       type="password"
                       placeholder="••••••••"/>
                @error('password')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            <button type="submit" class="fbtn">Log In →</button>
        </form>

        <div class="flink" style="margin-top:16px">
            Don't have an account?
            <a href="{{ route('auth.register') }}">Create one free</a>
        </div>


    </div>
</div>
@endsection
