<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        return view('suppliers.index');
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        // TODO: Implement supplier creation logic
        return redirect()->route('suppliers.index')->with('success', 'تم إضافة المورد بنجاح');
    }

    public function show($id)
    {
        return view('suppliers.show');
    }

    public function edit($id)
    {
        return view('suppliers.edit');
    }

    public function update(Request $request, $id)
    {
        // TODO: Implement supplier update logic
        return redirect()->route('suppliers.index')->with('success', 'تم تحديث بيانات المورد بنجاح');
    }

    public function destroy($id)
    {
        // TODO: Implement supplier deletion logic
        return redirect()->route('suppliers.index')->with('success', 'تم حذف المورد بنجاح');
    }
}