@extends('layouts.app')

@section('title', 'تفاصيل العميل')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">تفاصيل العميل</h3>
                    <div>
                        <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">اسم العميل</th>
                                    <td>{{ $customer->name }}</td>
                                </tr>
                                <tr>
                                    <th>رقم الهاتف</th>
                                    <td>{{ $customer->phone }}</td>
                                </tr>
                                <tr>
                                    <th>البريد الإلكتروني</th>
                                    <td>{{ $customer->email ?: 'غير متوفر' }}</td>
                                </tr>
                                <tr>
                                    <th>العنوان</th>
                                    <td>{{ $customer->address ?: 'غير متوفر' }}</td>
                                </tr>
                                <tr>
                                    <th>الرصيد</th>
                                    <td>{{ number_format($customer->balance, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>حد الائتمان</th>
                                    <td>{{ number_format($customer->credit_limit, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>إحصائيات العميل</h4>
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
                                            <div class="border-right-warning p-3">
                                                <h5>عدد الفواتير</h5>
                                                <h3>{{ $invoiceCount }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Sales -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>آخر المبيعات</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>رقم الفاتورة</th>
                                            <th>التاريخ</th>
                                            <th>المبلغ</th>
                                            <th>حالة الدفع</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentSales as $sale)
                                            <tr>
                                                <td>{{ $sale->invoice_number }}</td>
                                                <td>{{ $sale->sale_date->format('Y-m-d') }}</td>
                                                <td>{{ number_format($sale->total, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $sale->payment_status === 'paid' ? 'success' : ($sale->payment_status === 'partial' ? 'warning' : 'danger') }}">
                                                        {{ $sale->payment_status === 'paid' ? 'مدفوع' : ($sale->payment_status === 'partial' ? 'مدفوع جزئياً' : 'غير مدفوع') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">لا توجد مبيعات</td>
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