<?php

// app/Models/Boletin.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boletin extends Model
{
    use HasFactory;

    protected $table = 'boletins';

    protected $fillable = [ 'contenido', 'archivo', 'user_id', 'estado'];
}

