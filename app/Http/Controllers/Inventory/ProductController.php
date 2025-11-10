<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->orderBy('name')
            ->paginate(10);
            
        return view('inventory.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('inventory.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'code' => 'required|unique:products',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'current_stock' => 'required|integer|min:0',
            'unit' => 'required'
        ], [
            'name.required' => 'اسم المنتج مطلوب',
            'name.max' => 'اسم المنتج يجب أن لا يتجاوز 255 حرف',
            'code.required' => 'كود المنتج مطلوب',
            'code.unique' => 'كود المنتج مستخدم مسبقاً',
            'category_id.required' => 'التصنيف مطلوب',
            'category_id.exists' => 'التصنيف المختار غير موجود',
            'purchase_price.required' => 'سعر الشراء مطلوب',
            'purchase_price.numeric' => 'سعر الشراء يجب أن يكون رقماً',
            'purchase_price.min' => 'سعر الشراء لا يمكن أن يكون سالباً',
            'selling_price.required' => 'سعر البيع مطلوب',
            'selling_price.numeric' => 'سعر البيع يجب أن يكون رقماً',
            'selling_price.min' => 'سعر البيع لا يمكن أن يكون سالباً',
            'min_stock_level.required' => 'الحد الأدنى للمخزون مطلوب',
            'min_stock_level.integer' => 'الحد الأدنى للمخزون يجب أن يكون رقماً صحيحاً',
            'min_stock_level.min' => 'الحد الأدنى للمخزون لا يمكن أن يكون سالباً',
            'current_stock.required' => 'المخزون الحالي مطلوب',
            'current_stock.integer' => 'المخزون الحالي يجب أن يكون رقماً صحيحاً',
            'current_stock.min' => 'المخزون الحالي لا يمكن أن يكون سالباً',
            'unit.required' => 'وحدة القياس مطلوبة'
        ]);

        Product::create($validatedData);
        return redirect()->route('products.index')->with('success', 'تم إضافة المنتج بنجاح');
    }

    public function show(Product $product)
    {
        $product->load('category');
        
        // Calculate totals
        $totalSales = $product->saleItems()
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->sum(DB::raw('sale_items.quantity * sale_items.unit_price'));
            
        $totalPurchases = $product->purchaseItems()
            ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
            ->sum(DB::raw('purchase_items.quantity * purchase_items.unit_price'));
            
        $soldQuantity = $product->saleItems()->sum('quantity');
        $purchasedQuantity = $product->purchaseItems()->sum('quantity');
        
        // Get stock movement history
        $stockMovements = DB::table('purchase_items')
            ->where('product_id', $product->id)
            ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
            ->select(
                'purchases.purchase_date as created_at',
                'purchase_items.quantity',
                DB::raw("'in' as type"),
                'purchases.invoice_number as reference',
                DB::raw("'شراء' as notes")
            )
            ->union(
                DB::table('sale_items')
                    ->where('product_id', $product->id)
                    ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                    ->select(
                        'sales.sale_date as created_at',
                        DB::raw('-sale_items.quantity as quantity'),
                        DB::raw("'out' as type"),
                        'sales.invoice_number as reference',
                        DB::raw("'بيع' as notes")
                    )
            )
            ->orderBy('created_at', 'desc')
            ->get();

        return view('inventory.products.show', compact('product', 'stockMovements', 'totalSales', 'totalPurchases', 'soldQuantity', 'purchasedQuantity'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('inventory.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'code' => 'required|unique:products,code,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'current_stock' => 'required|integer|min:0',
            'unit' => 'required'
        ], [
            'name.required' => 'اسم المنتج مطلوب',
            'code.required' => 'كود المنتج مطلوب',
            'code.unique' => 'كود المنتج مستخدم مسبقاً',
            'category_id.required' => 'التصنيف مطلوب',
            'category_id.exists' => 'التصنيف غير موجود',
            'purchase_price.required' => 'سعر الشراء مطلوب',
            'selling_price.required' => 'سعر البيع مطلوب',
            'min_stock_level.required' => 'الحد الأدنى للمخزون مطلوب',
            'current_stock.required' => 'المخزون الحالي مطلوب',
            'unit.required' => 'الوحدة مطلوبة'
        ]);

        $product->update($validatedData);
        return redirect()->route('products.show', $product->id)->with('success', 'تم تحديث المنتج بنجاح');
    }

    public function destroy(Product $product)
    {
        // Check if product is used in any transactions
        if ($product->purchaseItems()->exists() || $product->saleItems()->exists()) {
            return redirect()->route('products.index')
                ->with('error', 'لا يمكن حذف المنتج لأنه مرتبط بعمليات بيع أو شراء');
        }

        $product->forceDelete();
        return redirect()->route('products.index')->with('success', 'تم حذف المنتج بنجاح');
    }

    public function lowStock()
    {
        $products = Product::whereRaw('current_stock <= min_stock_level')
            ->with('category')
            ->paginate(10);

        return view('inventory.products.low-stock', compact('products'));
    }

    public function adjustStock(Request $request, Product $product)
    {
        $request->validate([
            'adjustment' => 'required|integer',
            'reason' => 'required|string'
        ]);

        $product->current_stock += $request->adjustment;
        $product->save();

        return redirect()->route('products.show', $product)
            ->with('success', 'تم تعديل المخزون بنجاح');
    }
}