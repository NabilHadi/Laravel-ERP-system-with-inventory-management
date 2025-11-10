@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ isset($product) ? 'تعديل منتج' : 'إضافة منتج جديد' }}
                    </h6>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading">
                                <i class="fas fa-exclamation-circle"></i> خطأ في النموذج
                            </h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}">
                        @csrf
                        @if(isset($product))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name">اسم المنتج <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                        value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="code">كود المنتج <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" 
                                        value="{{ old('code') }}" required>
                                    @error('code')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="category_id">التصنيف <span class="text-danger">*</span></label>
                                    <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                        <option value="">اختر التصنيف</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="unit">وحدة القياس <span class="text-danger">*</span></label>
                                    <select class="form-control @error('unit') is-invalid @enderror" id="unit" name="unit" required>
                                        <option value="">اختر الوحدة</option>
                                        <option value="قطعة" {{ old('unit') == 'قطعة' ? 'selected' : '' }}>قطعة</option>
                                        <option value="كجم" {{ old('unit') == 'كجم' ? 'selected' : '' }}>كيلوجرام</option>
                                        <option value="متر" {{ old('unit') == 'متر' ? 'selected' : '' }}>متر</option>
                                        <option value="لتر" {{ old('unit') == 'لتر' ? 'selected' : '' }}>لتر</option>
                                    </select>
                                    @error('unit')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="purchase_price">سعر الشراء <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('purchase_price') is-invalid @enderror" id="purchase_price" name="purchase_price" 
                                        value="{{ old('purchase_price') }}" 
                                        step="0.01" min="0" inputmode="decimal" placeholder="0.00" required>
                                    @error('purchase_price')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="selling_price">سعر البيع <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('selling_price') is-invalid @enderror" id="selling_price" name="selling_price" 
                                        value="{{ old('selling_price') }}" 
                                        step="0.01" min="0" inputmode="decimal" placeholder="0.00" required>
                                    @error('selling_price')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="current_stock">المخزون الحالي <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('current_stock') is-invalid @enderror" id="current_stock" name="current_stock" 
                                        value="{{ old('current_stock', '0') }}" 
                                        min="0" required>
                                    @error('current_stock')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="min_stock_level">الحد الأدنى للمخزون <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('min_stock_level') is-invalid @enderror" id="min_stock_level" name="min_stock_level" 
                                        value="{{ old('min_stock_level', '0') }}" 
                                        min="0" required>
                                    @error('min_stock_level')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="description">وصف المنتج</label>
                                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', isset($product) ? $product->description : '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">حفظ</button>
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto calculate selling price with 20% margin
    $('#purchase_price').on('input', function() {
        if (!$('#selling_price').val()) {
            var purchasePrice = parseFloat($(this).val()) || 0;
            var sellingPrice = purchasePrice * 1.2; // 20% margin
            $('#selling_price').val(sellingPrice.toFixed(2));
        }
    });
});
</script>
@endpush