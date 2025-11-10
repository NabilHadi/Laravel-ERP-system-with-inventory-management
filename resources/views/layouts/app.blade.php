<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- RTL & Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    
    <!-- Custom RTL Styles -->
    <style>
        body {
            font-family: 'Noto Sans Arabic', sans-serif;
        }
        .navbar-nav {
            padding-right: 0;
        }
        .dropdown-menu {
            text-align: right;
            min-width: 11rem;
        }
        .ms-auto {
            margin-right: auto !important;
            margin-left: 0 !important;
        }
        .me-auto {
            margin-left: auto !important;
            margin-right: 0 !important;
        }
        .border-right-primary {
            border-right: 4px solid #4e73df;
        }
        .border-right-success {
            border-right: 4px solid #1cc88a;
        }
        .border-right-warning {
            border-right: 4px solid #f6c23e;
        }
        .border-right-info {
            border-right: 4px solid #36b9cc;
        }
        .table {
            text-align: right;
        }
    </style>

    <!-- Google Fonts - Noto Sans Arabic -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Main Navigation -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            <!-- Dashboard -->
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('home') }}">
                                    <i class="fas fa-tachometer-alt me-1"></i> لوحة البيانات
                                </a>
                            </li>

                            <!-- Inventory Management -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="inventoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-warehouse me-1"></i> المخزون
                                </a>
                                <div class="dropdown-menu" aria-labelledby="inventoryDropdown">
                                    <a class="dropdown-item text-end" href="{{ route('products.index') }}">
                                        <i class="fas fa-boxes me-1"></i> المنتجات
                                    </a>
                                    <a class="dropdown-item text-end" href="{{ route('categories.index') }}">
                                        <i class="fas fa-tags me-1"></i> التصنيفات
                                    </a>
                                    <a class="dropdown-item text-end" href="{{ route('products.low-stock') }}">
                                        <i class="fas fa-exclamation-triangle me-1"></i> المخزون المنخفض
                                    </a>
                                </div>
                            </li>

                            <!-- Sales Management -->
                            <!-- <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="salesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-shopping-cart me-1"></i> المبيعات
                                </a>
                                <div class="dropdown-menu" aria-labelledby="salesDropdown">
                                    <a class="dropdown-item text-end" href="{{ route('sales.index') }}">
                                        <i class="fas fa-list me-1"></i> قائمة المبيعات
                                    </a>
                                    <a class="dropdown-item text-end" href="{{ route('sales.create') }}">
                                        <i class="fas fa-plus me-1"></i> إضافة فاتورة مبيعات
                                    </a>
                                    <a class="dropdown-item text-end" href="{{ route('customers.index') }}">
                                        <i class="fas fa-users me-1"></i> العملاء
                                    </a>
                                </div>
                            </li> -->

                            <!-- Purchase Management -->
                           <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="purchasesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-truck me-1"></i> المشتريات
                                </a>
                                <div class="dropdown-menu" aria-labelledby="purchasesDropdown">
                                    <a class="dropdown-item text-end" href="{{ route('purchases.index') }}">
                                        <i class="fas fa-list me-1"></i> قائمة المشتريات
                                    </a>
                                    <a class="dropdown-item text-end" href="{{ route('purchases.create') }}">
                                        <i class="fas fa-plus me-1"></i> إضافة فاتورة مشتريات
                                    </a>
                                    <a class="dropdown-item text-end" href="{{ route('suppliers.index') }}">
                                        <i class="fas fa-truck-loading me-1"></i> الموردين
                                    </a>
                                </div>
                            </li> 

                            <!-- Reports -->
                            <!-- <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="reportsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-chart-bar me-1"></i> التقارير
                                </a>
                                <div class="dropdown-menu" aria-labelledby="reportsDropdown">
                                    <a class="dropdown-item text-end" href="{{ route('reports.sales') }}">
                                        <i class="fas fa-chart-line me-1"></i> تقارير المبيعات
                                    </a>
                                    <a class="dropdown-item text-end" href="{{ route('reports.purchases') }}">
                                        <i class="fas fa-chart-pie me-1"></i> تقارير المشتريات
                                    </a>
                                    <a class="dropdown-item text-end" href="{{ route('reports.inventory') }}">
                                        <i class="fas fa-boxes me-1"></i> تقارير المخزون
                                    </a>
                                    <a class="dropdown-item text-end" href="{{ route('reports.customers') }}">
                                        <i class="fas fa-users me-1"></i> تقارير العملاء
                                    </a>
                                    <a class="dropdown-item text-end" href="{{ route('reports.suppliers') }}">
                                        <i class="fas fa-truck me-1"></i> تقارير الموردين
                                    </a>
                                </div>
                            </li> -->
                        @endauth
                    </ul>

                    <!-- User Navigation -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">تسجيل الدخول</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="fas fa-user me-1"></i> {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item text-end" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-1"></i> تسجيل الخروج
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
