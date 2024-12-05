<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->filled('brand')) {
            $query->where('brand', $request->input('brand'));
        }

        if ($request->filled('price_range')) {
            switch ($request->input('price_range')) {
                case 'under_100':
                    $query->where(function($query) {
                        $query->where('price_off', '<=', 100)
                              ->orWhereNull('price_off');
                    });
                    break;
                case '100_200':
                    $query->where(function($query) {
                        $query->whereBetween('price_off', [100, 200])
                              ->orWhereNull('price_off')
                              ->whereBetween('price', [100, 200]);
                    });
                    break;
                case 'over_200':
                    $query->where(function($query) {
                        $query->where('price_off', '>', 200)
                              ->orWhereNull('price_off')
                              ->where('price', '>', 200);
                    });
                    break;
            }
        }

        $products = $query->paginate(6);

        return view('products.index', compact('products'));
    }
}
