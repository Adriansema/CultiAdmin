<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NoRoleController extends Controller
{
    /**
     * Muestra la página para usuarios sin rol asignado.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('no-role-assigned'); // Esto cargará la vista resources/views/no-role-assigned.blade.php
    }
}