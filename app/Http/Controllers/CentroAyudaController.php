<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faq;
use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Mail;

class CentroAyudaController extends Controller
{
    public function index()
    {
        return view('centroAyuda.index');
    }

    // Función para manejar la búsqueda de preguntas frecuentes
    public function searchFaq(Request $request)
    {
        // Validamos el parámetro de búsqueda
        $request->validate([
            'query' => 'nullable|string|max:255', // Se valida que sea una cadena de texto
        ]);

        $searchTerm = $request->input('query');

        // Si hay un término de búsqueda, lo usamos para filtrar las FAQ
        $faqs = Faq::query()
            ->where('question', 'like', '%' . $searchTerm . '%') // Filtramos por la pregunta
            ->orWhere('answer', 'like', '%' . $searchTerm . '%') // También podemos filtrar por la respuesta
            ->get();

        // Devolvemos los resultados en formato JSON
        return response()->json($faqs);
    }

    public function show($id)
    {
        // Este método podría usarse para mostrar un FAQ individual (opcional)
        $faq = Faq::findOrFail($id);
        return view('centroAyuda.show', compact('faq'));
    }

    public function submitContact(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'message' => 'required|string|max:1000',
        ]);

        // Enviar el correo
        Mail::to(env('MAIL_FROM_ADDRESS'))->send(new ContactFormMail($request->all()));

        return redirect()->route('centroAyuda.index')->with('success', '¡Tu mensaje fue enviado con éxito!');
    }
}
