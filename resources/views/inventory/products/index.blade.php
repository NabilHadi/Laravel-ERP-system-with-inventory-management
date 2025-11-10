@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-4">إدارة المنتجات</h2>
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">قائمة المنتجات</h6>
                    <div>
                        <a href="{{ route('products.low-stock') }}" class="btn btn-warning me-2">
                            <i class="fas fa-exclamation-triangle"></i> المخزون المنخفض
                        </a>
                        <a href="{{ route('products.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> إضافة منتج جديد
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

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>الكود</th>
                                    <th>الاسم</th>
                                    <th>التصنيف</th>
                                    <th>سعر الشراء</th>
                                    <th>سعر البيع</th>
                                    <th>المخزون الحالي</th>
                                    <th>الحد الأدنى</th>
                                    <th>حالة المخزون</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>{{ $product->code }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->category->name }}</td>
                                        <td>{{ number_format($product->purchase_price, 2) }}</td>
                                        <td>{{ number_format($product->selling_price, 2) }}</td>
                                        <td>{{ $product->current_stock }} {{ $product->unit }}</td>
                                        <td>{{ $product->min_stock_level }} {{ $product->unit }}</td>
                                        <td>
                                            @if($product->current_stock <= 0)
                                                <span class="badge bg-danger">نفذ المخزون</span>
                                            @elseif($product->current_stock <= $product->min_stock_level)
                                                <span class="badge bg-warning">مخزون منخفض</span>
                                            @else
                                                <span class="badge bg-success">متوفر</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" 
                                                    onsubmit="return confirm('هل أنت متأكد من حذف هذا المنتج؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">لا توجد منتجات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
@endpush