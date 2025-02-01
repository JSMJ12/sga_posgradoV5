<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Secretario;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Models\Seccion;

class SecretarioController extends Controller

{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $secretarios = Secretario::with('seccion')->get();

            return datatables()->of($secretarios)
                ->addColumn('acciones', function ($row) {
                    $editBtn = '<a href="' . route('secretarios.edit', $row->id) . '" class="btn btn-primary" title="Editar Secretario">
                                <i class="fas fa-edit"></i>
                            </a>';
                    return $editBtn;
                })
                ->addColumn('foto', function ($row) {
                    $img = '<img src="' . asset('storage/' . $row->image) . '" alt="Imagen de ' . $row->nombre1 . '" style="max-width: 60px; border-radius: 50%;">';
                    return $img;
                })
                ->rawColumns(['acciones', 'foto'])
                ->make(true);
        }

        return view('secretarios.index');
    }


    public function create()
    {
        $secciones = DB::table('secciones')
            ->whereNotIn('id', function ($query) {
                $query->select('seccion_id')
                    ->from('secretarios')
                    ->whereNotNull('seccion_id');
            })
            ->get();
        return view('secretarios.create', compact('secciones'));
    }

    public function store(Request $request)
    {
        $secretario = new Secretario;
        $secretario->nombre1 = $request->input('nombre1');
        $secretario->nombre2 = $request->input('nombre2');
        $secretario->apellidop = $request->input('apellidop');
        $secretario->apellidom = $request->input('apellidom');
        $secretario->contra = bcrypt($request->input('dni')); // Encriptar la contraseña
        $secretario->sexo = $request->input('sexo');
        $secretario->dni = $request->input('dni');
        $secretario->email = $request->input('email');
        $request->validate([
            'image' => 'nullable|image|max:2048', //máximo tamaño 2MB
        ]);
        $primeraLetra = substr($secretario->nombre1, 0, 1);
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('imagenes_usuarios', 'public');
            $secretario->image = $path;
        } else {
            $secretario->image = 'https://ui-avatars.com/api/?name=' . urlencode($primeraLetra);
        }

        $secretario->seccion_id = $request->input('seccion_id');
        $secretario->save();

        $usuario = new User;
        $usuario->name = $request->input('nombre1');
        $usuario->apellido = $request->input('apellidop');
        $usuario->sexo = $request->input('sexo');
        $usuario->password = bcrypt($request->input('dni'));
        $usuario->status = $request->input('estatus', 'ACTIVO');
        $usuario->email = $request->input('email');
        $usuario->image = $secretario->image;
        $secretarioRole = Role::findById(3);
        $usuario->assignRole($secretarioRole);
        $usuario->save();



        return redirect()->route('secretarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit($id)
    {
        $secretario = Secretario::with('seccion')->find($id);
        $secciones = Seccion::all(); // Traer todas las secciones disponibles
        return view('secretarios.edit', compact('secretario', 'secciones'));
    }

    public function update(Request $request, $id)
    {
        $secretario = Secretario::findOrFail($id);

        $secretario->nombre1 = $request->input('nombre1');
        $secretario->nombre2 = $request->input('nombre2');
        $secretario->apellidop = $request->input('apellidop');
        $secretario->apellidom = $request->input('apellidom');
        $secretario->sexo = $request->input('sexo');
        $secretario->dni = $request->input('dni');
        $secretario->email = $request->input('email');
        $secretario->seccion_id = $request->input('seccion_id');

        $request->validate([
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Eliminar la imagen anterior si existe
            if ($secretario->image) {
                \Storage::disk('public')->delete($secretario->image);
            }

            $path = $request->file('image')->store('imagenes_usuarios', 'public');
            $secretario->image = $path;
        }

        $secretario->save();

        $usuario = User::where('email', $secretario->email)->firstOrFail();
        $usuario->name = $request->input('nombre1');
        $usuario->apellido = $request->input('apellidop');
        $usuario->sexo = $request->input('sexo');
        $usuario->email = $request->input('email');
        if ($request->hasFile('image')) {
            $usuario->image = $path;
        }
        $usuario->save();

        return redirect()->route('secretarios.index')->with('success', 'Secretario actualizado exitosamente.');
    }
}
