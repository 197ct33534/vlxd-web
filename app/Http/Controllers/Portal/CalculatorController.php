<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;

class CalculatorController extends Controller
{
    public function index()
    {
        return view('portal.calculator');
    }
}
