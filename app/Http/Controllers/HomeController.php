<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (Auth::check()) {
            if (auth()->user()->hasRole('Administrador')) {
                return redirect()->route('dashboard_admin');
            } elseif (auth()->user()->hasRole('Docente')) {
                return redirect()->route('dashboard_docente');
            } elseif (auth()->user()->hasRole('Secretario')) {
                return redirect()->route('dashboard_secretario');
            } elseif (auth()->user()->hasRole('Alumno')) {
                return redirect()->route('dashboard_alumno');
            } elseif (auth()->user()->hasRole('Postulante')) {
                return redirect()->route('dashboard_postulante');
            }
        }
    }

}
