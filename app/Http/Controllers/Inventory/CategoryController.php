<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->paginate(25);
        return view('inventory.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('inventory.categories.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string'
        ], [
            'name.required' => 'اسم التصنيف مطلوب',
            'name.unique' => 'اسم التصنيف مستخدم مسبقاً'
        ]);

        Category::create($validatedData);

        return redirect()->route('categories.index')
            ->with('success', 'تم إضافة التصنيف بنجاح');
    }

    public function show(Category $category)
    {
        // Get category statistics
        $productsCount = $category->products()->count();
        $totalSales = $category->products()
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->sum(DB::raw('sale_items.quantity * sale_items.unit_price'));
        
        $totalPurchases = $category->products()
            ->join('purchase_items', 'products.id', '=', 'purchase_items.product_id')
            ->sum(DB::raw('purchase_items.quantity * purchase_items.unit_price'));

        // Get products in this category with pagination
        $products = $category->products()
            ->orderBy('name')
            ->paginate(10);

        return view('inventory.categories.show', compact(
            'category', 
            'productsCount', 
            'totalSales', 
            'totalPurchases', 
            'products'
        ));
    }

    public function edit(Category $category)
    {
        return view('inventory.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string'
        ], [
            'name.required' => 'اسم التصنيف مطلوب',
            'name.unique' => 'اسم التصنيف مستخدم مسبقاً'
        ]);

        $category->update($validatedData);

        return redirect()->route('categories.index')
            ->with('success', 'تم تحديث التصنيف بنجاح');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'لا يمكن حذف التصنيف لأنه يحتوي على منتجات');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'تم حذف التصنيف بنجاح');
    }
}