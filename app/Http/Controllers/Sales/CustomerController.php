<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->paginate(10);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'required|string|max:20|unique:customers,phone',
            'address' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
        ], [
            'name.required' => 'اسم العميل مطلوب',
            'name.max' => 'اسم العميل يجب أن لا يتجاوز 255 حرف',
            'email.email' => 'البريد الإلكتروني غير صالح',
            'email.unique' => 'البريد الإلكتروني مستخدم من قبل',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.unique' => 'رقم الهاتف مستخدم من قبل',
            'credit_limit.numeric' => 'حد الائتمان يجب أن يكون رقم',
            'credit_limit.min' => 'حد الائتمان يجب أن يكون أكبر من أو يساوي صفر',
        ]);

        try {
            $customer = Customer::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'credit_limit' => $validated['credit_limit'] ?? 0,
                'balance' => 0, // Initial balance is 0
            ]);

            return redirect()
                ->route('customers.index')
                ->with('success', 'تم إضافة العميل بنجاح');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة العميل. الرجاء المحاولة مرة أخرى.');
        }
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        
        // Calculate customer statistics
        $totalSales = $customer->sales()->sum('total');
        $invoiceCount = $customer->sales()->count();
        $recentSales = $customer->sales()
            ->latest('sale_date')
            ->take(5)
            ->get();

        return view('customers.show', compact('customer', 'totalSales', 'invoiceCount', 'recentSales'));
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $id,
            'phone' => 'required|string|max:20|unique:customers,phone,' . $id,
            'address' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
        ], [
            'name.required' => 'اسم العميل مطلوب',
            'name.max' => 'اسم العميل يجب أن لا يتجاوز 255 حرف',
            'email.email' => 'البريد الإلكتروني غير صالح',
            'email.unique' => 'البريد الإلكتروني مستخدم من قبل',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.unique' => 'رقم الهاتف مستخدم من قبل',
            'credit_limit.numeric' => 'حد الائتمان يجب أن يكون رقم',
            'credit_limit.min' => 'حد الائتمان يجب أن يكون أكبر من أو يساوي صفر',
        ]);

        try {
            $customer->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'credit_limit' => $validated['credit_limit'] ?? 0,
            ]);

            return redirect()
                ->route('customers.index')
                ->with('success', 'تم تحديث بيانات العميل بنجاح');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث بيانات العميل. الرجاء المحاولة مرة أخرى.');
        }
    }

    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            
            // Check if customer has any sales
            if ($customer->sales()->exists()) {
                return back()->with('error', 'لا يمكن حذف العميل لوجود فواتير مرتبطة به');
            }
            
            // Check if customer has balance
            if ($customer->balance > 0) {
                return back()->with('error', 'لا يمكن حذف العميل لوجود رصيد مستحق عليه');
            }

            $customer->delete();

            return redirect()
                ->route('customers.index')
                ->with('success', 'تم حذف العميل بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء حذف العميل');
        }
    }
}