<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\QueryException;
use App\Models\Usuario;

class AuthController extends Controller
{
    public function mostrarFormularioLogin()
    {
        return view('auth.login');
    }

    public function mostrarFormularioRegistro()
    {
        return view('auth.register');
    }

    public function registrar(Request $request)
    {
        // $request->validate([
        //     'usuario' => 'required|string|max:50|unique:tbUsuarios,usuario',
        //     'password' => 'required|string|min:4'
        // ]);
    
        $usuario = $request->input('usuario');
        $password = $request->input('password');
        if (empty($usuario) || empty($password)) {
            return back()->withErrors(['credenciales' => 'Usuario o contraseña no cumplen con los requisitos.']);
        }
    
        try {
            // Ejecutamos el insert con protección (validación en la función SQL Server)
            DB::insert("
                INSERT INTO tbUsuarios (usuario, password, created_at, updated_at)
                VALUES (?, dbo.protect(?), DEFAULT, DEFAULT)
            ", [$usuario, $password]);
    
            // Verificamos si se insertó correctamente (usamos una simple consulta)
            $insertado = DB::selectOne("SELECT COUNT(*) as total FROM tbUsuarios WHERE usuario = ?", [$usuario]);
    
            if ($insertado->total == 0) {
                // Probablemente dbo.protect devolvió NULL y el trigger evitó la inserción
                return back()->withErrors(['credenciales' => 'Usuario o contraseña no cumplen con los requisitos.']);
            }
    
            return redirect()->route('login')->with('success', 'Usuario creado con éxito, inicie sesión');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withErrors(['credenciales' => 'Error al registrar usuario. Verifique los datos.']);
        }
    }
    

    public function login(Request $request)
    {
        $request->validate([
            'usuario' => 'required|string',
            'password' => 'required|string'
        ]);
    
        $usuario = $request->input('usuario');
        $password = $request->input('password');
    
        try {
            $resultado = DB::selectOne('SELECT dbo.validar_login(?, ?) AS valido', [$usuario, $password]);
    
            if ($resultado && $resultado->valido) {
                $user = DB::table('tbUsuarios')->where('usuario', $usuario)->first();                
                Session::put('idUsuario', $user->idUsuario);
                Session::put('usuario', $user->usuario);
                return redirect()->route('dashboard');
            } else {
                return back()->withErrors(['credenciales' => 'Usuario o contraseña incorrectos.']);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withErrors(['credenciales' => 'Error al validar credenciales.']);
        }
    }
    

    public function logout()
    {
        Session::flush();
        return redirect()->route('login');
    }
}
