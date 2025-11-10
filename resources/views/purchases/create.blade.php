@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ isset($purchase) ? 'تعديل أمر شراء' : 'إنشاء أمر شراء جديد' }}
                    </h6>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ isset($purchase) ? route('purchases.update', $purchase) : route('purchases.store') }}" id="purchaseForm">
                        @csrf
                        @if(isset($purchase))
                            @method('PUT')
                        @endif

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="invoice_number">رقم الفاتورة</label>
                                    <input type="text" class="form-control" id="invoice_number" name="invoice_number" 
                                        value="{{ old('invoice_number', isset($purchase) ? $purchase->invoice_number : $invoice_number) }}" 
                                        {{ isset($purchase) ? 'readonly' : '' }} required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="supplier_id">المورد</label>
                                    <select class="form-control" id="supplier_id" name="supplier_id" required>
                                        <option value="">اختر المورد</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ (old('supplier_id', isset($purchase) ? $purchase->supplier_id : '') == $supplier->id) ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="warehouse_id">المخزن</label>
                                    <select class="form-control" id="warehouse_id" name="warehouse_id" required>
                                        <option value="">اختر المخزن</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ (old('warehouse_id', isset($purchase) ? $purchase->warehouse_id : '') == $warehouse->id) ? 'selected' : '' }}>
                                                {{ $warehouse->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="purchase_date">تاريخ الشراء</label>
                                    <input type="date" class="form-control" id="purchase_date" name="purchase_date" 
                                        value="{{ old('purchase_date', isset($purchase) ? $purchase->purchase_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        المنتجات
                                        <button type="button" class="btn btn-primary btn-sm float-end" id="addItem">
                                            <i class="fas fa-plus"></i> إضافة منتج
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table" id="itemsTable">
                                                <thead>
                                                    <tr>
                                                        <th>المنتج</th>
                                                        <th>الكمية</th>
                                                        <th>سعر الوحدة</th>
                                                        <th>الإجمالي</th>
                                                        <th>حذف</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($purchase))
                                                        @foreach($purchase->items as $index => $item)
                                                            <tr>
                                                                <td>
                                                                    <select name="items[{{ $index }}][product_id]" class="form-control product-select" required>
                                                                        @foreach($products as $product)
                                                                            <option value="{{ $product->id }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                                                {{ $product->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity" 
                                                                        value="{{ $item->quantity }}" min="1" required>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="items[{{ $index }}][unit_price]" class="form-control unit-price" 
                                                                        value="{{ $item->unit_price }}" step="0.01" min="0" required>
                                                                </td>
                                                                <td>
                                                                    <span class="subtotal">{{ number_format($item->subtotal, 2) }}</span>
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-danger btn-sm remove-item">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notes">ملاحظات</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', isset($purchase) ? $purchase->notes : '') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">المجموع الفرعي:</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="subtotal" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">الضريبة:</label>
                                            <div class="col-sm-8">
                                                <input type="number" class="form-control" name="tax" id="tax" 
                                                    value="{{ old('tax', isset($purchase) ? $purchase->tax : 0) }}" min="0" step="0.01">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">الخصم:</label>
                                            <div class="col-sm-8">
                                                <input type="number" class="form-control" name="discount" id="discount" 
                                                    value="{{ old('discount', isset($purchase) ? $purchase->discount : 0) }}" min="0" step="0.01">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">الإجمالي:</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="total" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">حالة الدفع:</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" name="payment_status">
                                                    <option value="unpaid" {{ old('payment_status', isset($purchase) ? $purchase->payment_status : '') == 'unpaid' ? 'selected' : '' }}>غير مدفوع</option>
                                                    <option value="partial" {{ old('payment_status', isset($purchase) ? $purchase->payment_status : '') == 'partial' ? 'selected' : '' }}>مدفوع جزئياً</option>
                                                    <option value="paid" {{ old('payment_status', isset($purchase) ? $purchase->payment_status : '') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">حفظ</button>
                                <a href="{{ route('purchases.index') }}" class="btn btn-secondary">إلغاء</a>
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
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.product-select').select2();

    // Add item row
    $('#addItem').click(function() {
        var index = $('#itemsTable tbody tr').length;
        var row = `
            <tr>
                <td>
                    <select name="items[${index}][product_id]" class="form-control product-select" required>
                        <option value="">اختر المنتج</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="items[${index}][quantity]" class="form-control quantity" min="1" required>
                </td>
                <td>
                    <input type="number" name="items[${index}][unit_price]" class="form-control unit-price" step="0.01" min="0" required>
                </td>
                <td>
                    <span class="subtotal">0.00</span>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#itemsTable tbody').append(row);
        $('.product-select').select2();
    });

    // Remove item row
    $(document).on('click', '.remove-item', function() {
        $(this).closest('tr').remove();
        calculateTotals();
    });

    // Calculate row subtotal
    $(document).on('input', '.quantity, .unit-price', function() {
        var row = $(this).closest('tr');
        var quantity = parseFloat(row.find('.quantity').val()) || 0;
        var unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
        var subtotal = quantity * unitPrice;
        row.find('.subtotal').text(subtotal.toFixed(2));
        calculateTotals();
    });

    // Calculate totals
    function calculateTotals() {
        var subtotal = 0;
        $('.subtotal').each(function() {
            subtotal += parseFloat($(this).text()) || 0;
        });
        var tax = parseFloat($('#tax').val()) || 0;
        var discount = parseFloat($('#discount').val()) || 0;
        var total = subtotal + tax - discount;

        $('#subtotal').val(subtotal.toFixed(2));
        $('#total').val(total.toFixed(2));
    }

    // Recalculate on tax/discount change
    $('#tax, #discount').on('input', calculateTotals);

    // Initial calculation
    calculateTotals();
});
</script>
@endpush