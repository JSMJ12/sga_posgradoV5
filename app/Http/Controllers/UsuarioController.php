<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        if ($request->ajax()) {
            $data = User::with('roles')->select('users.*');
            return DataTables::of($data)
                ->addColumn('foto', function ($row) {
                    return '<img src="' . asset('storage/' . $row->image) . '" alt="Imagen de ' . $row->name . '" style="max-width: 60px; border-radius: 50%;" loading="lazy">';
                })
                ->addColumn('roles', function ($row) {
                    return $row->roles->map(function ($role) {
                        return $role->name;
                    })->implode(', ');
                })
                ->addColumn('mensajeria', function ($row) {
                    return '<button type="button" 
                                        class="btn btn-outline-info btn-sm btn-message"  
                                        data-id="' . $row->id . '" 
                                        data-nombre="' . $row->name . '" 
                                        data-toggle="modal" 
                                        data-target="#sendMessageModal" 
                                        title="Enviar mensaje">
                                <i class="fas fa-envelope"></i>
                            </button>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('usuarios.edit', $row->id) . '" class="btn btn-outline-primary btn-sm" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>';

                    if ($row->status == 'ACTIVO') {
                        $btn .= '<form action="' . route('usuarios.disable', $row->id) . '" method="POST" style="display: inline;">
                                    ' . csrf_field() . '
                                    ' . method_field('PUT') . '
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Deshabilitar">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>';
                    } else {
                        $btn .= '<form action="' . route('usuarios.enable', $row->id) . '" method="POST" style="display: inline;">
                                    ' . csrf_field() . '
                                    ' . method_field('PUT') . '
                                    <button type="submit" class="btn btn-outline-success btn-sm" title="Reactivar">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                </form>';
                    }

                    return $btn;
                })
                ->rawColumns(['foto', 'mensajeria', 'action']) // Permitir HTML sin escapar para estas columnas
                ->make(true);
        }

        return view('usuarios.index', compact('perPage'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $usuario = new User();

        $usuario->name = $request->input('usu_nombre');
        $usuario->apellido = $request->input('usu_apellido');
        $usuario->sexo = $request->input('usu_sexo');
        $usuario->email = $request->input('email');
        $usuario->password = bcrypt($request->input('usu_contrasena'));
        $usuario->status = $request->input('usu_estatus', 'ACTIVO');
        $request->validate([
            'usu_foto' => 'nullable|image|max:2048',
        ]);


        $primeraLetra = substr($usuario->name, 0, 1);

        // Almacenar la imagen
        if ($request->hasFile('usu_foto')) {
            $path = $request->file('usu_foto')->store('imagenes_usuarios', 'public');
            $usuario->image = $path;
        } else {
            $usuario->image = 'https://ui-avatars.com/api/?name=' . urlencode($primeraLetra);
        }

        $usuario->save();
        $usuario->roles()->sync($request->roles);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $usuario)
    {
        $roles = Role::all();
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $usuario->name = $request->input('usu_nombre');
        $usuario->apellido = $request->input('usu_apellido');
        $usuario->sexo = $request->input('usu_sexo');
        $usuario->email = $request->input('email');

        if ($request->input('usu_contrasena')) {
            $usuario->password = bcrypt($request->input('usu_contrasena'));
        }

        $usuario->status = $request->input('usu_estatus', 'ACTIVO');

        $request->validate([
            'usu_foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('usu_foto')) {
            // Eliminar la imagen anterior si existe
            if ($usuario->image) {
                Storage::disk('public')->delete($usuario->image);
            }

            $path = $request->file('usu_foto')->store('imagenes_usuarios', 'public');
            $usuario->image = $path;
        }

        $usuario->save();
        $usuario->roles()->sync($request->roles);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }


    public function checkUserOneStatus()
    {
        $user1 = User::find(1);

        if ($user1 && $user1->status === 'INACTIVO') {
            User::where('id', '<>', 1)->update(['status' => 'INACTIVO']);
            return redirect()->route('usuarios.index')->with('success', 'Todos los usuarios han sido deshabilitados porque el usuario principal está inactivo.');
        } else {
            User::where('id', '<>', 1)->update(['status' => 'ACTIVO']);
            return redirect()->route('usuarios.index')->with('success', 'Todos los usuarios han sido habilitados porque el usuario principal está activo.');
        }
    }

    public function disable(User $usuario)
    {
        $this->checkUserOneStatus();
        if ($usuario->id !== 1) {
            $usuario->status = 'INACTIVO';
            $usuario->save();

            return redirect()->route('usuarios.index')->with('success', 'Usuario deshabilitado exitosamente.');
        } else {
            return redirect()->route('usuarios.index')->with('error', 'No se puede deshabilitar al usuario con ID 1.');
        }
    }

    public function enable(User $usuario)
    {
        $usuario->status = 'ACTIVO';
        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuario habilitado exitosamente.');
    }

    public function actualizarPerfil(Request $request)
    {
        $user = User::find(Auth::id());
    
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password' => 'nullable|min:8|confirmed',
        ]);
    
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si es local (no un avatar por URL)
            if ($user->image && !str_starts_with($user->image, 'http')) {
                Storage::disk('public')->delete($user->image);
            }
    
            // Guardar nueva imagen
            $path = $request->file('image')->store('imagenes_usuarios', 'public');
            $user->image = $path;
        }
    
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
    
        $user->save();
    
        return redirect()->back()->with('success', 'Perfil actualizado correctamente.');
    }

}
