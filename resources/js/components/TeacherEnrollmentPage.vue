<template>
  <section class="page">
    <header class="page__header">
      <div>
        <p class="eyebrow">Docente</p>
        <h1>Estudiantes inscritos</h1>
        <p class="lead">Revisa tus materias asignadas y retira estudiantes cuando corresponda.</p>
      </div>
      <a class="back-link" href="/index">Volver al panel</a>
    </header>

    <div v-if="error" class="alert alert--error">{{ error }}</div>
    <div v-if="success" class="alert alert--success">{{ success }}</div>

    <section class="panel">
      <div class="panel__header">
        <div>
          <h2>Mis cursos asignados</h2>
          <p>Selecciona una materia para ver sus estudiantes inscritos.</p>
        </div>
      </div>

      <div class="selectors">
        <select v-model="selectedId" class="input" @change="loadData(1)">
          <option v-for="assignment in assignments" :key="assignment.IdCursoMateria" :value="assignment.IdCursoMateria">
            #{{ assignment.IdCursoMateria }} - {{ assignment.Curso.Nombre }} / {{ assignment.Materia.Nombre }}
          </option>
        </select>

        <input
          v-model="filters.search"
          class="input"
          type="text"
          placeholder="Buscar por nombre, CI o correo"
          @keyup.enter="loadData(1)"
        />

        <button class="button" @click="loadData(1)" :disabled="loading">
          {{ loading ? 'Cargando...' : 'Filtrar' }}
        </button>
      </div>

      <div v-if="selectedAssignment" class="summary">
        <article class="summary__card">
          <span>Curso</span>
          <strong>{{ selectedAssignment.Curso.Nombre }}</strong>
        </article>
        <article class="summary__card">
          <span>Materia</span>
          <strong>{{ selectedAssignment.Materia.Nombre }}</strong>
        </article>
        <article class="summary__card">
          <span>Inscritos activos</span>
          <strong>{{ selectedAssignment.InscritosActivos }}</strong>
        </article>
        <article class="summary__card">
          <span>Cupo</span>
          <strong>{{ selectedAssignment.MaxInscritos }}</strong>
        </article>
      </div>
    </section>

    <section class="panel">
      <div class="panel__header">
        <div>
          <h2>Listado de estudiantes</h2>
          <p>Solo puedes retirar estudiantes de tus propias materias asignadas.</p>
        </div>
      </div>

      <div v-if="students.data.length" class="table-wrap">
        <table class="table">
          <thead>
            <tr>
              <th>Estudiante</th>
              <th>CI</th>
              <th>Correo</th>
              <th>Semestre</th>
              <th>Fecha</th>
              <th>Accion</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in students.data" :key="item.IdInscripcion">
              <td>{{ fullName(item.Estudiante) }}</td>
              <td>{{ item.Estudiante.CI }}</td>
              <td>{{ item.Estudiante.Correo }}</td>
              <td>{{ item.Estudiante.Semestre ?? '-' }}</td>
              <td>{{ formatDate(item.Fecha) }}</td>
              <td>
                <button class="button button--danger" @click="withdraw(item)" :disabled="withdrawing === item.IdInscripcion">
                  {{ withdrawing === item.IdInscripcion ? 'Retirando...' : 'Retirar' }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="empty">
        No hay estudiantes inscritos en esta materia.
      </div>

      <div class="pagination" v-if="students.last_page > 1">
        <button class="button button--ghost" @click="loadData(students.current_page - 1)" :disabled="students.current_page <= 1 || loading">
          Anterior
        </button>
        <span>Pagina {{ students.current_page }} de {{ students.last_page }}</span>
        <button class="button button--ghost" @click="loadData(students.current_page + 1)" :disabled="students.current_page >= students.last_page || loading">
          Siguiente
        </button>
      </div>
    </section>
  </section>
</template>

<script>
import axios from 'axios';

export default {
  name: 'TeacherEnrollmentPage',
  data() {
    return {
      loading: false,
      withdrawing: null,
      error: '',
      success: '',
      assignments: [],
      selectedAssignment: null,
      selectedId: '',
      students: {
        data: [],
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
      },
      filters: {
        search: '',
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
          per_page: this.students.per_page,
          search: this.filters.search,
          IdCursoMateria: this.selectedId || undefined,
        };

        const response = await axios.get('/api/docente/inscripciones', { params });
        const payload = response.data.data;

        this.assignments = payload.assignments?.data || [];
        this.selectedAssignment = payload.selected_assignment || null;
        this.students = payload.students || this.students;

        if (!this.selectedId && this.selectedAssignment) {
          this.selectedId = this.selectedAssignment.IdCursoMateria;
        }
      } catch (error) {
        console.error(error);
        this.error = error.response?.data?.message || 'No se pudo cargar el panel del docente.';

        if (error.response?.status === 401 || error.response?.status === 403) {
          window.location.href = '/index';
        }
      } finally {
        this.loading = false;
      }
    },
    async withdraw(item) {
      try {
        this.withdrawing = item.IdInscripcion;
        this.error = '';
        this.success = '';

        await axios.patch(`/api/docente/inscripciones/${item.IdInscripcion}/retirar`);

        this.success = 'Estudiante retirado correctamente.';
        await this.loadData(this.students.current_page || 1);
      } catch (error) {
        console.error(error);
        this.error = error.response?.data?.message || 'No se pudo retirar al estudiante.';
      } finally {
        this.withdrawing = null;
      }
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
  color: #38bdf8;
}

h1, h2, p {
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
  background: linear-gradient(135deg, #38bdf8, #fbbf24);
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

.panel {
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 20px;
  padding: 1rem;
  display: grid;
  gap: 1rem;
}

.panel__header p {
  color: #cbd5e1;
  margin-top: 0.35rem;
}

.selectors {
  display: grid;
  grid-template-columns: 1.5fr 1.5fr auto;
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

.button--danger {
  background: linear-gradient(135deg, #ef4444, #f97316);
  color: #fff;
  border: 0;
}

.summary {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 0.75rem;
}

.summary__card {
  background: rgba(15, 23, 42, 0.55);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  padding: 1rem;
  display: grid;
  gap: 0.35rem;
}

.summary__card span {
  color: #cbd5e1;
}

.table-wrap {
  overflow-x: auto;
}

.table {
  width: 100%;
  border-collapse: collapse;
}

.table th,
.table td {
  padding: 0.85rem 0.75rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  text-align: left;
}

.table th {
  color: #fbbf24;
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
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
  .selectors,
  .summary {
    grid-template-columns: 1fr;
    display: grid;
  }
}
</style>
