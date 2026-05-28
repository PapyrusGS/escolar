<template>
  <section class="page">
    <header class="page__header">
      <div>
        <p class="eyebrow">RF06</p>
        <h1>Visualizacion de cursos</h1>
        <p class="lead">{{ roleLabel }} | Consulta los cursos disponibles segun tu perfil.</p>
      </div>
      <a class="back-link" href="/index">Volver al panel</a>
    </header>

    <div v-if="error" class="alert alert--error">{{ error }}</div>

    <section class="filters">
      <input v-model="filters.search" class="input" type="text" placeholder="Buscar por curso, materia, docente o turno" @keyup.enter="loadData(1)" />
      <input v-model="filters.IdCurso" class="input" type="number" min="1" placeholder="Id Curso" @keyup.enter="loadData(1)" />
      <input v-model="filters.IdTurno" class="input" type="number" min="1" placeholder="Id Turno" @keyup.enter="loadData(1)" />
      <button class="button" @click="loadData(1)" :disabled="loading">{{ loading ? 'Buscando...' : 'Buscar' }}</button>
      <button class="button button--ghost" @click="clearFilters" :disabled="loading">Limpiar</button>
    </section>

    <section class="stats">
      <article class="stat">
        <span>Elementos</span>
        <strong>{{ courses.total }}</strong>
      </article>
      <article class="stat">
        <span>Pagina</span>
        <strong>{{ courses.current_page }} / {{ courses.last_page }}</strong>
      </article>
      <article class="stat">
        <span>Rol</span>
        <strong>{{ roleLabel }}</strong>
      </article>
    </section>

    <section class="layout">
      <div class="panel">
        <div class="panel__header">
          <div>
            <h2>Listado</h2>
            <p>{{ listCaption }}</p>
          </div>
        </div>

        <div v-if="courses.data.length" class="grid">
          <button
            v-for="item in courses.data"
            :key="keyForItem(item)"
            class="course-card"
            :class="{ 'course-card--active': isSelected(item) }"
            @click="selectItem(item)"
          >
            <p class="course-card__meta">{{ item.type === 'inscripcion' ? 'Inscripcion' : 'Curso materia' }} #{{ keyForItem(item) }}</p>
            <h3>{{ item.Curso.Nombre }}</h3>
            <p class="course-card__subject">{{ item.Materia.Nombre }}</p>
            <div class="course-card__footer">
              <span>{{ item.Turno.Nombre }}</span>
              <span v-if="item.CupoDisponible !== undefined">Cupo {{ item.CupoDisponible }}</span>
            </div>
          </button>
        </div>

        <div v-else class="empty">
          No hay cursos para mostrar con los filtros actuales.
        </div>

        <div class="pagination" v-if="courses.last_page > 1">
          <button class="button button--ghost" @click="loadData(courses.current_page - 1)" :disabled="courses.current_page <= 1 || loading">
            Anterior
          </button>
          <span>Pagina {{ courses.current_page }} de {{ courses.last_page }}</span>
          <button class="button button--ghost" @click="loadData(courses.current_page + 1)" :disabled="courses.current_page >= courses.last_page || loading">
            Siguiente
          </button>
        </div>
      </div>

      <aside class="panel panel--detail">
        <div class="panel__header">
          <div>
            <h2>Detalle</h2>
            <p>Informacion relevante del curso seleccionado.</p>
          </div>
        </div>

        <div v-if="selected" class="detail">
          <div class="detail__hero">
            <p class="detail__eyebrow">{{ selected.type === 'inscripcion' ? 'Mi curso inscrito' : 'Oferta activa' }}</p>
            <h3>{{ selected.Curso.Nombre }}</h3>
            <p>{{ selected.Materia.Nombre }}</p>
          </div>

          <dl class="detail__list">
            <template v-if="selected.type === 'curso_materia'">
              <div>
                <dt>Docente</dt>
                <dd>{{ fullName(selected.Docente) }}</dd>
              </div>
              <div>
                <dt>Turno</dt>
                <dd>{{ selected.Turno.Nombre }} | {{ selected.Turno.Dias }}</dd>
              </div>
              <div>
                <dt>Horario</dt>
                <dd>{{ formatTime(selected.Turno.HoraInicio) }} - {{ formatTime(selected.Turno.HoraFin) }}</dd>
              </div>
              <div>
                <dt>Vigencia</dt>
                <dd>{{ selected.FechaInicio }} a {{ selected.FechaFin }}</dd>
              </div>
              <div>
                <dt>Cupo</dt>
                <dd>{{ selected.InscritosActivos }}/{{ selected.MaxInscritos }} inscritos</dd>
              </div>
              <div>
                <dt>Cupo disponible</dt>
                <dd>{{ selected.CupoDisponible }}</dd>
              </div>
              <div>
                <dt>Estado</dt>
                <dd>{{ selected.Estado ? 'Activo' : 'Inactivo' }}</dd>
              </div>
            </template>

            <template v-else>
              <div>
                <dt>Curso</dt>
                <dd>{{ selected.CursoMateria.Curso.Nombre }}</dd>
              </div>
              <div>
                <dt>Materia</dt>
                <dd>{{ selected.CursoMateria.Materia.Nombre }}</dd>
              </div>
              <div>
                <dt>Docente</dt>
                <dd>{{ fullName(selected.CursoMateria.Docente) }}</dd>
              </div>
              <div>
                <dt>Turno</dt>
                <dd>{{ selected.CursoMateria.Turno.Nombre }} | {{ selected.CursoMateria.Turno.Dias }}</dd>
              </div>
              <div>
                <dt>Horario</dt>
                <dd>{{ formatTime(selected.CursoMateria.Turno.HoraInicio) }} - {{ formatTime(selected.CursoMateria.Turno.HoraFin) }}</dd>
              </div>
              <div>
                <dt>Fecha de inscripcion</dt>
                <dd>{{ formatDate(selected.FechaInscripcion) }}</dd>
              </div>
              <div>
                <dt>Vigencia</dt>
                <dd>{{ selected.CursoMateria.FechaInicio }} a {{ selected.CursoMateria.FechaFin }}</dd>
              </div>
            </template>
          </dl>

          <div v-if="selected.Estudiantes?.length" class="detail__students">
            <h4>Estudiantes</h4>
            <ul>
              <li v-for="student in selected.Estudiantes" :key="student.IdInscripcion">
                {{ fullName(student.Estudiante) }} | CI {{ student.Estudiante.CI }}
              </li>
            </ul>
          </div>
        </div>

        <div v-else class="empty">
          Selecciona un curso para ver sus detalles.
        </div>
      </aside>
    </section>
  </section>
</template>

<script>
import axios from 'axios';

export default {
  name: 'CourseVisualizationPage',
  data() {
    return {
      loading: false,
      error: '',
      roleLabel: 'Usuario',
      listCaption: 'Selecciona un elemento para ver su informacion detallada.',
      courses: {
        data: [],
        current_page: 1,
        last_page: 1,
        per_page: 8,
        total: 0,
      },
      selected: null,
      filters: {
        search: '',
        IdCurso: '',
        IdTurno: '',
      },
    };
  },
  mounted() {
    const token = localStorage.getItem('auth_token');

    if (!token) {
      window.location.href = '/';
      return;
    }

    axios.defaults.headers.common.Authorization = `Bearer ${token}`;
    this.loadData(1);
  },
  methods: {
    async loadData(page = 1) {
      try {
        this.loading = true;
        this.error = '';

        const params = {
          page,
          per_page: this.courses.per_page,
          search: this.filters.search,
          IdCurso: this.filters.IdCurso,
          IdTurno: this.filters.IdTurno,
          selected: this.selected ? this.keyForItem(this.selected) : undefined,
        };

        const response = await axios.get('/api/visualizacion-cursos', { params });
        const payload = response.data.data;

        this.roleLabel = payload.role || 'Usuario';
        this.courses = payload.courses || this.courses;
        this.selected = payload.selected || this.courses.data[0] || null;
      } catch (error) {
        console.error(error);
        this.error = error.response?.data?.message || 'No se pudo cargar la visualizacion de cursos.';

        if (error.response?.status === 401) {
          window.location.href = '/';
        }
      } finally {
        this.loading = false;
      }
    },
    async selectItem(item) {
      this.selected = item;
      await this.loadData(this.courses.current_page || 1);
    },
    clearFilters() {
      this.filters.search = '';
      this.filters.IdCurso = '';
      this.filters.IdTurno = '';
      this.loadData(1);
    },
    keyForItem(item) {
      return item.type === 'inscripcion' ? item.IdInscripcion : item.IdCursoMateria;
    },
    isSelected(item) {
      if (!this.selected) {
        return false;
      }

      return this.keyForItem(item) === this.keyForItem(this.selected);
    },
    fullName(person) {
      if (!person) {
        return '-';
      }

      return [person.Nombre1, person.Nombre2, person.Apellido1, person.Apellido2]
        .filter(Boolean)
        .join(' ');
    },
    formatDate(value) {
      if (!value) {
        return '-';
      }

      return new Intl.DateTimeFormat('es-BO', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
      }).format(new Date(value));
    },
    formatTime(value) {
      if (!value) {
        return '-';
      }

      return String(value).slice(0, 5);
    },
  },
};
</script>

<style scoped>
.page {
  display: grid;
  gap: 1.25rem;
  color: #e5e7eb;
}

.page__header {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  align-items: flex-start;
}

.eyebrow {
  margin: 0 0 0.35rem;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  font-size: 0.72rem;
  color: #60a5fa;
}

h1, h2, h3, h4, p, dl {
  margin: 0;
}

h1 {
  font-size: 2rem;
}

.lead {
  margin-top: 0.5rem;
  color: #cbd5e1;
}

.back-link {
  align-self: center;
  text-decoration: none;
  color: #111827;
  background: linear-gradient(135deg, #60a5fa, #fbbf24);
  font-weight: 700;
  padding: 0.8rem 1rem;
  border-radius: 999px;
}

.alert {
  border-radius: 16px;
  padding: 0.9rem 1rem;
  font-weight: 600;
}

.alert--error {
  background: rgba(239, 68, 68, 0.16);
  color: #fecaca;
  border: 1px solid rgba(239, 68, 68, 0.25);
}

.filters {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr auto auto;
  gap: 0.75rem;
}

.input,
.button {
  border-radius: 14px;
  border: 1px solid rgba(255, 255, 255, 0.08);
  background: rgba(255, 255, 255, 0.06);
  color: #e5e7eb;
  padding: 0.85rem 1rem;
}

.button {
  cursor: pointer;
  font-weight: 700;
}

.button:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.button--ghost {
  background: transparent;
}

.stats {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 0.75rem;
}

.stat,
.panel {
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 20px;
}

.stat {
  padding: 1rem;
  display: grid;
  gap: 0.35rem;
}

.stat span {
  color: #cbd5e1;
}

.stat strong {
  font-size: 1.4rem;
}

.layout {
  display: grid;
  grid-template-columns: 1.4fr 1fr;
  gap: 1rem;
}

.panel {
  padding: 1rem;
  display: grid;
  gap: 1rem;
}

.panel__header p {
  color: #cbd5e1;
  margin-top: 0.35rem;
}

.grid {
  display: grid;
  gap: 0.75rem;
}

.course-card {
  width: 100%;
  text-align: left;
  border: 1px solid rgba(255, 255, 255, 0.08);
  background: rgba(15, 23, 42, 0.55);
  color: #e5e7eb;
  border-radius: 18px;
  padding: 1rem;
  cursor: pointer;
}

.course-card--active {
  border-color: rgba(251, 191, 36, 0.45);
  box-shadow: 0 0 0 1px rgba(251, 191, 36, 0.18);
}

.course-card__meta {
  font-size: 0.76rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #fbbf24;
  margin-bottom: 0.3rem;
}

.course-card__subject {
  color: #cbd5e1;
  margin-top: 0.45rem;
}

.course-card__footer {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  margin-top: 0.85rem;
  color: #93c5fd;
  font-size: 0.9rem;
}

.detail {
  display: grid;
  gap: 1rem;
}

.detail__hero {
  background: rgba(15, 23, 42, 0.55);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 18px;
  padding: 1rem;
}

.detail__eyebrow {
  color: #fbbf24;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.76rem;
  margin-bottom: 0.35rem;
}

.detail__hero p:last-child {
  color: #cbd5e1;
  margin-top: 0.35rem;
}

.detail__list {
  display: grid;
  gap: 0.75rem;
}

.detail__list div {
  padding: 0.85rem 1rem;
  border-radius: 16px;
  background: rgba(15, 23, 42, 0.5);
}

.detail__list dt {
  font-size: 0.76rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #fbbf24;
}

.detail__list dd {
  margin: 0.35rem 0 0;
  color: #e5e7eb;
}

.detail__students {
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  padding-top: 1rem;
}

.detail__students ul {
  margin: 0.75rem 0 0;
  padding-left: 1rem;
  color: #cbd5e1;
  display: grid;
  gap: 0.35rem;
}

.empty {
  padding: 1rem;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.04);
  color: #cbd5e1;
}

.pagination {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}

@media (max-width: 900px) {
  .page__header,
  .filters,
  .stats,
  .layout {
    grid-template-columns: 1fr;
    display: grid;
  }
}
</style>
