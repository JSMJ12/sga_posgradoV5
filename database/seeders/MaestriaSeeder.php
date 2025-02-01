<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Maestria;
use App\Models\Docente;
use App\Models\User;
use Spatie\Permission\Models\Role;

class MaestriaSeeder extends Seeder
{

    public function run()
    {
        // Crear el rol "Coordinador" si no existe
        if (!Role::where('name', 'Coordinador')->exists()) {
            Role::create(['name' => 'Coordinador', 'guard_name' => 'web']);
        }

        $docentes = Docente::inRandomOrder()->take(10)->get();

        foreach ($docentes as $docente) {
            Maestria::create([
                'nombre' => 'Maestría en ' . fake()->word(),
                'coordinador' => $docente->dni,
                'inscripcion' => fake()->numberBetween(500, 1000),
                'matricula' => fake()->numberBetween(1500, 3000),
                'arancel' => fake()->numberBetween(10000, 20000),
                'codigo' => fake()->numberBetween(10000, 20000),
            ]);

            // Busca el usuario por su email
            $usuario = User::where('email', $docente->email)->first();
            if ($usuario) {
                $usuario->assignRole('Coordinador');
            }
        }
    }
}
