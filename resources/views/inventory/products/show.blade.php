@extends('layouts.app')

@section('title', 'تفاصيل المنتج')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">تفاصيل المنتج</h3>
                    <div>
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">اسم المنتج</th>
                                    <td>{{ $product->name }}</td>
                                </tr>
                                <tr>
                                    <th>الكود</th>
                                    <td>{{ $product->code }}</td>
                                </tr>
                                <tr>
                                    <th>التصنيف</th>
                                    <td>{{ $product->category->name ?? 'غير مصنف' }}</td>
                                </tr>
                                <tr>
                                    <th>المخزون الحالي</th>
                                    <td>{{ number_format($product->current_stock, 0) }}</td>
                                </tr>
                                <tr>
                                    <th>الحد الأدنى للمخزون</th>
                                    <td>{{ number_format($product->min_stock_level, 0) }}</td>
                                </tr>
                                <tr>
                                    <th>سعر البيع</th>
                                    <td>{{ number_format($product->selling_price, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>سعر الشراء</th>
                                    <td>{{ number_format($product->purchase_price, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>الوصف</th>
                                    <td>{{ $product->description ?: 'لا يوجد وصف' }}</td>
                                </tr>
                                <!-- الحالة (status) row removed -->
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>إحصائيات المنتج</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <div class="border-right-primary p-3">
                                                <h5>إجمالي المبيعات</h5>
                                                <h3>{{ number_format($totalSales, 2) }}</h3>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="border-right-success p-3">
                                                <h5>إجمالي المشتريات</h5>
                                                <h3>{{ number_format($totalPurchases, 2) }}</h3>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="border-right-info p-3">
                                                <h5>الكمية المباعة</h5>
                                                <h3>{{ number_format($soldQuantity, 0) }}</h3>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="border-right-warning p-3">
                                                <h5>الكمية المشتراة</h5>
                                                <h3>{{ number_format($purchasedQuantity, 0) }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($product->image)
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h4>صورة المنتج</h4>
                                    </div>
                                    <div class="card-body">
                                        <img src="{{ asset('storage/' . $product->image) }}" 
                                             alt="{{ $product->name }}" 
                                             class="img-fluid">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Stock Movement History -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>حركة المخزون</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>التاريخ</th>
                                            <th>نوع الحركة</th>
                                            <th>الكمية</th>
                                            <th>المرجع</th>
                                            <th>الملاحظات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($stockMovements as $movement)
                                            <tr>
                                                <td>{{ $movement->created_at->format('Y-m-d') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $movement->type === 'in' ? 'success' : 'danger' }}">
                                                        {{ $movement->type === 'in' ? 'وارد' : 'صادر' }}
                                                    </span>
                                                </td>
                                                <td>{{ number_format($movement->quantity, 0) }}</td>
                                                <td>{{ $movement->reference }}</td>
                                                <td>{{ $movement->notes }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">لا توجد حركات مخزون</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .border-right-primary { border-right: 4px solid #4e73df; }
    .border-right-success { border-right: 4px solid #1cc88a; }
    .border-right-info { border-right: 4px solid #36b9cc; }
    .border-right-warning { border-right: 4px solid #f6c23e; }
</style>
@endpush