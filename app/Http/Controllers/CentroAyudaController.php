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

    public function showContactForm()
    {
        return view('centroAyuda.contactForm');
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
