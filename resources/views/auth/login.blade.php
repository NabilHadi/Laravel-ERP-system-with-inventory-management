@extends('layouts.auth')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        overflow: hidden;
    }

    .login-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);
        
        background-size: 400% 400%;
        animation: gradient-shift 15s ease infinite;
        position: relative;
        overflow: hidden;
    }

    @keyframes gradient-shift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Animated background shapes */
    .floating-shape {
        position: absolute;
        border-radius: 50%;
        opacity: 0.1;
        animation: float 6s ease-in-out infinite;
    }

    .shape-1 {
        width: 300px;
        height: 300px;
        background: #fff;
        top: -50px;
        left: -50px;
        animation-delay: 0s;
    }

    .shape-2 {
        width: 200px;
        height: 200px;
        background: #fff;
        bottom: 100px;
        right: -50px;
        animation-delay: 2s;
    }

    .shape-3 {
        width: 150px;
        height: 150px;
        background: #fff;
        bottom: -50px;
        left: 100px;
        animation-delay: 4s;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(10deg); }
    }

    /* Animated dots */
    .dot-container {
        position: absolute;
        width: 100%;
        height: 100%;
    }

    .dot {
        position: absolute;
        width: 6px;
        height: 6px;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 50%;
        animation: pulse-dot 3s ease-in-out infinite;
    }

    @keyframes pulse-dot {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.5); opacity: 0.2; }
    }

    .dot:nth-child(1) { top: 10%; left: 10%; animation-delay: 0s; }
    .dot:nth-child(2) { top: 20%; right: 15%; animation-delay: 0.5s; }
    .dot:nth-child(3) { bottom: 20%; left: 20%; animation-delay: 1s; }
    .dot:nth-child(4) { bottom: 10%; right: 10%; animation-delay: 1.5s; }
    .dot:nth-child(5) { top: 50%; left: 5%; animation-delay: 2s; }

    .login-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        width: 100%;
        max-width: 420px;
        padding: 50px;
        animation: slide-up 0.6s ease-out;
        border: 1px solid rgba(255, 255, 255, 0.2);
        position: relative;
        z-index: 10;
    }

    @keyframes slide-up {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .login-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .login-header h1 {
        font-size: 32px;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 10px;
        animation: fade-in-down 0.8s ease-out 0.2s backwards;
    }

    .login-header p {
        color: #718096;
        font-size: 14px;
        animation: fade-in-down 0.8s ease-out 0.4s backwards;
    }

    @keyframes fade-in-down {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-group {
        margin-bottom: 24px;
        animation: fade-in-up 0.8s ease-out backwards;
    }

    .form-group:nth-child(1) { animation-delay: 0.3s; }
    .form-group:nth-child(2) { animation-delay: 0.4s; }
    .form-group:nth-child(3) { animation-delay: 0.5s; }

    @keyframes fade-in-up {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #2d3748;
        font-size: 14px;
        text-align: right;
    }

    .form-group input[type="email"],
    .form-group input[type="password"] {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: #f7fafc;
    }

    .form-group input[type="email"]:focus,
    .form-group input[type="password"]:focus {
        outline: none;
        border-color: #667eea;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-group input[type="email"].is-invalid,
    .form-group input[type="password"].is-invalid {
        border-color: #fc8181;
        background: #fff5f5;
    }

    .invalid-feedback {
        display: block;
        color: #fc8181;
        font-size: 12px;
        margin-top: 6px;
        animation: shake 0.4s ease-in-out;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .form-check {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 8px;
    }

    .form-check-input1 {
        width: 18px;
        height: 18px;
        min-width: 18px;
        flex-shrink: 0;
        margin: 0;
        cursor: pointer;
        accent-color: #667eea;
        margin-left: 0;
    }

    .form-check-label1 {
        margin: 0;
        color: #4a5568;
        font-size: 14px;
        cursor: pointer;
        white-space: nowrap;
    }

    .login-button {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #667eea 0%, #2b1599ff 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 10px;
        animation: fade-in-up 0.8s ease-out 0.6s backwards;
    }

    .login-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    }

    .login-button:active {
        transform: translateY(0);
    }

    .forgot-password {
        text-align: center;
        margin-top: 20px;
        animation: fade-in-up 0.8s ease-out 0.7s backwards;
    }

    .forgot-password a {
        color: #667eea;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .forgot-password a:hover {
        color: #764ba2;
        text-decoration: underline;
    }

    @media (max-width: 600px) {
        .login-card {
            padding: 30px 20px;
            max-width: 90%;
        }

        .login-header h1 {
            font-size: 24px;
        }

        .floating-shape {
            display: none;
        }
    }
</style>

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
                        class="form-check-input1" 
                        type="checkbox" 
                        name="remember" 
                        id="remember" 
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    <label class="form-check-label1" for="remember">
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
