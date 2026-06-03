# AGENTS.md — Sistema Escolar

Referencia detallada en `CONTEXT.md`. Solo lo que un agente podría errar sin ayuda.

## Comandos esenciales
- `composer setup` — setup completo (install, .env, key:generate, migrate, npm install, build)
- `composer dev` — paralelo: artisan serve + queue:listen + pail logs + Vite
- `composer test` — corre `artisan config:clear && artisan test` (PHPUnit)
- `npm run build` / `npm run dev` — Vite standalone

## Arquitectura
- **Repository → Service → Controller**: Controllers son thin (llaman service + try/catch). Sin lógica de negocio ni Eloquent directo.
- Servicios orquestan repos, usan `DB::transaction()` para writes multi-tabla, lanzan `RuntimeException`.
- Repositorios **sin interfaces** (excepción: `ReporteStrategyInterface`).
- DI con `readonly` promoted properties en constructores.

## DB y modelos
- PKs `Id*` (`IdUsuario`, `IdCurso`, etc.). `public $timestamps = false;` siempre.
- Columnas en español (`Correo`, `Contrasena`, `Nombre1`, `Apellido1`, `CI`, `Estado`).
- Roles: `1 = admin`, `2 = docente`, `3 = estudiante`.
- **Modelos Eloquent faltantes** (migraciones/seeders ya existen): Carrera, Modalidad, Pensum, EstudianteCarrera, CarreraMateriaPensum.
- ⚠️ `UserFactory.php` usa `name/email` por defecto — incompatible con columnas reales.

## API
- Shape: `{ "status": bool, "message": "texto en español", "data": {}|[] }`.
- Sanctum Bearer token en `localStorage` como `auth_token`.
- `ValidationException`/`AuthenticationException` globales en `bootstrap/app.php`.

## Dashboard (ya existe, NO crear otro)
- Ruta: `GET /api/auth/dashboard/stats` → `DashboardService->getStats(userId, roleId)`.
- **Un solo Dashboard** que retorna data distinta según el rol via `switch ($idRol)`.
- Componente `Dashboard.vue` montado en `/dashboard`.
- NO crear nuevos endpoints/vistas. Extender `DashboardService`.

## Flujo de inscripción de estudiantes (RF05)
- **Frontend**: `InscripcionMaterias.vue` (`/cursos/inscripcion`) llama `GET /api/estudiante/cursos/disponibles`.
- **Backend**: `EstudianteRepository@getMateriasPorModalidad`:
  1. Obtiene `IdCarrera` + `IdModalidad` desde `estudiantecarrera`
  2. Filtra materias del Pensum filtrado por Carrera + Modalidad
  3. Excluye materias ya inscritas
  4. Valida prerrequisitos (`IdMateriaPrevia` aprobada con nota >= 51)
  5. Cruza con `cursos_materias`, `cursos`, `turnos`, `materias`
  6. Retorna: `IdCursoMateria, Curso, Aula, Piso, Materia, CodigoMateria, Turno (Nombre), FechaInicio, FechaFin`
- **Inscripción**: `POST /api/estudiante/cursos/inscribir` con `{ IdCursoMateria }`.

### ⚠️ Lógica de horarios (punto crítico)
- La tabla `turnos` tiene `HoraInicio`, `HoraFin`, y flags por día (`Lun`-`Dom`), pero la app **solo usa `turnos.Nombre`** para mostrar y detectar conflictos.
- **Detección de conflicto**: `verificarCruceODuplicado` compara solo `IdTurno` → rechaza si ya hay otra materia en el **mismo `IdTurno`**, sin considerar horas reales ni solapamiento de días.
- **Modalidades** (`modalidad` tabla): Modular (4 sem, max 2 mat), Semestral (24 sem, max 6 mat), Anual (48 sem, max 10 mat). El `Pensum` se filtra por `IdModalidad`, pero **no hay verificación** de `MaxMaterias` ni duración al inscribir.
- **Ausente**: validación de cruce horario real, límite de materias por modalidad, ni distinción Semestral/Modular en vistas.

## Frontend
- Vue 3, JS plano, Tailwind v4 (`@import 'tailwindcss'`).
- Componentes montados por ID en `resources/js/app.js` (array `mounts`).
- Axios global con interceptor 401 → redirect `/`.
- Dependencias: GSAP, `@lucide/vue`.

## Auth y middleware
- `auth:sanctum` para rutas protegidas.
- `admin.role` → `IdRol === 1`. `teacher.role` → `IdRol === 2`. Ambos existen.
- Rutas docente en `routes/api.php` usan `teacher.role`.

## Rutas
- `routes/estudiantes_api.php` montado aparte vía `then()` en `bootstrap/app.php`.
- `routes/web.php` solo devuelve vistas Blade.

## Gotchas
- `README.md` es boilerplate Laravel default.
- Tests son solo stubs (`tests/Feature|Unit/ExampleTest.php`).
