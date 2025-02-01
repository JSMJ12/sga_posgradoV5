<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Alumno;

class DescuentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function alumnos(Request $request)
    {
        if ($request->ajax()) {
            $query = Alumno::with('maestria');

            return datatables()->eloquent($query)
                ->addColumn('maestria_nombre', function ($alumno) {
                    return $alumno->maestria ? $alumno->maestria->nombre : 'Sin Maestría';
                })
                ->addColumn('foto', function ($alumno) {
                    return '<img src="' . asset('storage/' . $alumno->image) . '" alt="Foto de ' . $alumno->nombre1 . '" 
                        class="img-thumbnail rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">';
                })
                ->addColumn('nombre_completo', function ($alumno) {
                    return "{$alumno->nombre1} {$alumno->nombre2} {$alumno->apellidop} {$alumno->apellidom}";
                })
                ->addColumn('acciones', function ($alumno) {
                    if ($alumno->descuento !== null) {
                        return '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Descuento aplicado</span>';
                    }
                    return '<button class="btn btn-primary btn-sm select-descuento" data-dni="' . $alumno->dni . '" data-toggle="modal">
                                <i class="fas fa-tags"></i> Descuento
                            </button>';
                })
                              
                ->rawColumns(['foto', 'acciones', 'nombre_completo'])
                ->toJson();
        }

        return view('pagos.descuento');
    }
}
