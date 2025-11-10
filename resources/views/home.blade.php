@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">القائمة الرئيسية</h1>
    </div>

    <!-- Row 1: Financial & Inventory Overview -->
    <div class="row">
        <!-- المبيعات اليومية -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-right-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                المبيعات اليومية</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ isset($data['total_sales']) ? number_format($data['total_sales'], 2) : '0.00' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- المشتريات اليومية -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-right-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                المشتريات اليومية</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ isset($data['total_purchases']) ? number_format($data['total_purchases'], 2) : '0.00' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- قيمة المخزون -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-right-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                قيمة المخزون</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ isset($data['inventory_value']) ? number_format($data['inventory_value'], 2) : '0.00' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- منتجات منخفضة المخزون -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-right-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                منتجات منخفضة المخزون</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ isset($data['low_stock_products']) ? $data['low_stock_products'] : '0' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Stats -->
    <div class="row">
        <!-- المنتجات -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-right-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                المنتجات</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Product::count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- التصنيفات -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-right-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                التصنيفات</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Category::count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- العملاء -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-right-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                العملاء</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ isset($data['total_customers']) ? $data['total_customers'] : \App\Models\Customer::count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الموردين -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-right-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                الموردين</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Supplier::count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Recent Activity Tables -->
    <div class="row">
        <!-- Recent Sales Table -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">آخر المبيعات</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>رقم الفاتورة</th>
                                    <th>العميل</th>
                                    <th>التاريخ</th>
                                    <th>المجموع</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($data['recent_sales']))
                                    @foreach($data['recent_sales'] as $sale)
                                    <tr>
                                        <td>{{ $sale->invoice_number }}</td>
                                        <td>{{ $sale->customer->name }}</td>
                                        <td>{{ $sale->sale_date->format('Y-m-d') }}</td>
                                        <td>{{ number_format($sale->total, 2) }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">لا توجد مبيعات حديثة</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Purchases Table -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">آخر المشتريات</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>رقم الفاتورة</th>
                                    <th>المورد</th>
                                    <th>التاريخ</th>
                                    <th>المجموع</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($data['recent_purchases']))
                                    @foreach($data['recent_purchases'] as $purchase)
                                    <tr>
                                        <td>{{ $purchase->invoice_number }}</td>
                                        <td>{{ $purchase->supplier->name }}</td>
                                        <td>{{ $purchase->purchase_date->format('Y-m-d') }}</td>
                                        <td>{{ number_format($purchase->total, 2) }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">لا توجد مشتريات حديثة</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 4: Low Stock Products -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">المنتجات منخفضة المخزون</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>المنتج</th>
                                    <th>الكود</th>
                                    <th>التصنيف</th>
                                    <th>المخزون الحالي</th>
                                    <th>الحد الأدنى</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\Product::with('category')->whereRaw('current_stock <= min_stock_level')->take(5)->get() as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->code }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>{{ $product->current_stock }} {{ $product->unit }}</td>
                                    <td>{{ $product->min_stock_level }} {{ $product->unit }}</td>
                                    <td>
                                        <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('products.low-stock') }}" class="btn btn-warning">
                            عرض كل المنتجات منخفضة المخزون
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 5: Top Products -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">المنتجات الأكثر مبيعاً</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>المنتج</th>
                                    <th>الكود</th>
                                    <th>التصنيف</th>
                                    <th>عدد المبيعات</th>
                                    <th>المخزون الحالي</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($data['top_products']) && count($data['top_products']) > 0)
                                    @foreach($data['top_products'] as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->code }}</td>
                                        <td>{{ $product->category->name }}</td>
                                        <td>{{ $product->sale_items_count }}</td>
                                        <td>{{ $product->current_stock }} {{ $product->unit }}</td>
                                        <td>
                                            <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center">لا توجد بيانات متوفرة</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
