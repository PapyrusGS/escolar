<template>
  <section class="docente-notas">
    <header class="docente-notas__header">
      <div>
        <p class="eyebrow">RF07 • Registro de Calificaciones</p>
        <h1>Gestión de Notas</h1>
        <p>Asigna, edita y visualiza el rendimiento de tus alumnos por curso.</p>
      </div>
      <div class="header-actions">
        <a class="btn-action btn-action--secondary" href="/docente/cursos">← Volver a Mis Cursos</a>
        <a class="btn-action btn-action--secondary" href="/dashboard">Panel</a>
      </div>
    </header>

    <div v-if="message" class="alert" :class="`alert--${messageType}`">
      {{ message }}
      <button class="alert__close" @click="message = ''">&times;</button>
    </div>

    <!-- Selector de curso -->
    <div class="course-selector">
      <label>Selecciona un curso para gestionar las notas:</label>
      <select v-model="selectedCursoId" @change="onCursoChange" class="selector-input">
        <option value="" disabled>-- Seleccionar curso --</option>
        <option v-for="c in cursos" :key="c.IdCursoMateria" :value="c.IdCursoMateria">
          [{{ c.CodigoMateria }}] {{ c.MateriaNombre }} — Aula {{ c.Aula }} ({{ c.Inscritos }} inscritos)
        </option>
      </select>
    </div>

    <!-- Estado de carga cursos -->
    <div v-if="loadingCursos" class="loading-state">
      <div class="spinner"></div>
      <p>Cargando tus cursos...</p>
    </div>

    <!-- Sin cursos asignados -->
    <div v-else-if="cursos.length === 0 && !loadingCursos" class="empty-state">
      <div class="empty-state__icon">📚</div>
      <h2>No tienes cursos asignados</h2>
      <p>Contacta al administrador para que te asigne materias.</p>
    </div>

    <!-- Contenido de notas (solo si hay curso seleccionado) -->
    <template v-if="selectedCursoId">

      <!-- Estado de carga notas -->
      <div v-if="loadingNotas" class="loading-state">
        <div class="spinner"></div>
        <p>Cargando notas del curso...</p>
      </div>

      <template v-else>

        <!-- Panel de rendimiento -->
        <div v-if="rendimiento" class="rendimiento-panel">
          <h3>📊 Rendimiento del Curso</h3>
          <div class="rendimiento-grid">
            <div class="rend-card">
              <span class="rend-card__label">Promedio</span>
              <strong class="rend-card__value">{{ rendimiento.promedio }}</strong>
            </div>
            <div class="rend-card rend-card--green">
              <span class="rend-card__label">Aprobados</span>
              <strong class="rend-card__value">{{ rendimiento.aprobados }}</strong>
            </div>
            <div class="rend-card rend-card--red">
              <span class="rend-card__label">Reprobados</span>
              <strong class="rend-card__value">{{ rendimiento.reprobados }}</strong>
            </div>
            <div class="rend-card">
              <span class="rend-card__label">% Aprobación</span>
              <strong class="rend-card__value">{{ rendimiento.porcentaje_aprobacion }}%</strong>
            </div>
            <div class="rend-card">
              <span class="rend-card__label">Nota Máx</span>
              <strong class="rend-card__value">{{ rendimiento.nota_maxima ?? '—' }}</strong>
            </div>
            <div class="rend-card">
              <span class="rend-card__label">Nota Mín</span>
              <strong class="rend-card__value">{{ rendimiento.nota_minima ?? '—' }}</strong>
            </div>
          </div>
        </div>

        <!-- Tabla de alumnos con notas -->
        <div v-if="alumnos.length === 0" class="empty-state" style="margin-top: 24px;">
          <p>No hay alumnos inscritos en este curso aún.</p>
        </div>

        <div v-else class="notas-table-container">
          <div class="table-header">
            <h3>Alumnos ({{ alumnos.length }})</h3>
            <span class="table-hint">Las notas se guardan individualmente. Regla: nota ≥ 51 = Aprobado.</span>
          </div>
          <table class="notas-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Estudiante</th>
                <th>CI</th>
                <th>Nota Actual</th>
                <th>Acciones</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(a, idx) in alumnos" :key="a.IdInscripcion" :class="{ 'row--has-nota': a.EstadoNota }">
                <td>{{ idx + 1 }}</td>
                <td>
                  <div class="student-cell">
                    <span class="student-name">{{ a.Estudiante }}</span>
                  </div>
                </td>
                <td>{{ a.CI }}</td>
                <td>
                  <span v-if="a.EstadoNota" class="nota-display" :class="a.Aprobado ? 'nota-display--approved' : 'nota-display--failed'">
                    {{ a.Nota }}
                  </span>
                  <span v-else class="nota-display nota-display--pending">—</span>
                </td>
                <td>
                  <div class="nota-actions">
                    <input
                      v-if="editingId === a.IdInscripcion"
                      v-model.number="editingValue"
                      type="number"
                      min="0"
                      max="100"
                      step="0.01"
                      class="nota-input"
                      @keyup.enter="guardarNota(a)"
                      @keyup.escape="cancelEdit"
                      ref="notaInput"
                    />
                    <template v-if="editingId === a.IdInscripcion">
                      <button class="btn-sm btn-sm--save" @click="guardarNota(a)" :disabled="savingNota">
                        {{ savingNota ? '...' : '✓' }}
                      </button>
                      <button class="btn-sm btn-sm--cancel" @click="cancelEdit">✕</button>
                    </template>
                    <template v-else>
                      <button class="btn-sm btn-sm--edit" @click="startEdit(a)">
                        {{ a.EstadoNota ? '✏️ Editar' : '+ Asignar' }}
                      </button>
                    </template>
                  </div>
                </td>
                <td>
                  <span class="status-chip" :class="a.Aprobado ? 'status-chip--approved' : 'status-chip--pending'">
                    {{ a.EstadoNota ? (a.Aprobado ? 'Aprobado' : 'Reprobado') : 'Sin nota' }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </template>
    </template>
  </section>
</template>

<script>
import axios from 'axios';

export default {
  name: 'DocenteNotas',
  data() {
    return {
      cursos: [],
      selectedCursoId: '',
      loadingCursos: false,
      loadingNotas: false,
      alumnos: [],
      rendimiento: null,

      // Edición inline
      editingId: null,
      editingValue: null,
      savingNota: false,

      message: '',
      messageType: 'error',
    };
  },
  mounted() {
    this.init();
  },
  methods: {
    async init() {
      this.loadingCursos = true;
      const token = localStorage.getItem('auth_token');
      if (token) {
        axios.defaults.headers.common.Authorization = `Bearer ${token}`;
      } else {
        window.location.href = '/';
        return;
      }

      // Si hay query ?curso=, seleccionarlo automáticamente
      const params = new URLSearchParams(window.location.search);
      const cursoParam = params.get('curso');

      await this.loadCursos();

      if (cursoParam && this.cursos.some(c => String(c.IdCursoMateria) === String(cursoParam))) {
        this.selectedCursoId = parseInt(cursoParam);
        await this.onCursoChange();
      }

      this.loadingCursos = false;
    },
    async loadCursos() {
      try {
        const { data } = await axios.get('/api/docente/notas/cursos');
        this.cursos = data?.data ?? [];
      } catch (error) {
        this.setMessage('No se pudieron cargar los cursos.', 'error');
      }
    },
    async onCursoChange() {
      if (!this.selectedCursoId) return;
      this.loadingNotas = true;
      this.alumnos = [];
      this.rendimiento = null;
      this.cancelEdit();

      try {
        await Promise.all([
          this.loadNotas(),
          this.loadRendimiento(),
        ]);
      } finally {
        this.loadingNotas = false;
      }
    },
    async loadNotas() {
      try {
        const { data } = await axios.get(`/api/docente/cursos/${this.selectedCursoId}/notas`);
        this.alumnos = data?.data ?? [];
      } catch (error) {
        this.setMessage('No se pudieron cargar las notas.', 'error');
      }
    },
    async loadRendimiento() {
      try {
        const { data } = await axios.get(`/api/docente/cursos/${this.selectedCursoId}/rendimiento`);
        this.rendimiento = data?.data ?? null;
      } catch (error) {
        console.error('Error al cargar rendimiento:', error);
      }
    },
    startEdit(alumno) {
      this.editingId = alumno.IdInscripcion;
      this.editingValue = alumno.Nota ?? '';
      this.$nextTick(() => {
        const inputs = this.$refs.notaInput;
        if (inputs && inputs.length > 0) {
          inputs[0].focus();
        }
      });
    },
    cancelEdit() {
      this.editingId = null;
      this.editingValue = null;
    },
    async guardarNota(alumno) {
      if (this.editingValue === null || this.editingValue === '' || isNaN(this.editingValue)) {
        this.setMessage('Debe ingresar un valor numérico válido.', 'error');
        return;
      }

      const nota = parseFloat(this.editingValue);
      if (nota < 0 || nota > 100) {
        this.setMessage('La nota debe estar entre 0 y 100.', 'error');
        return;
      }

      this.savingNota = true;
      try {
        if (alumno.EstadoNota) {
          // Editar nota existente
          await axios.put(`/api/docente/notas/${alumno.IdNota}`, { Nota: nota });
        } else {
          // Asignar nueva nota
          await axios.post('/api/docente/notas', {
            IdInscripcion: alumno.IdInscripcion,
            Nota: nota,
          });
        }

        this.setMessage(alumno.EstadoNota ? 'Nota actualizada correctamente.' : 'Nota asignada correctamente.', 'success');
        this.cancelEdit();
        await Promise.all([this.loadNotas(), this.loadRendimiento()]);
      } catch (error) {
        const msg = error?.response?.data?.message || 'Error al guardar la nota.';
        this.setMessage(msg, 'error');
      } finally {
        this.savingNota = false;
      }
    },
    formatDate(dateString) {
      if (!dateString) return '';
      return new Date(dateString).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' });
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
.docente-notas { min-height: 100vh; padding: 32px; background: linear-gradient(180deg, #07111f 0%, #101b2b 100%); color: #eef2ff; }
.docente-notas__header { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 32px; }
.eyebrow { margin: 0 0 8px; color: #38bdf8; text-transform: uppercase; letter-spacing: .18em; font-size: .75rem; }
h1 { margin: 0; font-size: 2rem; }
p { margin: 8px 0 0; color: #cbd5e1; }
.header-actions { display: flex; gap: 12px; }

.btn-action { border-radius: 999px; padding: 12px 24px; font-weight: 700; text-decoration: none; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; font-size: 0.9rem; }
.btn-action--primary { background: #38bdf8; color: #0f172a; }
.btn-action--primary:hover { background: #0ea5e9; }
.btn-action--secondary { background: transparent; border: 1px solid rgba(148, 163, 184, .22); color: #cbd5e1; }

.alert { position: relative; margin: 20px 0; padding: 16px 40px 16px 18px; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; }
.alert--success { background: rgba(16, 185, 129, .16); color: #d1fae5; border: 1px solid rgba(16, 185, 129, .3); }
.alert--error { background: rgba(239, 68, 68, .16); color: #fecaca; border: 1px solid rgba(239, 68, 68, .3); }
.alert__close { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: transparent; border: none; font-size: 1.5rem; color: inherit; cursor: pointer; }

.course-selector { background: rgba(15, 23, 42, 0.85); border: 1px solid rgba(148, 163, 184, 0.18); border-radius: 20px; padding: 20px 24px; margin-bottom: 24px; }
.course-selector label { display: block; font-weight: 700; color: #e2e8f0; margin-bottom: 10px; font-size: 0.92rem; }
.selector-input { width: 100%; border-radius: 12px; border: 1px solid rgba(148, 163, 184, .22); background: rgba(30, 41, 59, .82); color: #f8fafc; padding: 12px 16px; font-size: 0.95rem; outline: none; }
.selector-input:focus { border-color: #38bdf8; box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.18); }

.loading-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 80px 40px; }
.spinner { width: 40px; height: 40px; border: 4px solid rgba(56, 189, 248, 0.1); border-top-color: #38bdf8; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 16px; }

.empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 60px 40px; background: rgba(15, 23, 42, 0.4); border: 1px dashed rgba(148, 163, 184, 0.2); border-radius: 24px; }
.empty-state__icon { font-size: 3rem; margin-bottom: 16px; }
.empty-state h2 { margin: 0 0 10px; color: #f8fafc; font-size: 1.4rem; }
.empty-state p { margin: 0; color: #cbd5e1; max-width: 460px; line-height: 1.6; }

/* Rendimiento */
.rendimiento-panel { background: rgba(15, 23, 42, 0.85); border: 1px solid rgba(148, 163, 184, 0.18); border-radius: 20px; padding: 24px; margin-bottom: 24px; }
.rendimiento-panel h3 { margin: 0 0 16px; color: #38bdf8; font-size: 1.1rem; }
.rendimiento-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 14px; }
.rend-card { background: rgba(30, 41, 59, 0.5); border: 1px solid rgba(148, 163, 184, 0.1); border-radius: 14px; padding: 16px; text-align: center; display: flex; flex-direction: column; gap: 4px; }
.rend-card__label { font-size: 0.75rem; text-transform: uppercase; color: #94a3b8; font-weight: 600; letter-spacing: 0.04em; }
.rend-card__value { font-size: 1.5rem; color: #f8fafc; font-weight: 800; }
.rend-card--green .rend-card__value { color: #34d399; }
.rend-card--red .rend-card__value { color: #f87171; }

/* Tabla de Notas */
.notas-table-container { background: rgba(15, 23, 42, .86); border: 1px solid rgba(148, 163, 184, .18); border-radius: 24px; overflow: hidden; box-shadow: 0 20px 60px rgba(0, 0, 0, .25); }
.table-header { padding: 20px 24px; border-bottom: 1px solid rgba(148, 163, 184, .12); display: flex; justify-content: space-between; align-items: center; }
.table-header h3 { margin: 0; color: #fbbf24; font-size: 1.1rem; }
.table-hint { font-size: 0.78rem; color: #94a3b8; }

.notas-table { width: 100%; border-collapse: collapse; text-align: left; }
.notas-table th { background: rgba(30, 41, 59, .5); color: #e2e8f0; font-weight: 700; padding: 14px 20px; border-bottom: 1px solid rgba(148, 163, 184, .12); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.03em; }
.notas-table td { padding: 14px 20px; border-bottom: 1px solid rgba(148, 163, 184, .06); vertical-align: middle; }
.notas-table tbody tr:hover { background: rgba(255, 255, 255, .02); }
.notas-table tbody tr.row--has-nota { background: rgba(16, 185, 129, 0.03); }

.student-cell { display: flex; flex-direction: column; }
.student-name { font-weight: 700; color: #f8fafc; font-size: 0.95rem; }

.nota-display { font-weight: 700; font-size: 1rem; padding: 4px 12px; border-radius: 8px; display: inline-flex; min-width: 50px; justify-content: center; }
.nota-display--approved { background: rgba(16, 185, 129, 0.18); color: #34d399; }
.nota-display--failed { background: rgba(239, 68, 68, 0.18); color: #f87171; }
.nota-display--pending { background: rgba(148, 163, 184, 0.08); color: #64748b; }

.nota-actions { display: flex; gap: 6px; align-items: center; }
.nota-input { width: 80px; border-radius: 8px; border: 1px solid #38bdf8; background: rgba(30, 41, 59, .9); color: #f8fafc; padding: 6px 10px; font-size: 0.95rem; text-align: center; outline: none; }
.nota-input:focus { box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2); }

.btn-sm { border-radius: 8px; padding: 6px 10px; font-weight: 700; border: none; cursor: pointer; font-size: 0.82rem; transition: all 0.2s; }
.btn-sm--edit { background: rgba(56, 189, 248, 0.12); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3); }
.btn-sm--edit:hover { background: #38bdf8; color: #0f172a; }
.btn-sm--save { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
.btn-sm--save:hover { background: #34d399; color: #0f172a; }
.btn-sm--cancel { background: rgba(239, 68, 68, 0.12); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
.btn-sm--cancel:hover { background: #ef4444; color: #ffffff; }
.btn-sm:disabled { opacity: 0.5; cursor: not-allowed; }

.status-chip { font-weight: 700; font-size: 0.75rem; padding: 4px 10px; border-radius: 8px; text-transform: uppercase; display: inline-flex; }
.status-chip--approved { background: rgba(16, 185, 129, 0.18); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
.status-chip--pending { background: rgba(148, 163, 184, 0.1); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.15); }

@keyframes spin { to { transform: rotate(360deg); } }

@media (max-width: 900px) {
  .docente-notas__header { flex-direction: column; align-items: stretch; gap: 20px; }
  .rendimiento-grid { grid-template-columns: repeat(3, 1fr); }
  .notas-table th:nth-child(3), .notas-table td:nth-child(3) { display: none; }
}
</style>
