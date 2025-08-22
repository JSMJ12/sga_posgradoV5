<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Alumno;
use App\Models\Docente;
use App\Models\Secretario;

class UpdateUserPasswords extends Command
{
    protected $signature = 'users:update-passwords';
    protected $description = 'Actualizar las contraseñas de todos los usuarios con su DNI (hash). Solo ejecutarlo una vez.';

    public function handle()
    {
        $this->info('Iniciando actualización de contraseñas...');

        // Alumnos
        $alumnos = Alumno::all();
        foreach ($alumnos as $alumno) {
            $user = User::where('email', $alumno->email_institucional)->first();
            if ($user) {
                $user->password = Hash::make($alumno->dni);
                $user->save();
                $this->info("Alumno actualizado: {$user->email}");
            }
        }

        // Docentes
        $docentes = Docente::all();
        foreach ($docentes as $docente) {
            $user = User::where('email', $docente->email)->first();
            if ($user) {
                $user->password = Hash::make($docente->dni);
                $user->save();
                $this->info("Docente actualizado: {$user->email}");
            }
        }

        // Secretarios
        $secretarios = Secretario::all();
        foreach ($secretarios as $secretario) {
            $user = User::where('email', $secretario->email)->first();
            if ($user) {
                $user->password = Hash::make($secretario->dni);
                $user->save();
                $this->info("Secretario actualizado: {$user->email}");
            }
        }

        $this->info('Proceso finalizado.');
    }
}
