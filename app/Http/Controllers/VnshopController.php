<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VnshopController extends Controller
{
    public function login()
    {
        return view('login');
    }
    public function dashboard()
    {
        return view('dashboard.dashboard');
    }
    public function store()
    {
        return view('stores.list_store');
    }
    public function productWaitingApproval()
    {
        return view('products.list_product');
    }
}
