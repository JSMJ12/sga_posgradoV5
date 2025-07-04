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
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
            ->where('status', 'ACTIVO')
            ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'El usuario no existe o no está activo.'
            ]);
        }

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ], $request->remember)) {

            return redirect()->route('inicio');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no son válidas.'
        ]);
    }


    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
