<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard with business analytics.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $data = [
            'total_sales' => Sale::whereDate('sale_date', $today)->sum('total'),
            'total_purchases' => Purchase::whereDate('purchase_date', $today)->sum('total'),
            'monthly_sales' => Sale::whereBetween('sale_date', [$startOfMonth, now()])->sum('total'),
            'monthly_purchases' => Purchase::whereBetween('purchase_date', [$startOfMonth, now()])->sum('total'),
            'inventory_value' => Product::sum(DB::raw('current_stock * purchase_price')),
            'low_stock_products' => Product::whereRaw('current_stock <= min_stock_level')->count(),
            'total_customers' => Customer::count(),
            'total_suppliers' => Supplier::count(),
            'recent_sales' => Sale::with('customer')->latest()->take(5)->get(),
            'recent_purchases' => Purchase::with('supplier')->latest()->take(5)->get(),
            'top_products' => Product::with('category')->withCount('saleItems')->orderByDesc('sale_items_count')->take(5)->get(),
        ];

        return view('home', compact('data'));
    }
}
