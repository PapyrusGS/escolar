<template>
  <section class="page">
    <header class="page__header">
      <div>
        <p class="eyebrow">RF05</p>
        <h1>Inscripcion a cursos</h1>
        <p class="lead">Revisa las ofertas disponibles y matriculate en la que corresponda a tu carrera.</p>
      </div>
      <a class="back-link" href="/index">Volver al panel</a>
    </header>

    <div v-if="error" class="alert alert--error">{{ error }}</div>
    <div v-if="success" class="alert alert--success">{{ success }}</div>

    <section class="filters">
      <input v-model="filters.search" class="input" type="text" placeholder="Buscar por curso, materia, docente o turno" @keyup.enter="loadData(1)" />
      <input v-model="filters.IdCurso" class="input" type="number" min="1" placeholder="Id Curso" @keyup.enter="loadData(1)" />
      <input v-model="filters.IdTurno" class="input" type="number" min="1" placeholder="Id Turno" @keyup.enter="loadData(1)" />
      <button class="button" @click="loadData(1)" :disabled="loading">{{ loading ? 'Buscando...' : 'Buscar' }}</button>
      <button class="button button--ghost" @click="clearFilters" :disabled="loading">Limpiar</button>
    </section>

    <section class="stats">
      <article class="stat">
        <span>Disponibles</span>
        <strong>{{ available.total }}</strong>
      </article>
      <article class="stat">
        <span>Mis inscripciones</span>
        <strong>{{ myEnrollments.length }}</strong>
      </article>
      <article class="stat">
        <span>Pagina</span>
        <strong>{{ available.current_page }} / {{ available.last_page }}</strong>
      </article>
    </section>

    <section class="panel">
      <div class="panel__header">
        <div>
          <h2>Cursos disponibles</h2>
          <p>Solo veras ofertas activas, con cupo y asociadas a tu carrera.</p>
        </div>
      </div>

      <div v-if="available.data.length" class="grid">
        <article v-for="item in available.data" :key="item.IdCursoMateria" class="offer">
          <div class="offer__top">
            <div>
              <p class="offer__meta">Oferta #{{ item.IdCursoMateria }}</p>
              <h3>{{ item.Curso.Nombre }}</h3>
            </div>
            <span class="badge" :class="{ 'badge--low': item.CupoDisponible <= 5 }">
              Cupo {{ item.CupoDisponible }}/{{ item.MaxInscritos }}
            </span>
          </div>

          <p class="offer__subject">{{ item.Materia.Nombre }}</p>
          <ul class="offer__list">
            <li><strong>Docente:</strong> {{ fullName(item.Docente) }}</li>
            <li><strong>Turno:</strong> {{ item.Turno.Nombre }} | {{ item.Turno.Dias }}</li>
            <li><strong>Horario:</strong> {{ formatTime(item.Turno.HoraInicio) }} - {{ formatTime(item.Turno.HoraFin) }}</li>
            <li><strong>Vigencia:</strong> {{ item.FechaInicio }} a {{ item.FechaFin }}</li>
          </ul>

          <button class="button button--wide" @click="enroll(item)" :disabled="loadingEnroll === item.IdCursoMateria">
            {{ loadingEnroll === item.IdCursoMateria ? 'Inscribiendo...' : 'Inscribirme' }}
          </button>
        </article>
      </div>

      <div v-else class="empty">
        No hay cursos disponibles para inscripcion en este momento.
      </div>

      <div class="pagination" v-if="available.last_page > 1">
        <button class="button button--ghost" @click="loadData(available.current_page - 1)" :disabled="available.current_page <= 1 || loading">
          Anterior
        </button>
        <span>Pagina {{ available.current_page }} de {{ available.last_page }}</span>
        <button class="button button--ghost" @click="loadData(available.current_page + 1)" :disabled="available.current_page >= available.last_page || loading">
          Siguiente
        </button>
      </div>
    </section>

    <section class="panel">
      <div class="panel__header">
        <div>
          <h2>Mis inscripciones</h2>
          <p>Resumen de las materias en las que ya estas inscrito.</p>
        </div>
      </div>

      <div v-if="myEnrollments.length" class="list">
        <article v-for="item in myEnrollments" :key="item.IdInscripcion" class="list__item">
          <div>
            <h3>{{ item.CursoMateria.Curso.Nombre }} - {{ item.CursoMateria.Materia.Nombre }}</h3>
            <p>{{ fullName(item.CursoMateria.Docente) }} | {{ item.CursoMateria.Turno.Nombre }} | {{ item.CursoMateria.Turno.Dias }}</p>
          </div>
          <span class="list__date">{{ formatDate(item.Fecha) }}</span>
        </article>
      </div>

      <div v-else class="empty">
        Aun no tienes inscripciones registradas.
      </div>
    </section>
  </section>
</template>

<script>
import axios from 'axios';

export default {
  name: 'EnrollmentPage',
  data() {
    return {
      loading: false,
      loadingEnroll: null,
      error: '',
      success: '',
      available: {
        data: [],
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
      },
      myEnrollments: [],
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
          per_page: this.available.per_page,
          search: this.filters.search,
          IdCurso: this.filters.IdCurso,
          IdTurno: this.filters.IdTurno,
        };

        const response = await axios.get('/api/inscripciones', { params });
        const payload = response.data.data;

        this.available = payload.available;
        this.myEnrollments = payload.my_inscriptions || [];
      } catch (error) {
        console.error(error);
        this.error = error.response?.data?.message || 'No se pudieron cargar las inscripciones.';

        if (error.response?.status === 401 || error.response?.status === 403) {
          window.location.href = '/index';
        }
      } finally {
        this.loading = false;
      }
    },
    async enroll(item) {
      try {
        this.loadingEnroll = item.IdCursoMateria;
        this.error = '';
        this.success = '';

        const response = await axios.post('/api/inscripciones', {
          IdCursoMateria: item.IdCursoMateria,
        });

        this.success = response.data.message || 'Inscripcion realizada correctamente.';
        await this.loadData(this.available.current_page || 1);
      } catch (error) {
        console.error(error);
        this.error = error.response?.data?.message || 'No se pudo completar la inscripcion.';
      } finally {
        this.loadingEnroll = null;
      }
    },
    clearFilters() {
      this.filters.search = '';
      this.filters.IdCurso = '';
      this.filters.IdTurno = '';
      this.loadData(1);
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
  color: #34d399;
}

h1, h2, h3, p {
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
  background: linear-gradient(135deg, #34d399, #fbbf24);
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

.alert--success {
  background: rgba(16, 185, 129, 0.16);
  color: #a7f3d0;
  border: 1px solid rgba(16, 185, 129, 0.25);
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

.button--wide {
  width: 100%;
  margin-top: 1rem;
  background: linear-gradient(135deg, #34d399, #fbbf24);
  color: #111827;
  border: 0;
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
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1rem;
}

.offer {
  padding: 1rem;
  border-radius: 18px;
  background: rgba(15, 23, 42, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.offer__top {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  align-items: flex-start;
}

.offer__meta {
  color: #fbbf24;
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  margin-bottom: 0.25rem;
}

.offer h3 {
  font-size: 1.15rem;
}

.offer__subject {
  margin: 0.75rem 0 0.85rem;
  color: #cbd5e1;
  font-weight: 600;
}

.offer__list {
  margin: 0;
  padding-left: 1rem;
  color: #d1d5db;
  display: grid;
  gap: 0.35rem;
}

.badge {
  padding: 0.35rem 0.7rem;
  border-radius: 999px;
  background: rgba(16, 185, 129, 0.18);
  color: #a7f3d0;
  font-size: 0.8rem;
  font-weight: 700;
}

.badge--low {
  background: rgba(251, 191, 36, 0.18);
  color: #fde68a;
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

.list {
  display: grid;
  gap: 0.75rem;
}

.list__item {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  padding: 0.95rem 1rem;
  border-radius: 16px;
  background: rgba(15, 23, 42, 0.55);
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.list__item p {
  color: #cbd5e1;
  margin-top: 0.3rem;
}

.list__date {
  color: #fbbf24;
  white-space: nowrap;
}

@media (max-width: 900px) {
  .page__header,
  .offer__top,
  .list__item {
    flex-direction: column;
  }

  .filters,
  .stats {
    grid-template-columns: 1fr;
  }
}
</style>
