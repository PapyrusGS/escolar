# CONTEXT.md — Sistema Escolar (escolar)

> Documento de contexto técnico generado para consumo de LLMs. Refleja el estado real del repositorio.

---

## 1. PROPÓSITO DEL SISTEMA

**Sistema Escolar** es una aplicación web full‑stack para la gestión académica de un colegio/instituto técnico. Cubre:

- **Autenticación** de usuarios con tres roles (`admin`, `docente`, `estudiante`) usando tokens Sanctum.
- **Gestión de usuarios** (CRUD) realizada por administradores (con `Estado` activo/inactivo, asignación a `rol`, `carrera` y `modalidad` para estudiantes).
- **Gestión académica**: carreras, materias, pénsum, cursos (aulas), cursos‑materias, turnos, modalidad.
- **Inscripción de estudiantes** a cursos‑materias disponibles según su carrera/modalidad.
- **Gestión de notas** y notificaciones.
- **Reportes dinámicos por rol** (`AdminReporteStrategy`, `DocenteReporteStrategy`, `EstudianteReporteStrategy`) con filtros.
- **Dashboard** con estadísticas (`/api/auth/dashboard/stats`).
- **Perfil de usuario** (ver/editar/cambiar contraseña).

El frontend (Vue 3) consume una API REST JSON; las páginas Blade son *thin shells* que montan componentes Vue sobre `div` con `id` específicos.

---

## 2. STACK TECNOLÓGICO

| Capa | Tecnología |
|---|---|
| **Backend** | PHP 8.2+, Laravel 12 (`laravel/framework ^12.0`) |
| **Auth API** | Laravel Sanctum 4.3 (tokens personales) |
| **Frontend** | Vue 3.5 (Options + Composition API, **sin TypeScript**) |
| **Build / Dev Server** | Vite 7, `laravel-vite-plugin` 2, `@vitejs/plugin-vue` 6 |
| **Estilos** | Tailwind CSS 4 (`@tailwindcss/vite`, `@import 'tailwindcss';`) |
| **HTTP Client** | Axios (instancia global con interceptor 401) |
| **Base de datos** | MySQL (`.env` local) — `.env.example` viene con SQLite por defecto |
| **Drivers Laravel** | `database` para session, cache y queue |
| **Concurrently (dev)** | `composer dev` lanza en paralelo: `artisan serve`, `queue:listen`, `pail`, `vite` |
| **Testing (stubs)** | PHPUnit 11.5 / Mockery / Collision / Faker |
| **Code style** | Laravel Pint 1.24 instalado, sin config personalizada |
| **Convenciones DB** | PKs `Id*` (`IdUsuario`, `IdRol`, `IdCarrera`, `IdMateria`, `IdCurso`, `IdCursoMateria`, `IdInscripcion`, `IdNota`, `IdNotificacion`, `IdTurno`, `IdModalidad`, `IdPensum`, `IdEstudianteCarrera`); `timestamps = false`; nombres de columna en español (`Correo`, `Contrasena`, `Nombre1`, `Apellido1`, `CI`, `Telefono`, `FechaRegistro`, `Estado`). |

---

## 3. ARQUITECTURA Y ESTRUCTURA DE CARPETAS

Patrón: **Repository → Service → Controller** con DI por constructor y `readonly` promoted properties (PHP 8.2+).

```
escolar/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php                  # base
│   │   │   └── API/                            # 7 controllers JSON
│   │   │       ├── AuthController.php          # login, logout, perfil, changePassword
│   │   │       ├── UsuarioController.php       # CRUD usuarios (admin)
│   │   │       ├── EstudianteController.php    # perfil + inscripción a cursos
│   │   │       ├── CursoMateriaController.php  # CRUD cursos-materias (admin)
│   │   │       ├── DashboardController.php
│   │   │       ├── NotificacionController.php
│   │   │       └── ReporteController.php        # usa Strategies
│   │   ├── Middleware/
│   │   │   └── EnsureAdminRole.php             # alias 'admin.role' → IdRol === 1
│   │   │   # ⚠️ EnsureTeacherRole REFERENCIADO en bootstrap/app.php PERO NO EXISTE
│   │   └── Requests/                           # FormRequests por dominio
│   │       ├── Auth/   (LoginRequest, UpdateProfileRequest, ChangePasswordRequest)
│   │       ├── Usuario/(StoreUsuarioRequest, UpdateUsuarioRequest)
│   │       ├── Curso/  (CursoMateria*)
│   │       └── Estudiante/(EditarPerfil, CambiarContrasena, InscribirCurso)
│   ├── Models/                                 # Eloquent; tabla/PK explícitas
│   │   ├── User.php        # tabla 'usuarios', PK 'IdUsuario', getAuthPassword()→Contrasena
│   │   ├── Rol.php         # tabla 'roles', PK 'IdRol'
│   │   ├── Curso.php       # tabla 'cursos', PK 'IdCurso'
│   │   ├── CursoMateria.php
│   │   ├── Materia.php
│   │   ├── Notificacion.php
│   │   └── Turno.php
│   │   # ⚠️ Faltan modelos: Carrera, Modalidad, Pensum, Inscripcion, Nota,
│   │   # EstudianteCarrera, CarreraMateriaPensum, Notificacion (existe)
│   ├── Repositories/                           # 11 repos, todos sin interface
│   │   ├── UsuarioRepository.php
│   │   ├── RolRepository.php
│   │   ├── CarreraRepository.php
│   │   ├── ModalidadRepository.php
│   │   ├── MateriaRepository.php
│   │   ├── TurnoRepository.php
│   │   ├── CursoRepository.php
│   │   ├── CursoMateriaRepository.php
│   │   ├── EstudianteCarreraRepository.php
│   │   ├── EstudianteRepository.php
│   │   └── NotificacionRepository.php
│   ├── Services/                               # orquesta repos + transacciones
│   │   ├── AuthService.php
│   │   ├── UsuarioService.php
│   │   ├── EstudianteService.php
│   │   ├── CursoMateriaService.php
│   │   ├── NotificacionService.php
│   │   ├── DashboardService.php
│   │   └── ReporteService.php                  # delega a Strategies
│   ├── Strategies/                             # Strategy pattern para reportes
│   │   ├── ReporteStrategyInterface.php
│   │   ├── AdminReporteStrategy.php
│   │   ├── DocenteReporteStrategy.php
│   │   └── EstudianteReporteStrategy.php
│   └── Providers/AppServiceProvider.php
├── bootstrap/app.php                           # registra middlewares, handlers 422/401
├── database/
│   ├── migrations/                             # 18 migraciones, todas con timestamps false
│   └── seeders/                                # 15 seeders (ver AGENTS.md §Seeders)
├── public/                                     # entrypoint web (index.php)
├── resources/
│   ├── css/app.css                             # @import 'tailwindcss';
│   ├── js/
│   │   ├── app.js                              # entrypoint: monta N componentes Vue
│   │   ├── bootstrap.js                        # axios + interceptor 401 → redirect '/'
│   │   └── components/                         # 11 .vue (Login, Index, User*, Curso*,
│   │       │                                    #  Dashboard, Reportes, AdminNotif, Profile)
│   └── views/                                  # Blade shells con <div id="*-app">
│       ├── welcome.blade.php                   # #login-app
│       ├── index.blade.php                     # #index-app
│       ├── dashboard.blade.php
│       ├── perfil.blade.php
│       ├── reportes.blade.php
│       ├── usuarios/  (create, index)
│       └── cursos/   (index, visualizacion)
├── routes/
│   ├── web.php                                 # solo devuelve vistas (sin lógica)
│   ├── api.php                                 # rutas /api/* principales
│   ├── estudiantes_api.php                     # /api/estudiante/* (montado en then() de bootstrap)
│   └── console.php
├── .env.example                                # DB por defecto sqlite; en .env real → mysql
├── composer.json                               # scripts: setup, dev, test
├── package.json                                # scripts: build, dev
└── vite.config.js                              # plugins: laravel + vue + tailwindcss
```

---

## 4. FLUJO PRINCIPAL DE DATOS

**Ejemplo: `POST /api/auth/login`**

1. **Cliente (Vue)** → `axios.post('/api/auth/login', { Correo, Contrasena })` (token JWT‑like guardado en `localStorage` por `bootstrap.js`).
2. **Routing** → `routes/api.php` → `AuthController@login`.
3. **Validación** → `App\Http\Requests\Auth\LoginRequest` (FormRequest autorrellena 422 JSON si falla).
4. **Controller** → invoca `AuthService->login(correo, contrasena)`.
5. **Service** → `UsuarioRepository->findActiveByCorreo(correo)` (query Eloquent con `with('rol')` + `where Estado = true`).
6. **Repository/Model** → Eloquent sobre tabla `usuarios` (PK `IdUsuario`, `getAuthPassword()` devuelve `Contrasena`).
7. **Service** → `Hash::check($contrasena, $usuario->Contrasena)`.
8. **Auth** → `$usuario->createToken('auth_token', ['*'])->plainTextToken` (Sanctum).
9. **Service** → retorna array `{ status, message, data: { user, token } }`.
10. **Controller** → `response()->json($result, 200|401)`.
11. **Excepciones globales** en `bootstrap/app.php`: `ValidationException → 422`, `AuthenticationException → 401`, todas con shape `{ status:false, message, data }`.
12. **Cliente** → guarda `auth_token` + `auth_user` en `localStorage`; en cada request el `axios.interceptors.request` adjunta `Authorization: Bearer <token>`. Interceptor de respuesta limpia el storage y redirige a `/` ante 401.

**Para rutas protegidas** (`auth:sanctum`) + rol admin (`admin.role`): el middleware `EnsureAdminRole` aborta con 403 JSON si `IdRol !== 1`.

**Frontend mounting** (Vite → `resources/js/app.js`): cada vista Blade expone un `<div id="X-app">` y `app.js` hace `createApp(Component).mount('#X-app')` solo si el elemento existe.

---

## 5. REGLAS DE DESARROLLO (AI RULES)

> Estas reglas son **obligatorias** para cualquier código nuevo o modificado en este repo.

### 5.1 Arquitectura y capas
- **Respetar el patrón Repository → Service → Controller.** No saltarse capas; los Controllers NO deben contener lógica de negocio ni queries Eloquent directas.
- **Repositories** devuelven modelos, colecciones o tipos primitivos. No formatean respuestas HTTP.
- **Services** orquestan repos, manejan `DB::transaction(...)` para escrituras multi‑tabla y lanzan `RuntimeException` con mensaje legible.
- **Controllers** son *thin*: llaman al service, capturan `Throwable`, y formatean la respuesta JSON estándar.
- Los repositorios **no usan interfaces** (decisión actual del proyecto). No introducir contratos nuevos sin discutirlo.
- `App\Strategies\ReporteStrategyInterface` **sí** es interface y debe mantenerse (patrón Strategy aplicado solo a reportes).

### 5.2 Inyección de dependencias y PHP
- Usar **constructor DI con `readonly` promoted properties** (PHP 8.2+). Ej.: `public function __construct(private readonly UsuarioRepository $r) {}`.
- Tipar siempre retornos (`JsonResponse`, `array`, `?User`).
- No añadir código TypeScript en el frontend (es JS plano Vue 3).

### 5.3 Base de datos y modelos
- **Mantener convención `Id*`** para PKs (`IdUsuario`, `IdCurso`, `IdMateria`, etc.).
- Mantener `public $timestamps = false;` en todos los modelos.
- Usar **siempre consultas Eloquent parametrizadas** (nunca concatenar input del usuario en SQL crudo). Si se usa `DB::table`, los bindings deben seguir siendo seguros.
- Las migraciones nuevas deben seguir el orden de `database/migrations/` y mantener nombres `Id*`.
- Para agregar tablas faltantes, **crear también su modelo Eloquent** y, si aplica, su repositorio.

### 5.4 API y respuestas JSON
- **Shape obligatorio** en TODA respuesta JSON:
  ```json
  { "status": true, "message": "texto humano en español", "data": {} }
  ```
- Códigos: `200` OK, `201` Created, `400` Bad Request, `401` No autenticado, `403` Sin permisos, `422` Validación, `500` Error servidor.
- Mensajes al usuario **en español** y sin filtrar stack traces en producción.
- `ValidationException` y `AuthenticationException` ya están manejadas en `bootstrap/app.php` — no duplicar handlers.

### 5.5 Autenticación y autorización
- Sanctum para tokens (`Authorization: Bearer <token>`). Token guardado por el cliente en `localStorage` como `auth_token`.
- Roles: `1 = admin`, `2 = docente`, `3 = estudiante` (validar contra `IdRol`).
- Middleware `admin.role` (alias) para endpoints exclusivos de admin. **No crear** nuevas rutas admin sin este middleware.
- ⚠️ El alias `teacher.role` está registrado pero su clase `EnsureTeacherRole` **no existe** — si se usa, debe crearse primero.

### 5.6 Validación
- Toda entrada del cliente pasa por un **FormRequest** en `app/Http/Requests/<Dominio>/`. No usar `$request->validate()` inline.
- Nombres de campos en FormRequests deben coincidir con el contrato del frontend (ya usa `Correo`, `Contrasena`, etc.).
- Devolver errores con la forma estándar (`422` + `data: $e->errors()`).

### 5.7 Frontend (Vue 3 + Vite + Tailwind)
- Componentes en `resources/js/components/*.vue`, **JS plano** (sin TS).
- Cada vista Blade expone un `<div id="<nombre>-app">` y el componente se monta en `resources/js/app.js`. Si se añade un componente nuevo, registrarlo en el array `mounts` de `app.js`.
- Usar `axios` desde la instancia global de `bootstrap.js` (ya configura `Authorization` y el interceptor 401).
- Estilos con **Tailwind v4** (`@import 'tailwindcss';`). No introducir otros frameworks de CSS.
- Llamadas API con **ruta relativa** (`/api/...`), no hardcodear host.

### 5.8 Seeders y datos
- 15 seeders registrados en `DatabaseSeeder.php`. Mantener contraseñas de prueba en `123456` solo para seeders.
- Roles sembrados: Admin (1), Docente (2), Estudiante (3).

### 5.9 Commits, estilo y prohibiciones
- **No añadir comentarios** en el código nuevo salvo que el usuario lo pida explícitamente.
- No committear `.env`, `node_modules/`, `vendor/`, `storage/` (ya en `.gitignore`).
- No exponer claves, tokens ni contraseñas en logs o respuestas.
- **No committear a `main` directo** sin feature branch.
- `composer test` corre los stubs PHPUnit existentes — mantenerlos verdes al añadir tests reales.

### 5.10 Gotchas / cosas pendientes
- Crear `EnsureTeacherRole` antes de usar el alias `teacher.role`.
- Crear los 11 modelos Eloquent faltantes (Carrera, Modalidad, Pensum, Inscripcion, Nota, EstudianteCarrera, CarreraMateriaPensum).
- Revisar `UserFactory.php` — actualmente usa `name/email` por defecto, incompatible con `usuarios`.
- Las rutas que apunta `IndexPage.vue` (`/usuarios`, `/cursos`, `/perfil`, etc.) ya están definidas en `web.php` ✅.
- `README.md` sigue siendo el default de Laravel — considerar customizarlo.

---

*Fin del contexto. Cualquier LLM que lea este archivo debe poder generar código consistente con el proyecto sin necesidad de reexplorarlo.*
