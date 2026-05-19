<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OfflineController extends Controller
{
    public function index()
    {
        return view('offline');
    }
}
