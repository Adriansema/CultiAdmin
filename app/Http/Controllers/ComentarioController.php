<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comentario;
use Carbon\Carbon;

class ComentarioController extends Controller
{
    public function index()
    {
        $comentarios = Comentario::latest()->get();
        return view('comentarios.index', compact('comentarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'contenido' => 'required|string|max:500',
        ]);
        
        Comentario::create([
            'contenido' => $request->contenido,
            'created_at' => now()
        ]);
        
        return redirect()->route('comentarios.index')->with('success', 'Comentario agregado');
    }

    public function destroy(Comentario $comentario)
    {
        $comentario->delete();
        return redirect()->route('comentarios.index')->with('success', 'Comentario eliminado');
    }

    public function filtrarPorMes($mes)
    {
        $comentarios = Comentario::whereMonth('created_at', $mes)->get();
        return view('comentarios.index', compact('comentarios'));
    }

    public function limpiar()
    {
        Comentario::where('created_at', '<', Carbon::now()->subMinutes(2))->delete();
        return redirect()->route('comentarios.index')->with('success', 'Comentarios antiguos eliminados');
    }
}
