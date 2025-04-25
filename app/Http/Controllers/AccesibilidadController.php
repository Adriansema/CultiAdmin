<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccesibilidadController extends Controller
{
    /**
     * Muestra la vista de accesibilidad.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('accesibilidad.index');
    }
}
