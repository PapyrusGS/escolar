# AGENTS.md

## Stack
Laravel 12, PHP 8.2+, MySQL, Vue 3 (Options + Composition API), Tailwind CSS 4, Vite 7.

## Commands
| Command | Action |
|---|---|
| `composer setup` | Full install: deps, .env, key, migrate, npm install & build |
| `composer dev` | Parallel dev servers (artisan serve, queue, logs, Vite) |
| `composer test` | Run PHP tests |
| `npm run dev` | Vite dev server only |
| `npm run build` | Production build |

## Database
- **MySQL** — `.env` overrides the SQLite default in `.env.example`
- Custom PKs: `IdUsuario`, `IdRol`, `IdCarrera`, etc. (`Id*` convention on all 18 tables) — timestamps **disabled**
- Spanish names: `Correo`, `Contrasena`, `Nombre1`, `Apellido1`, `CI`
- Session/cache/queue use `database` driver
- Tables: `roles`, `usuarios`, `carreras`, `materias`, `pensum`, `cursos`, `cursos_materias`, `turnos`, `modalidad`, `notificaciones`, `inscripciones`, `notas`, `estudiante_carrera`, `carrera_materia_pensum`

## Architecture
- **Repositories** (`app/Repositories/`) → **Services** (`app/Services/`) → **Controllers** (`app/Http/Controllers/API/`)
- Constructor DI with `readonly` promoted properties (PHP 8.2+)
- Validation in **FormRequests** (`app/Http/Requests/`)
- Standard JSON shape: `{ status: bool, message: string, data: mixed }`
- Auth: Sanctum tokens; custom field `Contrasena` (model overrides `getAuthPassword()`)
- Exception handling in `bootstrap/app.php`: `ValidationException` → 422, `AuthenticationException` → 401
- Middleware `admin.role` checks `IdRol === 1`
- Repositories: `UsuarioRepository`, `RolRepository`, `CarreraRepository`, `ModalidadRepository`, `EstudianteCarreraRepository`
- Services: `AuthService`, `UsuarioService`
- Controllers: `API\AuthController` (login/logout/perfil), `API\UsuarioController` (formData/store)
- FormRequests: `Auth\LoginRequest`, `Usuario\StoreUsuarioRequest`

## Frontend
- Vue 3 components mounted on Blade containers (`#login-app`, `#index-app`, `#user-create-app`)
- Entry: `resources/js/app.js` → `bootstrap.js` + components
- Styles: Tailwind v4 (`@import 'tailwindcss'` in `resources/css/app.css`)
- Plain JS Vue (no TypeScript)

## Seeders
15 seeders in `DatabaseSeeder.php`: `RolSeeder` (3 roles), `ModalidadSeeder`, `TurnoSeeder`, `CarreraSeeder` (5 carreras), `MateriaSeeder`, `PensumSeeder`, `CarreraMateriaPensumSeeder`, `UsuarioSeeder` (16 users: 2 admin, 4 docente, 10 estudiante — all passwords `123456`), `EstudianteCarreraSeeder`, `CursoSeeder`, `CursoMateriaSeeder`, `InscripcionSeeder`, `NotaSeeder`, `NotificacionSeeder`.

## Gotchas
- `EnsureTeacherRole.php` middleware registered but **not yet created**
- No CI, no lint/formatter config files (Pint in deps but no config)
- Tests are stubs only
- Missing Eloquent models for ~11 tables (only `User` and `Rol` exist)
- `UserFactory.php` uses default `name`/`email` — incompatible with `usuarios` schema
- `IndexPage.vue` references routes (`/usuarios`, `/cursos`, `/perfil`, etc.) not yet defined in `web.php`
- `README.md` is the default Laravel README (not customized)
