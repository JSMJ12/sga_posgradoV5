<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\BienvenidaNotificacion;


class LoginController extends Controller
{

    use AuthenticatesUsers;

    public function login(Request $request)
    {
        // Validar entrada
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
    
        // Verificar que el usuario esté activo
        $user = User::where('email', $request->email)
            ->where('status', 'ACTIVO')
            ->first();
    
        if (!$user) {
            return back()->withErrors([
                'email' => 'El usuario no existe o no está activo.'
            ]);
        }
    
        // Intentar autenticar
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ], $request->remember)) {
    
            // Redirección según rol
            $routes = [
                'Administrador' => 'dashboard_admin',
                'Docente' => 'dashboard_docente',
                'Secretario' => 'dashboard_secretario',
                'Alumno' => 'dashboard_alumno',
                'Postulante' => 'dashboard_postulante',
                'Secretario/a EPSU' => 'dashboard_secretario_epsu',
            ];
    
            foreach ($routes as $role => $route) {
                if (auth()->user()->hasRole($role)) {
                    // Redirigir al dashboard correspondiente
                    return redirect()->route($route);
                }
            }
        }
    
        // Fallo de autenticación
        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no son válidas.'
        ]);
    }
    


    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
