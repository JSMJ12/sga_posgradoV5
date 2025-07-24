<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ficha de Admisión</title>
    <style>
         @page {
            size: A4;
            margin: 0;
        }
        html, body {
            font-family: "Times New Roman", serif;
            font-size: 10pt;
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 120px 25px 10px 60px;
            background-image: url("{{ public_path('images/fondo-pdf.jpeg') }}");
            background-size: 98% 98%;
            background-position: center center;
            background-repeat: no-repeat;
        }

        .section-title {
            background: #22313f;
            color: #fff;
            font-weight: bold;
            font-size: 10pt;
            padding: 6px 12px;
            border-radius: 6px 6px 0 0;
            margin: 10px 0 0;
        }

        table.excel-style {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        table.excel-style td {
            border: 1px solid #999;
            padding: 4px 6px;
            background-color: #fff;
        }

        .avatar {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 2px solid #3d4a3d;
            border-radius: 6px;
        }

        table.firmas {
            width: 85%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-top: 20px;
        }

        table.firmas td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            vertical-align: top;
        }

        table.firmas hr {
            margin: 6px 20px;
            border: none;
            border-top: 1px solid #000;
        }

        /* Evita que las tablas se corten entre páginas */
        table.excel-style,
        table.firmas {
            page-break-inside: avoid;
        }

        /* Opcional: para la tabla de datos del estudiante */
        table.datos-estudiante {
            page-break-inside: avoid;
        }

        .section-group {
            page-break-inside: avoid;
            width: 85%;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>

    <!-- Datos del Postulante -->
    <div class="section-group">
        <div class="section-title">Datos del Estudiante</div>
        <table class="excel-style datos-estudiante">
            <tr>
                <td><strong>Apellidos y Nombres:</strong></td>
                <td colspan="3">{{ $postulante->apellidop }} {{ $postulante->apellidom }} {{ $postulante->nombre1 }}
                    {{ $postulante->nombre2 }}</td>
                <td rowspan="4" style="text-align: center; width: 110px;">
                    <img src="{{ $postulante->imagen ? public_path('storage/' . $postulante->imagen) : public_path('images/default-avatar.jpg') }}"
                        class="avatar" alt="Foto">
                </td>
            </tr>
            <tr>
                <td><strong>Cédula/Pasaporte:</strong></td>
                <td>{{ $postulante->dni }}</td>
                <td><strong>Correo:</strong></td>
                <td>{{ $postulante->correo_electronico }}</td>
            </tr>
            <tr>
                <td><strong>Celular:</strong></td>
                <td>{{ $postulante->celular }}</td>
                <td><strong>Edad:</strong></td>
                <td>{{ $postulante->edad }}</td>
            </tr>
            <tr>
                <td><strong>Sexo:</strong></td>
                <td>{{ $postulante->sexo }}</td>
                <td><strong>Tipo de Sangre:</strong></td>
                <td>{{ $postulante->tipo_sangre }}</td>
            </tr>
        </table>
    </div>

    <!-- Maestría -->
    <div class="section-group">
        <div class="section-title">Maestría</div>
        <table class="excel-style">
            <tr>
                <td><strong>Programa:</strong></td>
                <td colspan="3">{{ $postulante->maestria->nombre ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Datos Personales -->
    <div class="section-group">
        <div class="section-title">Datos Personales</div>
        <table class="excel-style">
            <tr>
                <td><strong>Fecha de Nacimiento:</strong></td>
                <td>{{ $postulante->fecha_nacimiento }}</td>
                <td><strong>Nacionalidad:</strong></td>
                <td>{{ $postulante->nacionalidad }}</td>
            </tr>
            <tr>
                <td><strong>Libreta Militar:</strong></td>
                <td>{{ $postulante->libreta_militar }}</td>
                <td><strong>¿Discapacidad?</strong></td>
                <td>{{ $postulante->discapacidad }}</td>
            </tr>
            <tr>
                <td><strong>Porcentaje:</strong></td>
                <td>{{ $postulante->porcentaje_discapacidad }}</td>
                <td><strong>Código CONADIS:</strong></td>
                <td>{{ $postulante->codigo_conadis }}</td>
            </tr>
            <tr>
                <td><strong>Tipo Discapacidad:</strong></td>
                <td colspan="3">{{ $postulante->tipo_discapacidad }}</td>
            </tr>
        </table>
    </div>

    <!-- Residencia -->
    <div class="section-group">
        <div class="section-title">Residencia</div>
        <table class="excel-style">
            <tr>
                <td><strong>País:</strong></td>
                <td>{{ $postulante->pais_residencia }}</td>
                <td><strong>Años:</strong></td>
                <td>{{ $postulante->anios_residencia }}</td>
            </tr>
            <tr>
                <td><strong>Provincia:</strong></td>
                <td>{{ $postulante->provincia }}</td>
                <td><strong>Cantón:</strong></td>
                <td>{{ $postulante->canton }}</td>
            </tr>
            <tr>
                <td><strong>Parroquia:</strong></td>
                <td>{{ $postulante->parroquia }}</td>
                <td><strong>Calle Principal:</strong></td>
                <td>{{ $postulante->calle_principal }}</td>
            </tr>
            <tr>
                <td><strong>Número:</strong></td>
                <td>{{ $postulante->numero_direccion }}</td>
                <td><strong>Calle Secundaria:</strong></td>
                <td>{{ $postulante->calle_secundaria }}</td>
            </tr>
            <tr>
                <td><strong>Referencia:</strong></td>
                <td>{{ $postulante->referencia_direccion }}</td>
                <td><strong>Teléfono Domicilio:</strong></td>
                <td>{{ $postulante->telefono_domicilio }}</td>
            </tr>
            <tr>
                <td><strong>Celular Residencia:</strong></td>
                <td colspan="3">{{ $postulante->celular_residencia }}</td>
            </tr>
        </table>
    </div>

    <!-- Académica -->
    <div class="section-group">
        <div class="section-title">Información Académica</div>
        <table class="excel-style">
            <tr>
                <td><strong>Especialidad Bachillerato:</strong></td>
                <td>{{ $postulante->especialidad_bachillerato }}</td>
                <td><strong>Colegio:</strong></td>
                <td>{{ $postulante->colegio_bachillerato }}</td>
            </tr>
            <tr>
                <td><strong>Ciudad:</strong></td>
                <td>{{ $postulante->ciudad_bachillerato }}</td>
                <td><strong>Título Profesional:</strong></td>
                <td>{{ $postulante->titulo_profesional }}</td>
            </tr>
            <tr>
                <td><strong>Especialidad:</strong></td>
                <td>{{ $postulante->especialidad_mencion }}</td>
                <td><strong>Universidad:</strong></td>
                <td>{{ $postulante->universidad_titulo }}</td>
            </tr>
            <tr>
                <td><strong>Ciudad Universidad:</strong></td>
                <td>{{ $postulante->ciudad_universidad }}</td>
                <td><strong>País:</strong></td>
                <td>{{ $postulante->pais_universidad }}</td>
            </tr>
            <tr>
                <td><strong>Registro SENESCYT:</strong></td>
                <td colspan="3">{{ $postulante->registro_senescyt }}</td>
            </tr>
            <tr>
                <td><strong>Título Posgrado:</strong></td>
                <td>{{ $postulante->titulo_posgrado }}</td>
                <td><strong>Universidad Posgrado:</strong></td>
                <td>{{ $postulante->universidad_posgrado }}</td>
            </tr>
            <tr>
                <td><strong>Ciudad Posgrado:</strong></td>
                <td>{{ $postulante->ciudad_posgrado }}</td>
                <td><strong>País Posgrado:</strong></td>
                <td>{{ $postulante->pais_posgrado }}</td>
            </tr>
        </table>
    </div>
    
    <!-- Laboral -->
    <div class="section-group page-break">
        <div class="section-title">Información Laboral</div>
        <table class="excel-style">
            <tr>
                <td><strong>Lugar Trabajo:</strong></td>
                <td>{{ $postulante->lugar_trabajo }}</td>
                <td><strong>Función:</strong></td>
                <td>{{ $postulante->funcion_laboral }}</td>
            </tr>
            <tr>
                <td><strong>Ciudad:</strong></td>
                <td>{{ $postulante->ciudad_trabajo }}</td>
                <td><strong>Dirección:</strong></td>
                <td>{{ $postulante->direccion_trabajo }}</td>
            </tr>
            <tr>
                <td><strong>Teléfono Trabajo:</strong></td>
                <td colspan="3">{{ $postulante->telefono_trabajo }}</td>
            </tr>
        </table>
    </div>

    <!-- Socioeconómica -->
    <div class="section-group">
        <div class="section-title">Datos Socioeconómicos</div>
        <table class="excel-style">
            <tr>
                <td><strong>Etnia:</strong></td>
                <td>{{ $postulante->etnia }}</td>
                <td><strong>Nacionalidad Indígena:</strong></td>
                <td>{{ $postulante->nacionalidad_indigena }}</td>
            </tr>
            <tr>
                <td><strong>Tipo Colegio:</strong></td>
                <td>{{ $postulante->tipo_colegio }}</td>
                <td><strong>Miembros Hogar:</strong></td>
                <td>{{ $postulante->cantidad_miembros_hogar }}</td>
            </tr>
            <tr>
                <td><strong>Ingreso Total:</strong></td>
                <td>{{ $postulante->ingreso_total_hogar }}</td>
                <td><strong>Formación Padre:</strong></td>
                <td>{{ $postulante->nivel_formacion_padre }}</td>
            </tr>
            <tr>
                <td><strong>Formación Madre:</strong></td>
                <td>{{ $postulante->nivel_formacion_madre }}</td>
                <td><strong>Origen Recursos:</strong></td>
                <td>{{ $postulante->origen_recursos_estudios }}</td>
            </tr>
        </table>
    </div>

    <!-- Contacto Emergencia -->
    <div class="section-group">
        <div class="section-title">Contacto de Emergencia</div>
        <table class="excel-style">
            <tr>
                <td><strong>Apellidos:</strong></td>
                <td>{{ $postulante->contacto_apellidos }}</td>
                <td><strong>Nombres:</strong></td>
                <td>{{ $postulante->contacto_nombres }}</td>
            </tr>
            <tr>
                <td><strong>Parentesco:</strong></td>
                <td>{{ $postulante->contacto_parentesco }}</td>
                <td><strong>Teléfono:</strong></td>
                <td>{{ $postulante->contacto_telefono }}</td>
            </tr>
            <tr>
                <td><strong>Celular:</strong></td>
                <td colspan="3">{{ $postulante->contacto_celular }}</td>
            </tr>
        </table>
    </div>

    {{-- Firmas --}}
    <table class="firmas">
        <tr>
            <td>
                <b>Elaborado por:</b><br>
                <br>
                <br>
                <hr>

                <span style="text-transform:uppercase;">
                    {{ $postulante->apellidop }} {{ $postulante->apellidom }} {{ $postulante->nombre1 }}
                    {{ $postulante->nombre2 }}
                </span><br><b>SECRETARIO/A</b>
            </td>
            <td>
                <b>Revisado por:</b><br>
                <br>
                <br>
                <hr>

                <span style="text-transform:uppercase;">
                    {{ $secretario->full_name ?? '--' }}
                </span><br><b>SECRETARIO/A</b>
            </td>
            <td>
                <b>Aprobado por:</b><br>
                <br>
                <br>
                <hr>

                <span style="text-transform:uppercase;">{{ strtoupper($nombreCompleto) }}</span><br>
                <b>COORDINADOR(A) DEL PROGRAMA</b>
            </td>
        </tr>
    </table>
</body>

</html>
