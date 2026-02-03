<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageManagerController extends Controller
{
    /**
     * Show the image manager page.
     */
    public function index()
    {
        return view('images.index');
    }

    /**
     * Show the upload image page.
     */
    public function create()
    {
        return view('images.create');
    }
}
