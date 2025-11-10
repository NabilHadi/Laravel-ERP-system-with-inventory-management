@extends('layouts.app')

@section('title', 'تفاصيل التصنيف')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">تفاصيل التصنيف</h3>
                    <div>
                        <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">اسم التصنيف</th>
                                    <td>{{ $category->name }}</td>
                                </tr>
                                <tr>
                                    <th>الوصف</th>
                                    <td>{{ $category->description ?: 'لا يوجد وصف' }}</td>
                                </tr>
                                <tr>
                                    <th>عدد المنتجات</th>
                                    <td>{{ $productsCount }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>إحصائيات التصنيف</h4>
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products in this category -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>المنتجات في هذا التصنيف</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>الكود</th>
                                            <th>اسم المنتج</th>
                                            <th>المخزون الحالي</th>
                                            <th>سعر البيع</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($products as $product)
                                            <tr>
                                                <td>{{ $product->code }}</td>
                                                <td>{{ $product->name }}</td>
                                                <td>{{ number_format($product->current_stock, 0) }}</td>
                                                <td>{{ number_format($product->selling_price, 2) }}</td>
                                                <td>
                                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">لا توجد منتجات في هذا التصنيف</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

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
    </div>
</div>
@endsection

@push('styles')
<style>
    .border-right-primary { border-right: 4px solid #4e73df; }
    .border-right-success { border-right: 4px solid #1cc88a; }
</style>
@endpush