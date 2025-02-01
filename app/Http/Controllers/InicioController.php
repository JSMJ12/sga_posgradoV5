<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InicioController extends Controller
{
    public function redireccionarDashboard()
    { 
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
}
