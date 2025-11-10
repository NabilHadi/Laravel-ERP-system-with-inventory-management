@extends('layouts.app')

@section('title', 'المنتجات ذات المخزون المنخفض')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-4">المنتجات ذات المخزون المنخفض</h2>
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-exclamation-triangle"></i> المنتجات التي تحتاج إلى إعادة طلب
                    </h6>
                    <div>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> جميع المنتجات
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @forelse($products as $product)
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="card border-left-warning">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h5 class="card-title">{{ $product->name }}</h5>
                                                <p class="card-text text-muted mb-2">
                                                    <strong>الكود:</strong> {{ $product->code }}
                                                </p>
                                                <p class="card-text text-muted mb-2">
                                                    <strong>التصنيف:</strong> {{ $product->category->name ?? 'غير مصنف' }}
                                                </p>
                                                <p class="card-text text-muted">
                                                    <strong>الوصف:</strong> {{ $product->description ?: 'لا يوجد وصف' }}
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-6 mb-3">
                                                        <div class="border-left-warning p-3">
                                                            <h6 class="text-warning">المخزون الحالي</h6>
                                                            <h4>{{ $product->current_stock }} {{ $product->unit }}</h4>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 mb-3">
                                                        <div class="border-left-info p-3">
                                                            <h6 class="text-info">الحد الأدنى</h6>
                                                            <h4>{{ $product->min_stock_level }} {{ $product->unit }}</h4>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-6 mb-3">
                                                        <div class="border-left-primary p-3">
                                                            <h6 class="text-primary">سعر الشراء</h6>
                                                            <h5>{{ number_format($product->purchase_price, 2) }}</h5>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 mb-3">
                                                        <div class="border-left-success p-3">
                                                            <h6 class="text-success">سعر البيع</h6>
                                                            <h5>{{ number_format($product->selling_price, 2) }}</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <div class="progress" style="height: 25px;">
                                                    <div class="progress-bar bg-warning" 
                                                         role="progressbar" 
                                                         style="width: {{ min(($product->current_stock / $product->min_stock_level) * 100, 100) }}%"
                                                         aria-valuenow="{{ $product->current_stock }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="{{ $product->min_stock_level }}">
                                                        {{ number_format(($product->current_stock / $product->min_stock_level) * 100, 0) }}%
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> عرض التفاصيل
                                                </a>
                                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i> تعديل
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-check-circle"></i> جميع المنتجات لديها مخزون كافي
                        </div>
                    @endforelse

                    @if($products->hasPages())
                        <div class="mt-4">
                            {{ $products->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .border-left-warning { border-left: 4px solid #f6c23e; }
    .border-left-info { border-left: 4px solid #36b9cc; }
    .border-left-primary { border-left: 4px solid #4e73df; }
    .border-left-success { border-left: 4px solid #1cc88a; }
    
    .card {
        transition: box-shadow 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
</style>
@endpush
