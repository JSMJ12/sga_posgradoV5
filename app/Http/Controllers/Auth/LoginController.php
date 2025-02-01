<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    public function login(Request $request)
    {
        // Validar el estado del usuario antes de intentar autenticar
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Buscar al usuario por correo electrónico y estado
        $user = User::where('email', $request->email)
                    ->where('status', 'ACTIVO')
                    ->first();

        // Verificar si se encontró un usuario activo
        if (!$user) {
            return redirect()->route('login')->withErrors([
                'email' => 'El usuario no existe o no está activo.'
            ]);
        }

        // Intentar autenticar al usuario con las credenciales proporcionadas
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            // Redirigir al usuario a la página correcta según su rol
            if (auth()->user()->hasRole('Administrador')) {
                return redirect()->route('dashboard_admin');
            } elseif (auth()->user()->hasRole('Docente')) {
                return redirect()->route('dashboard_docente');
            } elseif (auth()->user()->hasRole('Secretario')) {
                return redirect()->route('dashboard_secretario');
            } elseif (auth()->user()->hasRole('Alumno')) {
                return redirect()->route('dashboard_alumno');
            }elseif (auth()->user()->hasRole('Postulante')) {
                return redirect()->route('dashboard_postulante');
            }elseif (auth()->user()->hasRole('Secretario/a EPSU')) {
                return redirect()->route('dashboard_secretario_epsu');
            }
            
        }

        
        // Si la autenticación falla, mostrar un mensaje de error y volver a la página de inicio de sesión
        return redirect()->route('login')->withErrors([
            'email' => 'Las credenciales proporcionadas no son válidas.'
        ]);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
