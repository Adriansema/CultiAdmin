<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Faq extends Model
{
    use HasFactory;
    //app/Models/Faq.php

    // Definir qué campos se pueden llenar de manera masiva
    protected $fillable = ['question', 'answer'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($faq) {
            // Validar la pregunta y la respuesta
            $validator = Validator::make($faq->toArray(), [
                'question' => 'required|string|max:255',
                'answer'   => 'required|string',
            ]);

            if ($validator->fails()) {
                // Manejar los errores de validación, tal vez arrojar una excepción o devolver un mensaje
                throw new \Illuminate\Validation\ValidationException($validator);
            }
        });
    }

    public function user()
    {
        //Si algún día decides agregar usuarios que puedan
        //crear preguntas frecuentes,
        //puedes agregar una relación con el modelo User.
        return $this->belongsTo(User::class);
    }

    public function getQuestionAttribute($value)
    {
        return ucfirst($value); // Esto convierte la primera letra de la pregunta a mayúscula cuando se accede a ella.
    }

    public static function searchByKeyword($keyword)
    {
        return self::where('question', 'like', '%' . $keyword . '%')->get();
    } //Este método te permitirá buscar preguntas frecuentes por una palabra clave.

}
