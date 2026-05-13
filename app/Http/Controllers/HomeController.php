<?php

namespace App\Http\Controllers;

use App\Services\ProductService;

class HomeController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        if (auth()->guest()) {
            return view('home.index');
        }
        
        $statistics = $this->productService->getHomeStatistics();
        
        return view('home.index', $statistics);
    }
}