<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table ='tbUsuario';
    protected $primaryKey = 'idUsuario';
    public $timestamps =true;

    protected $fillable =[
      'usuario',
      'password',  
    ];
    protected $hidden = ['password'];
}
