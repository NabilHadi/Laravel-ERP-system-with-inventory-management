@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">تفاصيل أمر الشراء</h6>
                    <div>
                        <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <a href="#" class="btn btn-primary btn-sm" onclick="window.print()">
                            <i class="fas fa-print"></i> طباعة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">معلومات المورد</h5>
                            <p><strong>اسم المورد:</strong> {{ $purchase->supplier->name }}</p>
                            <p><strong>رقم الهاتف:</strong> {{ $purchase->supplier->phone }}</p>
                            <p><strong>العنوان:</strong> {{ $purchase->supplier->address }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h5 class="mb-3">معلومات الفاتورة</h5>
                            <p><strong>رقم الفاتورة:</strong> {{ $purchase->invoice_number }}</p>
                            <p><strong>تاريخ الشراء:</strong> {{ $purchase->purchase_date->format('Y-m-d') }}</p>
                            <p><strong>المخزن:</strong> {{ $purchase->warehouse->name }}</p>
                        </div>
                    </div>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>المنتج</th>
                                    <th>الكمية</th>
                                    <th>سعر الوحدة</th>
                                    <th>الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchase->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->unit_price, 2) }}</td>
                                        <td>{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>المجموع الفرعي:</strong></td>
                                    <td>{{ number_format($purchase->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>الضريبة:</strong></td>
                                    <td>{{ number_format($purchase->tax, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>الخصم:</strong></td>
                                    <td>{{ number_format($purchase->discount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>الإجمالي:</strong></td>
                                    <td>{{ number_format($purchase->total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">معلومات إضافية</h5>
                            <p><strong>حالة الدفع:</strong> 
                                <span class="badge {{ $purchase->payment_status === 'paid' ? 'bg-success' : ($purchase->payment_status === 'partial' ? 'bg-warning' : 'bg-danger') }}">
                                    {{ $purchase->payment_status === 'paid' ? 'مدفوع' : ($purchase->payment_status === 'partial' ? 'مدفوع جزئياً' : 'غير مدفوع') }}
                                </span>
                            </p>
                            @if($purchase->notes)
                                <p><strong>ملاحظات:</strong><br>{{ $purchase->notes }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<style>
    @media print {
        .navbar, .card-header, .btn {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush
@endsection