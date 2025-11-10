@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ isset($sale) ? 'تعديل فاتورة بيع' : 'إنشاء فاتورة بيع جديدة' }}
                    </h6>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ isset($sale) ? route('sales.update', $sale) : route('sales.store') }}" id="saleForm">
                        @csrf
                        @if(isset($sale))
                            @method('PUT')
                        @endif

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="invoice_number">رقم الفاتورة</label>
                                    <input type="text" class="form-control" id="invoice_number" name="invoice_number" 
                                        value="{{ old('invoice_number', isset($sale) ? $sale->invoice_number : $invoice_number) }}" 
                                        {{ isset($sale) ? 'readonly' : '' }} required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="customer_id">العميل</label>
                                    <select class="form-control" id="customer_id" name="customer_id" required>
                                        <option value="">اختر العميل</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" 
                                                data-balance="{{ $customer->balance }}"
                                                data-phone="{{ $customer->phone }}"
                                                data-email="{{ $customer->email }}"
                                                data-address="{{ $customer->address }}"
                                                {{ (old('customer_id', isset($sale) ? $sale->customer_id : '') == $customer->id) ? 'selected' : '' }}>
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Customer Details Card -->
                            <div class="col-12 mt-3" id="customer_details" style="display: none;">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">بيانات العميل</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>الرصيد الحالي:</strong>
                                                <span id="customer_balance" class="ms-2">0.00</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>رقم الهاتف:</strong>
                                                <span id="customer_phone" class="ms-2">-</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>البريد الإلكتروني:</strong>
                                                <span id="customer_email" class="ms-2">-</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>العنوان:</strong>
                                                <span id="customer_address" class="ms-2">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="warehouse_id">المخزن</label>
                                    <select class="form-control" id="warehouse_id" name="warehouse_id" required>
                                        <option value="">اختر المخزن</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ (old('warehouse_id', isset($sale) ? $sale->warehouse_id : '') == $warehouse->id) ? 'selected' : '' }}>
                                                {{ $warehouse->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sale_date">تاريخ البيع</label>
                                    <input type="date" class="form-control" id="sale_date" name="sale_date" 
                                        value="{{ old('sale_date', isset($sale) ? $sale->sale_date->format('Y-m-d') : date('Y-m-d')) }}" required>
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
                                                        <th>المخزون المتاح</th>
                                                        <th>الكمية</th>
                                                        <th>سعر الوحدة</th>
                                                        <th>الإجمالي</th>
                                                        <th>حذف</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($sale))
                                                        @foreach($sale->items as $index => $item)
                                                            <tr>
                                                                <td>
                                                                    <select name="items[{{ $index }}][product_id]" class="form-control product-select" required>
                                                                        @foreach($products as $product)
                                                                            <option value="{{ $product->id }}" 
                                                                                data-stock="{{ $product->current_stock }}"
                                                                                data-price="{{ $product->selling_price }}"
                                                                                {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                                                {{ $product->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <span class="available-stock">{{ $item->product->current_stock }}</span>
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
                                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', isset($sale) ? $sale->notes : '') }}</textarea>
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
                                                    value="{{ old('tax', isset($sale) ? $sale->tax : 0) }}" min="0" step="0.01">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">الخصم:</label>
                                            <div class="col-sm-8">
                                                <input type="number" class="form-control" name="discount" id="discount" 
                                                    value="{{ old('discount', isset($sale) ? $sale->discount : 0) }}" min="0" step="0.01">
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
                                                <select class="form-control" name="payment_status" id="payment_status">
                                                    <option value="unpaid" {{ old('payment_status', isset($sale) ? $sale->payment_status : '') == 'unpaid' ? 'selected' : '' }}>غير مدفوع</option>
                                                    <option value="partial" {{ old('payment_status', isset($sale) ? $sale->payment_status : '') == 'partial' ? 'selected' : '' }}>مدفوع جزئياً</option>
                                                    <option value="paid" {{ old('payment_status', isset($sale) ? $sale->payment_status : '') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Payment Details Section -->
                                        <div id="payment_details" style="display: none;">
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">المبلغ المدفوع:</label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control" name="paid_amount" id="paid_amount" 
                                                        value="{{ old('paid_amount', isset($sale) ? $sale->paid_amount : 0) }}" min="0" step="0.01">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label">طريقة الدفع:</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control" name="payment_method" id="payment_method">
                                                        <option value="cash">نقداً</option>
                                                        <option value="bank_transfer">تحويل بنكي</option>
                                                        <option value="check">شيك</option>
                                                        <option value="card">بطاقة ائتمان</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div id="bank_details" style="display: none;">
                                                <div class="row mb-3">
                                                    <label class="col-sm-4 col-form-label">اسم البنك:</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" name="bank_name">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label class="col-sm-4 col-form-label">رقم المرجع:</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" name="reference_number">
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="check_details" style="display: none;">
                                                <div class="row mb-3">
                                                    <label class="col-sm-4 col-form-label">رقم الشيك:</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" name="check_number">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label class="col-sm-4 col-form-label">تاريخ الشيك:</label>
                                                    <div class="col-sm-8">
                                                        <input type="date" class="form-control" name="check_date">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">حفظ</button>
                                <button type="button" class="btn btn-success" id="previewInvoice">
                                    <i class="fas fa-eye"></i> معاينة الفاتورة
                                </button>
                                <button type="button" class="btn btn-info" id="printInvoice">
                                    <i class="fas fa-print"></i> طباعة
                                </button>
                                <a href="{{ route('sales.index') }}" class="btn btn-secondary">إلغاء</a>
                            </div>
                        </div>

                        <!-- Invoice Preview Modal -->
                        <div class="modal fade" id="previewModal" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">معاينة الفاتورة</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body" id="invoice_preview">
                                        <!-- Invoice preview content will be loaded here -->
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                        <button type="button" class="btn btn-primary" id="printPreview">
                                            <i class="fas fa-print"></i> طباعة
                                        </button>
                                    </div>
                                </div>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2 and Bootstrap components
    $('.product-select').select2();
    var previewModal = new bootstrap.Modal(document.getElementById('previewModal'));

    // Add item row
    $('#addItem').click(function() {
        var index = $('#itemsTable tbody tr').length;
        var row = `
            <tr>
                <td>
                    <select name="items[${index}][product_id]" class="form-control product-select" required>
                        <option value="">اختر المنتج</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                data-stock="{{ $product->current_stock }}"
                                data-price="{{ $product->selling_price }}">
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <span class="available-stock">0</span>
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

    // Product selection change
    $(document).on('change', '.product-select', function() {
        var row = $(this).closest('tr');
        var option = $(this).find('option:selected');
        var stock = option.data('stock');
        var price = option.data('price');
        
        row.find('.available-stock').text(stock);
        row.find('.unit-price').val(price);
        calculateRowTotal(row);
    });

    // Calculate row total
    function calculateRowTotal(row) {
        var quantity = parseFloat(row.find('.quantity').val()) || 0;
        var unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
        var stock = parseFloat(row.find('.available-stock').text()) || 0;

        // Validate quantity against stock
        if (quantity > stock) {
            alert('الكمية المطلوبة أكبر من المخزون المتاح');
            row.find('.quantity').val(stock);
            quantity = stock;
        }

        var subtotal = quantity * unitPrice;
        row.find('.subtotal').text(subtotal.toFixed(2));
        calculateTotals();
    }

    // Calculate row subtotal on input change
    $(document).on('input', '.quantity, .unit-price', function() {
        calculateRowTotal($(this).closest('tr'));
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

    // Customer selection change
    $('#customer_id').change(function() {
        var option = $(this).find('option:selected');
        if (option.val()) {
            $('#customer_balance').text(parseFloat(option.data('balance')).toFixed(2));
            $('#customer_phone').text(option.data('phone') || '-');
            $('#customer_email').text(option.data('email') || '-');
            $('#customer_address').text(option.data('address') || '-');
            $('#customer_details').slideDown();
        } else {
            $('#customer_details').slideUp();
        }
    });

    // Payment status change
    $('#payment_status').change(function() {
        if ($(this).val() === 'paid' || $(this).val() === 'partial') {
            $('#payment_details').slideDown();
            if ($(this).val() === 'paid') {
                $('#paid_amount').val($('#total').val());
            }
        } else {
            $('#payment_details').slideUp();
        }
    });

    // Payment method change
    $('#payment_method').change(function() {
        $('#bank_details, #check_details').slideUp();
        if ($(this).val() === 'bank_transfer') {
            $('#bank_details').slideDown();
        } else if ($(this).val() === 'check') {
            $('#check_details').slideDown();
        }
    });

    // Preview Invoice
    $('#previewInvoice').click(function() {
        var invoiceHtml = generateInvoiceHtml();
        $('#invoice_preview').html(invoiceHtml);
        previewModal.show();
    });

    // Print Invoice
    $('#printInvoice, #printPreview').click(function() {
        var invoiceHtml = generateInvoiceHtml();
        var printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html dir="rtl">
                <head>
                    <title>فاتورة بيع</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        body { font-family: Arial, sans-serif; }
                        .invoice-header { border-bottom: 2px solid #ddd; margin-bottom: 20px; }
                        .invoice-footer { border-top: 2px solid #ddd; margin-top: 20px; }
                        @media print {
                            .no-print { display: none; }
                            a { text-decoration: none; }
                        }
                    </style>
                </head>
                <body>
                    ${invoiceHtml}
                    <button class="btn btn-primary no-print" onclick="window.print()">طباعة</button>
                </body>
            </html>
        `);
        printWindow.document.close();
    });

    // Generate Invoice HTML
    function generateInvoiceHtml() {
        var customer = $('#customer_id option:selected');
        var items = [];
        $('#itemsTable tbody tr').each(function() {
            var product = $(this).find('.product-select option:selected');
            var quantity = $(this).find('.quantity').val();
            var unitPrice = $(this).find('.unit-price').val();
            var subtotal = $(this).find('.subtotal').text();
            
            if (product.val()) {
                items.push({
                    product: product.text(),
                    quantity: quantity,
                    unitPrice: unitPrice,
                    subtotal: subtotal
                });
            }
        });

        return `
            <div class="container">
                <div class="invoice-header py-3">
                    <div class="row">
                        <div class="col-6">
                            <h2>فاتورة بيع</h2>
                            <p>رقم الفاتورة: ${$('#invoice_number').val()}</p>
                            <p>التاريخ: ${$('#sale_date').val()}</p>
                        </div>
                        <div class="col-6 text-start">
                            <h3>بيانات العميل</h3>
                            <p>الاسم: ${customer.text()}</p>
                            <p>الهاتف: ${customer.data('phone') || '-'}</p>
                            <p>العنوان: ${customer.data('address') || '-'}</p>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th>الكمية</th>
                                <th>سعر الوحدة</th>
                                <th>الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${items.map(item => `
                                <tr>
                                    <td>${item.product}</td>
                                    <td>${item.quantity}</td>
                                    <td>${parseFloat(item.unitPrice).toFixed(2)}</td>
                                    <td>${parseFloat(item.subtotal).toFixed(2)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>

                <div class="invoice-footer">
                    <div class="row">
                        <div class="col-6">
                            <p>ملاحظات:</p>
                            <p>${$('#notes').val() || '-'}</p>
                        </div>
                        <div class="col-6 text-start">
                            <p>المجموع الفرعي: ${$('#subtotal').val()}</p>
                            <p>الضريبة: ${$('#tax').val()}</p>
                            <p>الخصم: ${$('#discount').val()}</p>
                            <p><strong>الإجمالي: ${$('#total').val()}</strong></p>
                            <p>حالة الدفع: ${$('#payment_status option:selected').text()}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
});
</script>
@endpush