@extends('layouts.app')

@section('title', 'تعديل المنتج')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">تعديل المنتج</h3>
                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right"></i> رجوع
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.update', $product->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name">اسم المنتج<span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $product->name) }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="code">كود المنتج<span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('code') is-invalid @enderror" 
                                           id="code" 
                                           name="code" 
                                           value="{{ old('code', $product->code) }}" 
                                           required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="category_id">التصنيف</label>
                                    <select class="form-control @error('category_id') is-invalid @enderror" 
                                            id="category_id" 
                                            name="category_id">
                                        <option value="">اختر التصنيف</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="min_stock_level">الحد الأدنى للمخزون</label>
                                    <input type="number" 
                                           class="form-control @error('min_stock_level') is-invalid @enderror" 
                                           id="min_stock_level" 
                                           name="min_stock_level" 
                                           value="{{ old('min_stock_level', $product->min_stock_level) }}"
                                           min="0">
                                    @error('min_stock_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="selling_price">سعر البيع<span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('selling_price') is-invalid @enderror" 
                                           id="selling_price" 
                                           name="selling_price" 
                                           value="{{ old('selling_price', $product->selling_price) }}"
                                           step="0.01"
                                           min="0"
                                           required>
                                    @error('selling_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="purchase_price">سعر الشراء<span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('purchase_price') is-invalid @enderror" 
                                           id="purchase_price" 
                                           name="purchase_price" 
                                           value="{{ old('purchase_price', $product->purchase_price) }}"
                                           step="0.01"
                                           min="0"
                                           required>
                                    @error('purchase_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="current_stock">المخزون الحالي<span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('current_stock') is-invalid @enderror" 
                                           id="current_stock" 
                                           name="current_stock" 
                                           value="{{ old('current_stock', $product->current_stock) }}"
                                           min="0"
                                           required>
                                    @error('current_stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="unit">الوحدة<span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('unit') is-invalid @enderror" 
                                           id="unit" 
                                           name="unit" 
                                           value="{{ old('unit', $product->unit) }}"
                                           required>
                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                                <div class="form-group mb-3">
                                    <label for="description">الوصف</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="3">{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- صورة المنتج (image) field removed -->
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> حفظ التغييرات
                                </button>
                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> إلغاء
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-group label {
        font-weight: 600;
    }
    .invalid-feedback {
        display: block;
    }
</style>
@endpush