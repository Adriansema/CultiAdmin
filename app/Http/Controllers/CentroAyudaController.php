<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CentroAyudaController extends Controller
{
    public function index()
    {
        return view('centroAyuda.index');
    }
}
