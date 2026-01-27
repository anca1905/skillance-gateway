<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaGatewayController extends Controller
{
    public function index(){
        $user = Auth::id();
        return view('wa-gateway.index', compact('user'));
    }
}
