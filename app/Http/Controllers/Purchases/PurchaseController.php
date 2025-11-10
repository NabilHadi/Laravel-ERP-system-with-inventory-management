<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'warehouse'])
            ->orderBy('purchase_date', 'desc')
            ->paginate(10);

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        $warehouses = Warehouse::all();
        $invoice_number = 'PO-' . date('Ymd') . '-' . str_pad(Purchase::count() + 1, 4, '0', STR_PAD_LEFT);

        return view('purchases.create', compact('suppliers', 'products', 'warehouses', 'invoice_number'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'purchase_date' => 'required|date',
            'invoice_number' => 'required|unique:purchases,invoice_number',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'warehouse_id' => $request->warehouse_id,
                'purchase_date' => $request->purchase_date,
                'invoice_number' => $request->invoice_number,
                'subtotal' => 0,
                'tax' => $request->tax ?? 0,
                'discount' => $request->discount ?? 0,
                'total' => 0,
                'payment_status' => $request->payment_status ?? 'unpaid',
                'notes' => $request->notes,
            ]);

            $subtotal = 0;
            foreach ($request->items as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $itemSubtotal;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $itemSubtotal,
                ]);

                // Update product stock
                $product = Product::find($item['product_id']);
                $product->current_stock += $item['quantity'];
                $product->save();
            }

            $total = $subtotal + $request->tax - $request->discount;
            $purchase->update([
                'subtotal' => $subtotal,
                'total' => $total,
            ]);

            // Update supplier balance
            $supplier = Supplier::find($request->supplier_id);
            $supplier->balance += $total;
            $supplier->save();

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'تم إنشاء أمر الشراء بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء إنشاء أمر الشراء')->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'warehouse', 'items.product']);
        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        $warehouses = Warehouse::all();
        $purchase->load('items.product');

        return view('purchases.edit', compact('purchase', 'suppliers', 'products', 'warehouses'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Revert previous stock changes
            foreach ($purchase->items as $item) {
                $product = Product::find($item->product_id);
                $product->current_stock -= $item->quantity;
                $product->save();
            }

            // Remove old items
            $purchase->items()->delete();

            // Add new items
            $subtotal = 0;
            foreach ($request->items as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $itemSubtotal;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $itemSubtotal,
                ]);

                // Update product stock
                $product = Product::find($item['product_id']);
                $product->current_stock += $item['quantity'];
                $product->save();
            }

            $total = $subtotal + $request->tax - $request->discount;

            // Update supplier balance
            $oldSupplier = Supplier::find($purchase->supplier_id);
            $oldSupplier->balance -= $purchase->total;
            $oldSupplier->save();

            $newSupplier = Supplier::find($request->supplier_id);
            $newSupplier->balance += $total;
            $newSupplier->save();

            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'warehouse_id' => $request->warehouse_id,
                'purchase_date' => $request->purchase_date,
                'subtotal' => $subtotal,
                'tax' => $request->tax ?? 0,
                'discount' => $request->discount ?? 0,
                'total' => $total,
                'payment_status' => $request->payment_status ?? 'unpaid',
                'notes' => $request->notes,
            ]);

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'تم تحديث أمر الشراء بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء تحديث أمر الشراء')->withInput();
        }
    }

    public function destroy(Purchase $purchase)
    {
        try {
            DB::beginTransaction();

            // Revert stock changes
            foreach ($purchase->items as $item) {
                $product = Product::find($item->product_id);
                $product->current_stock -= $item->quantity;
                $product->save();
            }

            // Update supplier balance
            $supplier = Supplier::find($purchase->supplier_id);
            $supplier->balance -= $purchase->total;
            $supplier->save();

            // Delete the purchase and its items (cascade)
            $purchase->delete();

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'تم حذف أمر الشراء بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء حذف أمر الشراء');
        }
    }
}