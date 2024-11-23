<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientEmbedController extends Controller
{
    public function wallet()
    {
        return view('client.wallet');
    }
}
