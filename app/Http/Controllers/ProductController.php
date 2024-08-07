<?php

namespace App\Http\Controllers;

use App\Imports\ProductsImport;
use App\Models\Product;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index()
    {
        return view('products', [
            'products' => Product::all(),
        ]);
    }

    public function importForm()
    {
        return view('import');
    }

    public function handleImport(Request $request)
    {
        $request->validate([
           'file' => 'required|file|mimes:xlsx,xls,csv|max:2048'
        ]);

        Excel::import(new ProductsImport, $request->file);

        return redirect()->back()->with('success', true);
    }

    public function show(Product $product)
    {
        return view('product', [
            'product' => $product,
        ]);
    }
}
