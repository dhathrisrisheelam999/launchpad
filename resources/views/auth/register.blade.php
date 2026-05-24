@extends('layouts.app')
@section('title','Register')
@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon">🚀</div>
            <h2 class="serif">Create Account</h2>
        </div>
        @if($errors->any())
        <div class="error-box">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        @endif
        <form action="{{ route('auth.register.store') }}" method="POST">
            @csrf
            <div class="fg">
                <label class="flb">Full Name</label>
                <input class="finp" name="name" value="{{ old('name') }}" placeholder="Rohan Verma"/>
                @error('name')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="fg">
                <label class="flb">Email</label>
                <input class="finp" name="email" type="email" value="{{ old('email') }}" placeholder="rohan@startup.com"/>
                @error('email')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="fg">
                <label class="flb">I am a</label>
                <select class="fsel" name="role">
                    <option value="founder">Founder / CEO</option>
                    <option value="investor">Investor / VC</option>
                    <option value="acquirer">Acquirer</option>
                    <option value="advisor">Advisor</option>
                </select>
            </div>
            <div class="fg">
                <label class="flb">Password</label>
                <input class="finp" name="password" type="password" placeholder="Min 8 characters"/>
                @error('password')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="fg">
                <label class="flb">Confirm Password</label>
                <input class="finp" name="password_confirmation" type="password" placeholder="Repeat password"/>
            </div>
            <button type="submit" class="fbtn">Create Account</button>
        </form>
        <div class="flink">Already have an account? <a href="{{ route('auth.login') }}">Log In</a></div>
    </div>
</div>
@endsection