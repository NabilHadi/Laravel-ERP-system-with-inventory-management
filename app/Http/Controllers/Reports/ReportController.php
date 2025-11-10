<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function sales()
    {
        // TODO: Implement sales report logic
        return view('reports.sales');
    }

    public function purchases()
    {
        // TODO: Implement purchases report logic
        return view('reports.purchases');
    }

    public function inventory()
    {
        // TODO: Implement inventory report logic
        return view('reports.inventory');
    }

    public function customers()
    {
        // TODO: Implement customers report logic
        return view('reports.customers');
    }

    public function suppliers()
    {
        // TODO: Implement suppliers report logic
        return view('reports.suppliers');
    }
}