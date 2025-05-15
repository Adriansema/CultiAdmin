<?php

// app/Models/IntentoAcceso.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntentoAcceso extends Model
{

    protected $table = 'intentos_acceso';

    protected $fillable = ['user_id', 'email', 'ip_address', 'user_agent'];
}

