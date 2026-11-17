# Códigos Acabados

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/Filament-5-FDAE4B?style=for-the-badge&logo=laravel&logoColor=white" alt="Filament 5">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
</p>

<p align="center">
  <strong>Sistema web de gestión y control operativo para el área de Acabados de una empresa textil.</strong>
</p>

<p align="center">
  Aplicación desarrollada para centralizar la gestión de códigos, controlar existencias, administrar trabajadores y registrar entregas de rollos mediante una plataforma web con indicadores, auditoría y control de acceso.
</p>

<p align="center">
  <a href="#-características">Características</a> •
  <a href="#-tecnologías">Tecnologías</a> •
  <a href="#-instalación">Instalación</a> •
  <a href="#-arquitectura-general">Arquitectura</a>
</p>

---

## Descripción

**Códigos Acabados** es un sistema web desarrollado para optimizar el control de las operaciones del área de Acabados dentro del sector textil.

La plataforma permite gestionar los códigos y sus existencias, administrar trabajadores, registrar entregas de rollos y mantener un seguimiento de los movimientos realizados.

Además, incorpora un dashboard administrativo con indicadores estadísticos, gráficos, alertas de stock y un historial de actividades para facilitar el seguimiento y la supervisión de las operaciones.

## Características

### Dashboard y estadísticas

- Indicadores de códigos registrados, stock disponible, entregas y trabajadores activos.
- Gráfico de entregas por períodos.
- Alertas para identificar códigos con stock bajo o agotado.
- Visualización de las últimas actividades del sistema.
- Widgets configurables individualmente para cada usuario.
- Visualización de componentes según los permisos asignados.

### Gestión de códigos

- Registro, consulta y actualización de códigos.
- Control de existencias disponibles.
- Actualización de stock.
- Protección contra eliminación de códigos relacionados con entregas.

### Gestión de trabajadores

- Registro y administración de trabajadores.
- Control de estados activo e inactivo.
- Consulta de información de trabajadores.
- Restricción de eliminación cuando existen entregas asociadas.

### Control de entregas

- Registro de entregas de rollos a trabajadores.
- Seguimiento de cantidades entregadas y restantes.
- Descuento automático del stock disponible.
- Restauración automática del stock al eliminar una entrega.
- Protección de operaciones críticas mediante transacciones y bloqueos de registros.
- Conservación de información histórica mediante snapshots.

### Usuarios, roles y permisos

- Administración de cuentas de usuario.
- Gestión de roles y permisos.
- Control de acceso a módulos y operaciones.
- Restricciones para proteger las operaciones administrativas.

**Roles disponibles:**

| Rol | Acceso |
|---|---|
| Administrador | Administración completa del sistema. |
| Supervisor | Acceso a las funcionalidades operativas autorizadas. |
| Operario | Consulta de información y registro de entregas según sus permisos. |

### Historial y auditoría

- Registro de actividades realizadas por los usuarios.
- Seguimiento de modificaciones en los registros.
- Identificación del usuario responsable.
- Visualización de cambios relevantes.
- Conservación de referencias históricas mediante snapshots.

### Configuración del sistema

- Personalización del nombre y descripción.
- Configuración de la identidad de la empresa.
- Administración de logotipos y favicon.
- Personalización del color principal.
- Configuración del texto del pie de página.
- Registro del usuario responsable de las modificaciones.

---

## Tecnologías

<p align="center">
  <img src="https://skillicons.dev/icons?i=php,laravel,mysql,tailwind,alpinejs,js,html,css,vite,npm,git,github" alt="Tecnologías utilizadas">
</p>

| Tecnología | Aplicación |
|---|---|
| PHP 8.4 | Lenguaje de programación backend. |
| Laravel 13 | Framework principal de desarrollo. |
| Filament 5 | Panel administrativo y gestión de recursos. |
| Livewire 4 | Componentes dinámicos e interactivos. |
| MySQL | Gestión de base de datos. |
| Tailwind CSS | Diseño y estilos de interfaz. |
| Alpine.js | Interactividad del frontend. |
| Spatie Permission | Administración de roles y permisos. |
| Spatie Activitylog | Registro y auditoría de actividades. |
| Composer | Administración de dependencias PHP. |
| NPM | Administración de dependencias frontend. |
| Vite | Compilación de recursos frontend. |

---

## Arquitectura general

```text
codigos-acabados/
│
├── app/
│   ├── Filament/
│   │   ├── Pages/
│   │   ├── Resources/
│   │   └── Widgets/
│   │
│   ├── Models/
│   └── Providers/
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
│
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│
├── routes/
│   └── web.php
│
├── public/
├── storage/
├── tests/
├── .env.example
└── composer.json
```

---

## Instalación

### Requisitos previos

- PHP 8.4 o superior.
- Composer.
- Node.js y NPM.
- MySQL.
- Extensiones PHP requeridas por Laravel y sus dependencias.

### 1. Clonar el repositorio

```bash
git clone https://github.com/AbrahamCueva/codigos-acabados.git

cd codigos-acabados
```

### 2. Instalar dependencias

```bash
composer install

npm install
```

### 3. Configurar variables de entorno

```bash
cp .env.example .env
```

Configurar los datos de conexión a MySQL en el archivo `.env`:

```env
APP_NAME="Códigos Acabados"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_TIMEZONE=America/Lima

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=codigos_acabados
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generar la clave de aplicación

```bash
php artisan key:generate
```

### 5. Ejecutar migraciones y seeders

```bash
php artisan migrate --seed
```

### 6. Crear el enlace de almacenamiento

```bash
php artisan storage:link
```

### 7. Compilar los recursos frontend

```bash
npm run build
```

### 8. Iniciar el servidor

```bash
php artisan serve
```

Acceder al panel administrativo:

```text
http://127.0.0.1:8000/admin
```

> **Nota:** Configura los usuarios iniciales y sus permisos de acuerdo con los seeders del proyecto. No se incluyen credenciales de acceso en este README.

---

## Seguridad e integridad de datos

El sistema incorpora mecanismos para proteger la información y mantener la consistencia de las operaciones:

- Autorización mediante roles y permisos.
- Validación de operaciones según los privilegios del usuario.
- Transacciones para operaciones que afectan el stock.
- Bloqueo de registros durante operaciones críticas.
- Protección contra eliminación de registros relacionados.
- Conservación de información histórica mediante snapshots.
- Registro de actividades para facilitar la trazabilidad.

---

## Capturas de pantalla

Las siguientes imágenes pueden incorporarse para mostrar las principales interfaces del sistema.

| Dashboard | Gestión de códigos |
|---|---|
| `screenshots/dashboard.png` | `screenshots/codigos.png` |

| Gestión de entregas | Gestión de trabajadores |
|---|---|
| `screenshots/entregas.png` | `screenshots/trabajadores.png` |

| Historial de actividades | Configuración |
|---|---|
| `screenshots/actividades.png` | `screenshots/configuracion.png` |

> Para visualizar las imágenes, agrega las capturas correspondientes dentro de la carpeta `screenshots/` y utiliza rutas Markdown como `![Dashboard](screenshots/dashboard.png)`.

---

## Objetivo del proyecto

Desarrollar una solución web que permita centralizar y optimizar el control de las operaciones relacionadas con códigos, existencias, trabajadores y entregas del área de Acabados, reduciendo la dependencia de registros manuales y facilitando el seguimiento de la información operativa.

---

## Autor

<p align="center">
  <strong>Abraham Elias Cueva Rico</strong>
  <br>
  Desarrollador web
  <br>
  Laravel · PHP · Filament · MySQL
</p>

---

<p align="center">
  <sub>Desarrollado para la gestión y control de operaciones del área de Acabados.</sub>
  <br>
  <sub>© 2026 Códigos Acabados. Todos los derechos reservados.</sub>
</p>
