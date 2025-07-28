# sga_posgradoV5

Sistema de Gestión Académica para Posgrados (UNESUM)

Este proyecto es un sistema web desarrollado en **Laravel** para gestionar los procesos académicos de posgrado de la Universidad Estatal del Sur de Manabí. Incluye módulos para postulantes, docentes, coordinadores y administrativos, cubriendo procesos de inscripción, gestión de documentos, matrícula, titulación y certificaciones.

## Características principales

- Gestión de postulantes: inscripción, carga y verificación de documentos, generación de fichas PDF.
- Gestión de docentes: registro, asignación de roles, carga de información personal y académica.
- Módulo de titulación: solicitudes, asignación de tutor, control de modalidades y generación de solicitudes en PDF.
- Certificaciones: generación de certificados de notas y de finalización.
- Panel administrativo: control de cohortes, maestrías, módulos y usuarios.
- Seguridad: gestión de roles y permisos con control de acceso.
- Integración con almacenamiento de archivos y generación de PDFs personalizados.

## Tecnologías utilizadas

- **Laravel** (PHP framework)
- **Blade** (Motor de plantillas)
- **MySQL** (Base de datos)
- **Bootstrap** (Frontend)
- **Carbon** (Fechas en PHP)
- **Spatie/laravel-permission** (Roles y permisos)
- **UI Avatars, Storage, DomPDF, etc.**

## Instalación y configuración

1. Clona el repositorio:

   ```sh
   git clone https://github.com/JSMJ12/sga_posgradoV5.git
   cd sga_posgradoV5
   ```

2. Instala las dependencias de Composer y NPM:

   ```sh
   composer install
   npm install && npm run dev
   ```

3. Copia el archivo `.env.example` a `.env` y configura tu base de datos y otras variables de entorno.

   ```sh
   cp .env.example .env
   php artisan key:generate
   ```

4. Ejecuta las migraciones y los seeders para crear las tablas y datos iniciales:

   ```sh
   php artisan migrate --seed
   ```

5. Levanta el servidor local de Laravel:

   ```sh
   php artisan serve
   ```

## Uso general

- Accede a `/login` para ingresar como administrador, docente o postulante.
- Los postulantes pueden inscribirse, subir documentos y descargar fichas.
- El panel administrativo permite gestión de usuarios, cohortes, maestrías, docentes y procesos de titulación.
- Se pueden generar certificados y solicitudes en PDF desde los módulos correspondientes.

## Estructura de carpetas relevante

- `app/Http/Controllers/` - Controladores de cada módulo.
- `app/Models/` - Modelos de entidad (e.g., Postulante, Docente).
- `database/migrations/` - Migraciones de base de datos.
- `database/seeders/` - Seeders para poblar datos iniciales.
- `resources/views/` - Vistas Blade del sistema.
- `public/` - Recursos estáticos y archivos subidos.

## Contribuciones

Las contribuciones son bienvenidas. Por favor, abre un issue o pull request para sugerencias o mejoras.

## Licencia

Este proyecto utiliza la licencia MIT.

---

**Documentación adicional de Laravel:**  
[Documentación oficial de Laravel](https://laravel.com/docs)
