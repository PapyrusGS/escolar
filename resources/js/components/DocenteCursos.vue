<template>
  <section class="docente-cursos">
    <header class="docente-cursos__header">
      <div>
        <p class="eyebrow">RF06 • Mis Cursos</p>
        <h1>Materias y Cursos Asignados</h1>
        <p>Consulta las materias que impartes, horarios y alumnos inscritos.</p>
      </div>
      <div class="header-actions">
        <a class="btn-action btn-action--primary" href="/docente/notas">📝 Gestionar Notas</a>
        <a class="btn-action btn-action--secondary" href="/dashboard">Volver al Panel</a>
      </div>
    </header>

    <div v-if="message" class="alert" :class="`alert--${messageType}`">
      {{ message }}
      <button class="alert__close" @click="message = ''">&times;</button>
    </div>

    <!-- Estado de carga -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Cargando tus cursos asignados...</p>
    </div>

    <!-- Sin cursos -->
    <div v-else-if="cursos.length === 0" class="empty-state">
      <div class="empty-state__icon">📚</div>
      <h2>No tienes cursos asignados</h2>
      <p>Contacta al administrador para que te asigne materias en el ciclo lectivo actual.</p>
    </div>

    <!-- Cuadrícula de cursos -->
    <div v-else class="courses-grid">
      <div v-for="c in cursos" :key="c.IdCursoMateria" class="course-card">
        <div class="course-card__header">
          <div>
            <span class="course-code">{{ c.Materia.CodigoMateria }}</span>
            <h3>{{ c.Materia.Nombre }}</h3>
          </div>
          <span class="course-slots" :class="{ 'course-slots--full': c.Inscritos >= c.MaxInscritos }">
            {{ c.Inscritos }}/{{ c.MaxInscritos }}
          </span>
        </div>

        <div class="course-card__body">
          <div class="info-row">
            <span class="info-icon">📍</span>
            <span><strong>Aula:</strong> {{ c.Curso.Nombre }} (Piso {{ c.Curso.Piso }})</span>
          </div>
          <div class="info-row">
            <span class="info-icon">🕒</span>
            <span>
              <strong>Turno:</strong>
              <span class="badge badge--turno">{{ c.Turno.Nombre }}</span>
              {{ formatTime(c.Turno.HoraInicio) }} - {{ formatTime(c.Turno.HoraFin) }}
            </span>
          </div>
          <div class="info-row">
            <span class="info-icon">📅</span>
            <span><strong>Vigencia:</strong> {{ formatDate(c.FechaInicio) }} al {{ formatDate(c.FechaFin) }}</span>
          </div>
        </div>

        <div class="course-card__actions">
          <button class="btn-action-card btn-action-card--primary" @click="verAlumnos(c)">
            👥 Ver Alumnos ({{ c.Inscritos }})
          </button>
          <a class="btn-action-card btn-action-card--secondary" :href="`/docente/notas?curso=${c.IdCursoMateria}`">
            📝 Gestionar Notas
          </a>
        </div>
      </div>
    </div>

    <!-- Modal: Alumnos Inscritos -->
    <div v-if="showAlumnosModal" class="modal-overlay" @click.self="closeAlumnosModal">
      <div class="modal-card">
        <header class="modal-card__header">
          <div>
            <h2>Alumnos Inscritos</h2>
            <p class="modal-subtitle">{{ selectedCurso?.Materia?.Nombre }} — {{ selectedCurso?.Materia?.CodigoMateria }}</p>
          </div>
          <button class="btn-close-modal" @click="closeAlumnosModal">&times;</button>
        </header>
        <div class="modal-card__body">
          <div v-if="loadingAlumnos" class="modal-loading">
            <div class="mini-spinner"></div>
            <p>Cargando alumnos inscritos...</p>
          </div>
          <div v-else-if="alumnos.length === 0" class="empty-modal">
            <p>No hay alumnos inscritos en este curso.</p>
          </div>
          <div v-else class="table-container">
            <table class="students-table">
              <thead>
                <tr>
                  <th>Estudiante</th>
                  <th>CI</th>
                  <th>Correo</th>
                  <th>Inscripto el</th>
                  <th>Nota</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="a in alumnos" :key="a.IdUsuario">
                  <td>
                    <div class="student-cell">
                      <span class="student-name">{{ a.Nombre }}</span>
                    </div>
                  </td>
                  <td>{{ a.CI }}</td>
                  <td>{{ a.Correo }}</td>
                  <td>{{ formatDate(a.FechaInscripcion) }}</td>
                  <td>
                    <span v-if="a.EstadoNota" class="nota-badge" :class="a.Aprobado ? 'nota-badge--approved' : 'nota-badge--failed'">
                      {{ a.Nota }}
                    </span>
                    <span v-else class="nota-badge nota-badge--pending">Sin nota</span>
                  </td>
                  <td>
                    <span class="status-chip" :class="a.Aprobado ? 'status-chip--approved' : 'status-chip--pending'">
                      {{ a.Aprobado ? 'Aprobado' : 'Cursando' }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import axios from 'axios';

export default {
  name: 'DocenteCursos',
  data() {
    return {
      cursos: [],
      loading: false,
      message: '',
      messageType: 'error',

      // Modal alumnos
      showAlumnosModal: false,
      selectedCurso: null,
      alumnos: [],
      loadingAlumnos: false,
    };
  },
  mounted() {
    this.init();
  },
  methods: {
    async init() {
      this.loading = true;
      const token = localStorage.getItem('auth_token');
      if (token) {
        axios.defaults.headers.common.Authorization = `Bearer ${token}`;
      } else {
        window.location.href = '/';
        return;
      }
      await this.loadCursos();
      this.loading = false;
    },
    async loadCursos() {
      try {
        const { data } = await axios.get('/api/docente/cursos');
        this.cursos = data?.data ?? [];
      } catch (error) {
        this.setMessage('No se pudieron cargar los cursos asignados.', 'error');
      }
    },
    async verAlumnos(curso) {
      this.selectedCurso = curso;
      this.showAlumnosModal = true;
      this.loadingAlumnos = true;
      this.alumnos = [];
      try {
        const { data } = await axios.get(`/api/docente/cursos/${curso.IdCursoMateria}/alumnos`);
        this.alumnos = data?.data ?? [];
      } catch (error) {
        this.setMessage('No se pudieron cargar los alumnos inscritos.', 'error');
      } finally {
        this.loadingAlumnos = false;
      }
    },
    closeAlumnosModal() {
      this.showAlumnosModal = false;
      this.selectedCurso = null;
      this.alumnos = [];
    },
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' });
    },
    formatTime(timeString) {
      if (!timeString) return '';
      return timeString.substring(0, 5);
    },
    setMessage(msg, type = 'error') {
      this.message = msg;
      this.messageType = type;
      window.scrollTo({ top: 0, behavior: 'smooth' });
    },
  },
};
</script>

<style scoped>
.docente-cursos { min-height: 100vh; padding: 32px; background: linear-gradient(180deg, #07111f 0%, #101b2b 100%); color: #eef2ff; }
.docente-cursos__header { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 32px; }
.eyebrow { margin: 0 0 8px; color: #38bdf8; text-transform: uppercase; letter-spacing: .18em; font-size: .75rem; }
h1 { margin: 0; font-size: 2rem; }
p { margin: 8px 0 0; color: #cbd5e1; }
.header-actions { display: flex; gap: 12px; }

.btn-action { border-radius: 999px; padding: 12px 24px; font-weight: 700; text-decoration: none; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; }
.btn-action--primary { background: #38bdf8; color: #0f172a; }
.btn-action--primary:hover { background: #0ea5e9; }
.btn-action--secondary { background: transparent; border: 1px solid rgba(148, 163, 184, .22); color: #cbd5e1; }

.alert { position: relative; margin: 20px 0; padding: 16px 40px 16px 18px; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; }
.alert--success { background: rgba(16, 185, 129, .16); color: #d1fae5; border: 1px solid rgba(16, 185, 129, .3); }
.alert--error { background: rgba(239, 68, 68, .16); color: #fecaca; border: 1px solid rgba(239, 68, 68, .3); }
.alert__close { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: transparent; border: none; font-size: 1.5rem; color: inherit; cursor: pointer; }

.loading-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 80px 40px; }
.spinner { width: 40px; height: 40px; border: 4px solid rgba(56, 189, 248, 0.1); border-top-color: #38bdf8; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 16px; }

.empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 80px 40px; background: rgba(15, 23, 42, 0.4); border: 1px dashed rgba(148, 163, 184, 0.2); border-radius: 24px; }
.empty-state__icon { font-size: 3rem; margin-bottom: 16px; }
.empty-state h2 { margin: 0 0 10px; color: #f8fafc; font-size: 1.4rem; }
.empty-state p { margin: 0; color: #cbd5e1; max-width: 460px; line-height: 1.6; }

.courses-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 20px; }
.course-card { background: rgba(15, 23, 42, .86); border: 1px solid rgba(148, 163, 184, .18); border-radius: 20px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 10px 25px rgba(0, 0, 0, .1); transition: all 0.25s; }
.course-card:hover { transform: translateY(-3px); border-color: rgba(56, 189, 248, 0.3); box-shadow: 0 12px 30px rgba(0, 0, 0, .2); }

.course-card__header { padding: 16px 20px; background: rgba(30, 41, 59, 0.4); border-bottom: 1px solid rgba(148, 163, 184, 0.08); display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }
.course-code { font-family: monospace; font-size: 0.8rem; color: #38bdf8; font-weight: 700; text-transform: uppercase; }
.course-card__header h3 { margin: 2px 0 0; font-size: 1.05rem; color: #f8fafc; line-height: 1.4; }
.course-slots { font-size: 0.78rem; font-weight: 700; padding: 4px 10px; border-radius: 999px; background: rgba(52, 211, 153, 0.15); color: #34d399; border: 1px solid rgba(52, 211, 153, 0.3); }
.course-slots--full { background: rgba(239, 68, 68, 0.15); color: #f87171; border-color: rgba(239, 68, 68, 0.3); }

.course-card__body { padding: 20px; flex: 1; display: flex; flex-direction: column; gap: 12px; }
.info-row { display: flex; align-items: flex-start; gap: 10px; font-size: 0.88rem; color: #e2e8f0; line-height: 1.4; }
.info-icon { font-size: 1rem; width: 16px; text-align: center; }
.badge { display: inline-flex; align-items: center; justify-content: center; padding: 3px 8px; border-radius: 999px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; margin-right: 6px; }
.badge--turno { background: rgba(139, 92, 246, .16); color: #c4b5fd; border: 1px solid rgba(139, 92, 246, .3); }

.course-card__actions { padding: 12px 20px 20px; display: flex; gap: 10px; }
.btn-action-card { flex: 1; border-radius: 12px; padding: 10px; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; text-decoration: none; text-align: center; }
.btn-action-card--primary { background: rgba(56, 189, 248, 0.12); border: 1px solid rgba(56, 189, 248, 0.3); color: #38bdf8; }
.btn-action-card--primary:hover { background: #38bdf8; color: #0f172a; }
.btn-action-card--secondary { background: rgba(255, 255, 255, 0.06); color: #cbd5e1; border: 1px solid rgba(148, 163, 184, 0.2); }
.btn-action-card--secondary:hover { background: rgba(255, 255, 255, 0.1); color: #f8fafc; }

/* Modal */
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(7, 11, 25, 0.85); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px; backdrop-filter: blur(8px); }
.modal-card { background: #0f172a; border: 1px solid rgba(148, 163, 184, .25); border-radius: 24px; width: 100%; max-width: 900px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); animation: zoomIn 0.22s ease-out; }
.modal-card__header { padding: 20px 24px; border-bottom: 1px solid rgba(148, 163, 184, .14); display: flex; justify-content: space-between; align-items: center; background: rgba(30, 41, 59, .4); }
.modal-card__header h2 { margin: 0; font-size: 1.25rem; color: #f8fafc; }
.modal-subtitle { margin: 4px 0 0; color: #94a3b8; font-size: 0.88rem; }
.btn-close-modal { background: transparent; border: none; color: #94a3b8; font-size: 2rem; cursor: pointer; line-height: 1; }
.btn-close-modal:hover { color: #f8fafc; }
.modal-card__body { padding: 24px; overflow-y: auto; max-height: 70vh; }
.modal-loading { display: flex; flex-direction: column; align-items: center; padding: 40px; }
.mini-spinner { width: 28px; height: 28px; border: 3px solid rgba(56, 189, 248, 0.1); border-top-color: #38bdf8; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 12px; }
.empty-modal { padding: 40px; text-align: center; color: #94a3b8; }

.table-container { background: rgba(15, 23, 42, .4); border: 1px solid rgba(148, 163, 184, .12); border-radius: 16px; overflow: hidden; }
.students-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.88rem; }
.students-table th { background: rgba(30, 41, 59, .6); color: #f8fafc; font-weight: 700; padding: 12px 16px; border-bottom: 1px solid rgba(148, 163, 184, .12); }
.students-table td { padding: 12px 16px; border-bottom: 1px solid rgba(148, 163, 184, .06); vertical-align: middle; color: #e2e8f0; }
.students-table tbody tr:hover { background: rgba(255, 255, 255, .01); }

.student-cell { display: flex; flex-direction: column; }
.student-name { font-weight: 700; color: #f8fafc; }

.nota-badge { font-weight: 700; font-size: 0.82rem; padding: 3px 10px; border-radius: 8px; display: inline-flex; }
.nota-badge--approved { background: rgba(16, 185, 129, 0.18); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
.nota-badge--failed { background: rgba(239, 68, 68, 0.18); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
.nota-badge--pending { background: rgba(148, 163, 184, 0.12); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.2); }

.status-chip { font-weight: 700; font-size: 0.75rem; padding: 3px 10px; border-radius: 8px; text-transform: uppercase; display: inline-flex; }
.status-chip--approved { background: rgba(16, 185, 129, 0.18); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
.status-chip--pending { background: rgba(245, 158, 11, 0.18); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }

@keyframes spin { to { transform: rotate(360deg); } }
@keyframes zoomIn { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }

@media (max-width: 900px) {
  .docente-cursos__header { flex-direction: column; align-items: stretch; gap: 20px; }
  .courses-grid { grid-template-columns: 1fr; }
  .course-card__actions { flex-direction: column; }
}
</style>
