<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PerfilAlumnoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function edit()
    {
        $user = Auth::user();
        $provincias = ['Azuay', 'Bolívar', 'Cañar', 'Carchi', 'Chimborazo', 'Cotopaxi', 'El Oro', 'Esmeraldas', 'Galápagos', 'Guayas', 'Imbabura', 'Loja', 'Los Ríos', 'Manabí', 'Morona Santiago', 'Napo', 'Orellana', 'Pastaza', 'Pichincha', 'Santa Elena', 'Santo Domingo de los Tsáchilas', 'Sucumbíos', 'Tungurahua', 'Zamora Chinchipe'];
        $estadosCiviles = [
            'Soltero/a',
            'Casado/a',
            'Divorciado/a',
            'Viudo/a',
            'Separado/a',
            'Unión libre'
        ];
        $tipo_colegio = [
            'FISCAL',
            'FISCOMISIONAL',
            'PARTICULAR',
            'MUNICIPAL',
            'EXTRANJERO',
            'NO REGISTRA'
        ];
        $ingreso_hogar = [
            'RANGO 1 - HASTA 1 SBU',
            'RANGO 2 - MÁS DE 1 A MENOS DE 2 SBU',
            'RANGO 3 - MÁS DE 2 A MENOS DE 3 SBU',
            'RANGO 4 - MÁS DE 3 A MENOS DE 4 SBU',
            'RANGO 5 - MÁS DE 4 A MENOS DE 5 SBU',
            'RANGO 6 - MÁS DE 5 A MENOS DE 6 SBU',
            'RANGO 7 - MÁS DE 6 A MENOS DE 7 SBU',
            'RANGO 8 - MÁS DE 7 A MENOS DE 8 SBU',
            'RANGO 9 - MÁS DE 8 A MENOS DE 9 SBU',
            'RANGO 10-DE 9 EN ADELANTE',
            'NO REGISTRA'
        ];
        $formacion_padre = [
            'NINGUNO',
            'CENTRO DE ALFABETIZACIÓN',
            'JARDIN INFANTES',
            'EDUCACIÓN BÁSICA',
            'EDUCACIÓN MEDIA',
            'SUPERIOR NO UNIVERSITARIA COMPLETA',
            'SUPERIOR NO UNIVERSITARIA INCOMPLETA',
            'SUPERIOR UNIVERSITARIA COMPLETA',
            'SUPERIOR UNIVERSITARIA INCOMPLETA',
            'DIPLOMADO',
            'ESPECIALIDAD',
            'POSGRADO MAESTRÍA',
            'POSGRADO ESPECIALIDAD ÁREA SALUD',
            'POSGRADO PHD',
            'NO SABE',
            'NO REGISTRA'
        ];
        $origen_recursos = [
            'RECURSOS PROPIOS',
            'PADRES TUTORES',
            'PAREJA SENTIMENTAL',
            'HERMANOS',
            'OTROS MIEMBROS DEL HOGAR',
            'OTROS FAMILIARES',
            'BECA ESTUDIO',
            'CRÉDITO EDUCATIVO',
            'NO REGISTRA'
        ];
        $alumno = Alumno::where('email_institucional', $user->email)->firstOrFail();

        return view('perfiles.alumno', compact('alumno', 'estadosCiviles', 'provincias', 'tipo_colegio', 'ingreso_hogar', 'formacion_padre', 'origen_recursos'));
    }

    public function update(Request $request)
    {
        $request->validate([
            
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $user = Auth::user();
        $alumno = Alumno::where('email_institucional', $user->email)->firstOrFail();

        // Actualizar todos los datos excepto la imagen
        $alumno->update($request->except(['image', 'discapacidad']));

        // Manejo de la imagen
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($alumno->image) {
                \Storage::disk('public')->delete($alumno->image);
            }

            // Guardar nueva imagen
            $path = $request->file('image')->store('imagenes_usuarios', 'public');
            $alumno->image = $path;
            $alumno->save();

            // También actualizar la imagen en la tabla `users`
            $usuario = User::where('email', $alumno->email_institucional)->first();
            if ($usuario) {
                $usuario->image = $path;
                $usuario->save();
            }
        }

        return redirect()->route('edit_datosAlumnos')->with('success', 'Perfil actualizado con éxito.');
    }
}
