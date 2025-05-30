# SIMJ

## Descripción

SIMJ (Sistema de Información para la Gestión de Proyectos) es una aplicación web orientada a la gestión y seguimiento de tareas asociadas a distintos proyectos. Está diseñada para facilitar la organización de actividades por usuario y la generación de reportes detallados en PDF.

## Funcionalidades principales

-   Gestión de proyectos (creación vía modal, solo para administradores)
-   Listado dinámico de proyectos, ordenado por fecha de uso (con recarga automática vía AJAX)
-   Calendario interactivo con funcionalidad de drag-and-drop para asignar tareas
-   Visualización personalizada de tareas por usuario
-   Carga dinámica de tareas según el usuario seleccionado
-   Generación de informes en PDF con filtros por proyecto, fecha y usuario
-   Cálculo de tiempo por tarea y acumulado por proyecto

## Tecnologías usadas

-   **Vite**: Herramienta moderna para desarrollo y empaquetado frontend (reemplaza Mix)
-   **Tailwind CSS**: Framework CSS utilitario para estilos rápidos y responsivos
-   **Bootstrap 5**: Framework CSS clásico para diseño web responsivo
-   **Sass**: Preprocesador CSS para usar variables, anidamiento, etc.
-   **Axios**: Cliente HTTP para hacer solicitudes AJAX desde el navegador
-   **FullCalendar**: Biblioteca JS para mostrar calendarios interactivos
-   **AdminLTE (Laravel AdminLTE)**: Plantilla de administración lista para usar con Laravel UI
-   **Laravel DOMPDF**: Generación de PDFs a partir de vistas Blade
-   **Laravel Tinker**: Consola interactiva para pruebas y manipulación de datos
-   **Laravel 12**: Framework PHP moderno para desarrollo web

## Requisitos del sistema

### Software base

-   PHP: ^8.2 (es decir, 8.2 o superior)
-   Composer: para gestionar dependencias de PHP
-   Node.js: >=16.x (compatible con Vite 6 y Tailwind CSS)
-   NPM: para gestionar paquetes frontend

### Base de datos

-   MySQL >=5.7 o MariaDB >=10.3  
    (Asumiendo que usas MySQL, común con Laravel; si usas otra, ajústalo según corresponda)

## Pasos de instalación

1. Clona el repositorio

```bash
git clone https://github.com/tu-usuario/simj.git
cd simj
```

2. Instala las dependencias de PHP con Composer

```bash
composer install
```

3. Copia el archivo .env y genera la clave de la aplicación

```bash
cp. env.example .env
php artisan key:generate
```

4. Configura la conexión a la base de datos
   Edita tu archivo .env y ajusta los siguientes valores:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simj
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

5. Ejecuta las migraciones

```bash
php artisan migrate
```

6. Instala las dependencias frontend con NPM y compila los assets

```bash
npm install
npm run dev
```

7. Levanta el servidor local

```bash
php artisan serve
```

### API interna

El proyecto cuenta con una API interna (no pública) que utiliza AJAX con Axios para funcionalidades como:

-   Carga dinámica de proyectos
-   Carga de tareas al calendario
-   Generación de informes

### Autor

José Expósito Ávila
