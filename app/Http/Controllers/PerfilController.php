<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Docente;
use App\Models\Alumno;
use App\Models\Secretario;
use App\Models\User;
use App\Models\Postulante;

class PerfilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function actualizar_p(Request $request)
    {
        $usuario = Auth::user();
        $rol = lcfirst($usuario->roles()->pluck('name')->first());

        // Verificar y cambiar la contraseña si es necesario
        if ($request->filled('cambiar-contrasena')) {
            $this->validate($request, [
                'password_actual' => 'required',
                'password_nueva' => 'required|min:8|different:password_actual|confirmed',
            ]);

            if (!Hash::check($request->input('password_actual'), $usuario->password)) {
                return redirect()->back()->withErrors(['password_actual' => 'La contraseña actual es incorrecta']);
            }

            $usuario->password = Hash::make($request->input('password_nueva'));
            $request->session()->flash('exito', 'Perfil actualizado con éxito');
        }

        // Actualizar la imagen del usuario
        if ($request->hasFile('image')) {
            $imagenPath = "public/imagenes_usuarios";
        
            // Eliminar la imagen anterior si existe
            Storage::delete(str_replace('/storage/', 'public/', $usuario->image));
        
            // Asegurar que el directorio de destino exista, si no, créalo
            Storage::makeDirectory($imagenPath);
        
            // Guardar la nueva imagen
            $image = $request->file('image')->store($imagenPath);
            $usuario->image = str_replace('public/', '/storage/', $image);
        }
        
        $usuario->save();

        $changes = $usuario->getChanges();

        // Redirigir según los cambios realizados
        return !empty($changes)
        ? ($rol == 'administrador'
            ? redirect(route('dashboard_admin'))->with('exito', 'Perfil actualizado con éxito')
            : redirect(route("dashboard_$rol"))->with('exito', 'Perfil actualizado con éxito'))
        : redirect()->back()->withErrors([
            'general' => 'No se realizaron cambios en el perfil',
            'changes' => $changes
        ]);
    }

}
