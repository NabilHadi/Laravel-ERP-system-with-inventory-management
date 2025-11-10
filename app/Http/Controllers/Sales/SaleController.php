<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\PaymentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with(['customer', 'warehouse'])
            ->orderBy('sale_date', 'desc')
            ->paginate(10);

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::where('current_stock', '>', 0)->get();
        $warehouses = Warehouse::all();
        $invoice_number = 'INV-' . date('Ymd') . '-' . str_pad(Sale::count() + 1, 4, '0', STR_PAD_LEFT);

        return view('sales.create', compact('customers', 'products', 'warehouses', 'invoice_number'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'sale_date' => 'required|date',
            'invoice_number' => 'required|unique:sales,invoice_number',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'payment_status' => 'required|in:paid,partial,unpaid',
            'paid_amount' => 'required_if:payment_status,paid,partial|numeric|min:0',
            'payment_method' => 'required_if:payment_status,paid,partial|in:cash,bank_transfer,check,card',
            'bank_name' => 'required_if:payment_method,bank_transfer,check',
            'reference_number' => 'required_if:payment_method,bank_transfer',
            'check_number' => 'required_if:payment_method,check',
            'check_date' => 'required_if:payment_method,check|date',
        ]);

        try {
            DB::beginTransaction();

            // Check stock availability
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                if ($product->current_stock < $item['quantity']) {
                    throw new \Exception("الكمية المطلوبة غير متوفرة للمنتج: {$product->name}");
                }
            }

            $sale = Sale::create([
                'customer_id' => $request->customer_id,
                'warehouse_id' => $request->warehouse_id,
                'sale_date' => $request->sale_date,
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
                $product = Product::find($item['product_id']);
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $itemSubtotal;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $itemSubtotal,
                ]);

                // Update product stock
                $product->current_stock -= $item['quantity'];
                $product->save();
            }

            $total = $subtotal + $request->tax - $request->discount;
            $paid_amount = 0;

            // Handle payment if it's partial or paid
            if (in_array($request->payment_status, ['paid', 'partial']) && $request->filled('paid_amount')) {
                $paid_amount = min($request->paid_amount, $total);
                
                // Create payment record
                $payment = new Payment([
                    'payment_type' => 'sale_payment',
                    'reference_id' => $sale->id,
                    'amount' => $paid_amount,
                    'payment_method' => $request->payment_method,
                    'payment_date' => $request->sale_date,
                    'status' => 'completed',
                    'notes' => $request->notes
                ]);

                $sale->payments()->save($payment);

                // Create payment details if needed
                if (in_array($request->payment_method, ['bank_transfer', 'check'])) {
                    $detailsData = [];
                    if ($request->payment_method === 'bank_transfer') {
                        $detailsData = [
                            'bank_name' => $request->bank_name,
                            'reference_number' => $request->reference_number
                        ];
                    } else {
                        $detailsData = [
                            'check_number' => $request->check_number,
                            'check_date' => $request->check_date,
                            'bank_name' => $request->bank_name
                        ];
                    }
                    $payment->details()->create($detailsData);
                }
            }

            $sale->update([
                'subtotal' => $subtotal,
                'total' => $total,
                'paid_amount' => $paid_amount
            ]);

            // Update customer balance with remaining amount
            $customer = Customer::find($request->customer_id);
            $remaining = $total - $paid_amount;
            if ($remaining > 0) {
                $customer->balance += $remaining;
                $customer->save();
            }

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'تم إنشاء الفاتورة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'warehouse', 'items.product']);
        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $customers = Customer::all();
        $products = Product::all();
        $warehouses = Warehouse::all();
        $sale->load('items.product');

        return view('sales.edit', compact('sale', 'customers', 'products', 'warehouses'));
    }

    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'sale_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Revert previous stock changes and check new stock availability
            foreach ($sale->items as $oldItem) {
                $product = Product::find($oldItem->product_id);
                $product->current_stock += $oldItem->quantity;
                $product->save();
            }

            foreach ($request->items as $newItem) {
                $product = Product::find($newItem['product_id']);
                if ($product->current_stock < $newItem['quantity']) {
                    throw new \Exception("الكمية المطلوبة غير متوفرة للمنتج: {$product->name}");
                }
            }

            // Remove old items
            $sale->items()->delete();

            // Add new items
            $subtotal = 0;
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $itemSubtotal;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $itemSubtotal,
                ]);

                // Update product stock
                $product->current_stock -= $item['quantity'];
                $product->save();
            }

            $total = $subtotal + $request->tax - $request->discount;

            // Update customer balance
            $oldCustomer = Customer::find($sale->customer_id);
            if ($sale->payment_status !== 'paid') {
                $oldCustomer->balance -= $sale->total;
                $oldCustomer->save();
            }

            $newCustomer = Customer::find($request->customer_id);
            if ($request->payment_status !== 'paid') {
                $newCustomer->balance += $total;
                $newCustomer->save();
            }

            $sale->update([
                'customer_id' => $request->customer_id,
                'warehouse_id' => $request->warehouse_id,
                'sale_date' => $request->sale_date,
                'subtotal' => $subtotal,
                'tax' => $request->tax ?? 0,
                'discount' => $request->discount ?? 0,
                'total' => $total,
                'payment_status' => $request->payment_status ?? 'unpaid',
                'notes' => $request->notes,
            ]);

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'تم تحديث الفاتورة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(Sale $sale)
    {
        try {
            DB::beginTransaction();

            // Revert stock changes
            foreach ($sale->items as $item) {
                $product = Product::find($item->product_id);
                $product->current_stock += $item->quantity;
                $product->save();
            }

            // Update customer balance
            if ($sale->payment_status !== 'paid') {
                $customer = Customer::find($sale->customer_id);
                $customer->balance -= $sale->total;
                $customer->save();
            }

            // Delete the sale and its items (cascade)
            $sale->delete();

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'تم حذف الفاتورة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء حذف الفاتورة');
        }
    }
}