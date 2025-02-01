<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maestria;
use App\Models\Docente;
use App\Models\User;

class MaestriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        $maestrias = Maestria::with('asignaturas', )->get();
        $docentes = Docente::all();
        return view('maestrias.index', compact('maestrias', 'docentes', 'perPage'));
    }

    public function store(Request $request)
    {   
        $maestria = new Maestria;
        $maestria->nombre = $request->input('nombre');
        $maestria->codigo = $request->input('codigo');
        $maestria->coordinador = $request->input('coordinador');
        $maestria->matricula = $request->input('matricula');
        $maestria->arancel = $request->input('arancel');
        $maestria->inscripcion = $request->input('inscripcion');
        $maestria->save();
        $coordinadorDNI = $request->input('coordinador');
        $docente = Docente::where('dni', $coordinadorDNI)->first();

        if ($docente) {
            $coordinadorEmail = $docente->email;
            $coordinadorUser = User::where('email', $coordinadorEmail)->first();

            if ($coordinadorUser) {
                $coordinadorUser->assignRole('Coordinador');
            }
        }
        session()->flash('success', 'La maestria se guardo con exito.');
        return redirect()->route('maestrias.index');
        

    }


    public function update(Request $request, Maestria $maestria)
    {
        // Obtenemos el DNI del nuevo coordinador
        $nuevoCoordinadorDNI = $request->input('coordinador');

        // Buscamos al docente correspondiente al nuevo coordinador
        $nuevoCoordinador = Docente::where('dni', $nuevoCoordinadorDNI)->first();

        if ($nuevoCoordinador) {
            // Obtenemos el email del nuevo coordinador
            $nuevoCoordinadorEmail = $nuevoCoordinador->email;

            // Buscamos al usuario correspondiente al nuevo coordinador en la tabla Users
            $nuevoCoordinadorUser = User::where('email', $nuevoCoordinadorEmail)->first();

            if ($nuevoCoordinadorUser) {
                // Asignamos el rol de coordinador al nuevo coordinador
                $nuevoCoordinadorUser->assignRole('Coordinador');
            }
        }

        // Quitamos el rol de coordinador al usuario anterior si existe
        $viejoCoordinadorDNI = $maestria->coordinador;
        $viejoCoordinador = Docente::where('dni', $viejoCoordinadorDNI)->first();
        
        if ($viejoCoordinador) {
            $viejoCoordinadorEmail = $viejoCoordinador->email;
            $viejoCoordinadorUser = User::where('email', $viejoCoordinadorEmail)->first();

            if ($viejoCoordinadorUser) {
                $viejoCoordinadorUser->removeRole('Coordinador');
            }
        }

        // Actualizamos la maestría
        $maestria->nombre = $request->input('nombre');
        $maestria->codigo = $request->input('codigo');
        $maestria->coordinador = $request->input('coordinador');
        $maestria->matricula = $request->input('matricula');
        $maestria->arancel = $request->input('arancel');
        $maestria->inscripcion = $request->input('inscripcion');
        $maestria->save();

        return redirect()->route('maestrias.index')->with('success', 'Maestría actualizada exitosamente.');
    }

    public function enable(Maestria $maestria)
    {
        $maestria->status = 'ACTIVO';
        $maestria->save();
    
        return redirect()->route('maestrias.index')->with('success', 'Maestria habilitada exitosamente.');
    }
    public function disable(Maestria $maestria)
    {
        $maestria->status = 'INACTIVO';
        $maestria->save();
    
        return redirect()->route('maestrias.index')->with('success', 'Maestria deshabilitada exitosamente.');
    }
}
