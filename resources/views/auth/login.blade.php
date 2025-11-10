@extends('layouts.auth')

@section('content')
<div class="login-container">
    <!-- Animated Background Shapes -->
    <div class="floating-shape shape-1"></div>
    <div class="floating-shape shape-2"></div>
    <div class="floating-shape shape-3"></div>

    <!-- Animated Dots -->
    <div class="dot-container">
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </div>

    <!-- Login Card -->
    <div class="login-card">
        <div class="login-header">
            <h1>محاسب برو</h1>
            <p>نظام إدارة المخزون والفواتير</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input 
                    id="email" 
                    type="email" 
                    class="@error('email') is-invalid @enderror" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autocomplete="email" 
                    autofocus
                    placeholder="user@example.com"
                >
                @error('email')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">كلمة المرور</label>
                <input 
                    id="password" 
                    type="password" 
                    class="@error('password') is-invalid @enderror" 
                    name="password" 
                    required 
                    autocomplete="current-password"
                    placeholder="••••••••"
                >
                @error('password')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input 
                        class="form-check-input" 
                        type="checkbox" 
                        name="remember" 
                        id="remember" 
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="remember">
                        تذكرني
                    </label>
                </div>
            </div>

            <button type="submit" class="login-button">
                <i class="fas fa-sign-in-alt" style="margin-left: 8px;"></i> تسجيل الدخول
            </button>

        </form>
    </div>
</div>
@endsection
