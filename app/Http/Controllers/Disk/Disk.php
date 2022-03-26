<?php

namespace App\Http\Controllers\Disk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Disk extends Controller
{
    public function index(Request $request)
    {
        return response()->json();
    } 
}
