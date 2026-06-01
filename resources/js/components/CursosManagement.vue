<template>
  <section class="cursos-management">
    <header class="cursos-management__header">
      <div>
        <p class="eyebrow">RF04</p>
        <h1>Gestión de cursos programados</h1>
        <p>Programa materias, asigna docentes, aulas y turnos para el ciclo lectivo.</p>
      </div>
      <div class="header-actions">
        <button class="btn-create" @click="openCreateModal">Programar nuevo</button>
        <a class="back-link" href="/index">Volver al panel</a>
      </div>
    </header>

    <div v-if="message" class="alert" :class="`alert--${messageType}`">
      {{ message }}
      <button class="alert__close" @click="message = ''">&times;</button>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="filters">
      <input 
        v-model="searchQuery" 
        type="text" 
        placeholder="Buscar por materia, docente o aula..." 
        class="filters__search"
      />
      <select v-model="turnoFilter" class="filters__select">
        <option value="">Todos los turnos</option>
        <option v-for="turno in turnos" :key="turno.IdTurno" :value="turno.IdTurno">
          {{ turno.Nombre }}
        </option>
      </select>
    </div>

    <!-- Tabla de Cursos Programados -->
    <div class="table-container">
      <table class="cursos-table">
        <thead>
          <tr>
            <th>Materia / Asignatura</th>
            <th>Aula / Ubicación</th>
            <th>Docente Asignado</th>
            <th>Turno / Horario</th>
            <th>Vigencia (Inicio - Fin)</th>
            <th>Cupos (Inscritos)</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="filteredCursos.length === 0">
            <td colspan="8" class="text-center py-8 text-gray">
              {{ loading ? 'Cargando programación...' : 'No se encontraron cursos programados.' }}
            </td>
          </tr>
          <tr v-for="cm in filteredCursos" :key="cm.IdCursoMateria">
            <td>
              <div class="materia-info">
                <span class="materia-info__name">{{ cm.Materia?.Nombre }}</span>
                <span class="materia-info__code">{{ cm.Materia?.CodigoMateria }}</span>
              </div>
            </td>
            <td>
              <div v-if="cm.Curso" class="classroom-info">
                <span class="classroom-info__aula">Aula {{ cm.Curso.Aula }}</span>
                <span class="subtext">Piso {{ cm.Curso.Piso }}</span>
              </div>
              <span v-else class="text-muted">—</span>
            </td>
            <td>
              <span class="teacher-name">{{ cm.Docente?.Nombre || 'Sin docente asignado' }}</span>
            </td>
            <td>
              <div v-if="cm.Turno" class="schedule-info">
                <span class="badge badge--turno">{{ cm.Turno.Nombre }}</span>
                <span class="subtext">{{ formatTime(cm.Turno.HoraInicio) }} - {{ formatTime(cm.Turno.HoraFin) }}</span>
              </div>
              <span v-else class="text-muted">—</span>
            </td>
            <td>
              <div class="date-info">
                <span>{{ formatDate(cm.FechaInicio) }}</span>
                <span class="subtext">al {{ formatDate(cm.FechaFin) }}</span>
              </div>
            </td>
            <td>
              <div class="slots-info">
                <strong :class="{ 'text-danger': cm.Inscritos >= cm.MaxInscritos }">
                  {{ cm.Inscritos }}
                </strong>
                <span class="subtext">de {{ cm.MaxInscritos }}</span>
              </div>
            </td>
            <td>
              <button 
                class="status-btn" 
                :class="cm.Estado ? 'status-btn--active' : 'status-btn--inactive'"
                @click="toggleStatus(cm)"
                :title="cm.Estado ? 'Desactivar curso' : 'Activar curso'"
              >
                {{ cm.Estado ? 'Activo' : 'Inactivo' }}
              </button>
            </td>
            <td>
              <div class="actions">
                <button class="btn-edit" @click="openEditModal(cm)">Editar</button>
                <button class="btn-delete" @click="confirmDelete(cm)">Eliminar</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal de Crear / Editar Curso Programado -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-card">
        <header class="modal-card__header">
          <h2>{{ isEditing ? 'Editar Programación de Curso' : 'Programar Nuevo Curso' }}</h2>
          <button class="btn-close-modal" @click="closeModal">&times;</button>
        </header>
        <div class="modal-card__body">
          <div v-if="hasModalError" class="alert alert--error" style="margin-top: 0; margin-bottom: 20px; display: block;">
            <div v-if="Array.isArray(modalError)">
              <ul style="margin: 0; padding-left: 20px; text-align: left;">
                <li v-for="(err, idx) in modalError" :key="idx">{{ err }}</li>
              </ul>
            </div>
            <div v-else>
              {{ modalError }}
            </div>
          </div>

          <form @submit.prevent="saveCursoMateria" class="modal-form">
            <div class="grid">
              <label>
                Materia / Asignatura
                <select v-model="form.IdMateria" required>
                  <option value="" disabled>Seleccione una materia</option>
                  <option v-for="m in materias" :key="m.IdMateria" :value="m.IdMateria">
                    [{{ m.CodigoMateria }}] {{ m.Nombre }}
                  </option>
                </select>
              </label>

              <label>
                Aula / Ubicación Física
                <select v-model="form.IdCurso" required>
                  <option value="" disabled>Seleccione un aula</option>
                  <option v-for="c in cursos" :key="c.IdCurso" :value="c.IdCurso">
                    {{ c.Nombre }} (Piso {{ c.Piso }})
                  </option>
                </select>
              </label>

              <label>
                Docente
                <select v-model="form.IdDocente" required>
                  <option value="" disabled>Seleccione un docente</option>
                  <option v-for="d in docentes" :key="d.IdUsuario" :value="d.IdUsuario">
                    {{ d.Nombre }}
                  </option>
                </select>
              </label>

              <label>
                Turno / Horario
                <select v-model="form.IdTurno" required>
                  <option value="" disabled>Seleccione un turno</option>
                  <option v-for="t in turnos" :key="t.IdTurno" :value="t.IdTurno">
                    {{ t.Nombre }} ({{ formatTime(t.HoraInicio) }} - {{ formatTime(t.HoraFin) }})
                  </option>
                </select>
              </label>

              <label>
                Fecha de Inicio
                <input v-model="form.FechaInicio" type="date" required />
              </label>

              <label>
                Fecha de Finalización
                <input v-model="form.FechaFin" type="date" required />
              </label>

              <label>
                Cupo Máximo de Alumnos
                <input v-model.number="form.MaxInscritos" type="number" min="1" required />
              </label>
            </div>

            <div class="modal-actions">
              <button type="submit" class="btn-save" :disabled="modalSubmitting">
                {{ modalSubmitting ? 'Guardando...' : 'Programar Curso' }}
              </button>
              <button type="button" class="btn-cancel" @click="closeModal">Cancelar</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal de Confirmación de Eliminación -->
    <div v-if="showDeleteModal" class="modal-overlay" @click.self="closeDeleteModal">
      <div class="modal-card modal-card--danger">
        <header class="modal-card__header">
          <h2>Confirmar Eliminación</h2>
          <button class="btn-close-modal" @click="closeDeleteModal">&times;</button>
        </header>
        <div class="modal-card__body text-center">
          <p>¿Está seguro de que desea eliminar la programación de esta materia?</p>
          <div class="course-block">
            <strong>{{ cmToDelete?.Materia?.Nombre }}</strong>
            <span class="subtext">Docente: {{ cmToDelete?.Docente?.Nombre }}</span>
            <span class="subtext">Aula: {{ cmToDelete?.Curso?.Aula }} (Piso {{ cmToDelete?.Curso?.Piso }})</span>
          </div>
          <p class="warning-text">
            <strong>Aviso de seguridad:</strong> Esta acción no podrá deshacerse. Si hay alumnos inscritos en este curso programado, la eliminación física fallará por seguridad del registro escolar.
          </p>
          <div class="modal-actions">
            <button class="btn-danger" @click="deleteCursoMateria" :disabled="modalSubmitting">
              {{ modalSubmitting ? 'Eliminando...' : 'Eliminar Programación' }}
            </button>
            <button class="btn-cancel" @click="closeDeleteModal">Cancelar</button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import axios from 'axios';

export default {
  name: 'CursosManagement',
  data() {
    return {
      cursosMaterias: [],
      cursos: [],
      materias: [],
      docentes: [],
      turnos: [],
      loading: false,
      searchQuery: '',
      turnoFilter: '',
      message: '',
      messageType: 'error',
      
      // Modal control
      showModal: false,
      isEditing: false,
      modalSubmitting: false,
      modalError: '',
      form: {
        IdCursoMateria: null,
        IdCurso: '',
        IdMateria: '',
        IdDocente: '',
        IdTurno: '',
        FechaInicio: '',
        FechaFin: '',
        MaxInscritos: 40,
      },

      // Delete Modal
      showDeleteModal: false,
      cmToDelete: null,
    };
  },
  computed: {
    filteredCursos() {
      const query = this.searchQuery.toLowerCase().trim();
      return this.cursosMaterias.filter(cm => {
        // Filtrar por Turno
        if (this.turnoFilter && Number(cm.IdTurno) !== Number(this.turnoFilter)) {
          return false;
        }
        // Filtrar por texto
        if (!query) return true;
        const materiaName = (cm.Materia?.Nombre || '').toLowerCase();
        const materiaCode = (cm.Materia?.CodigoMateria || '').toLowerCase();
        const teacherName = (cm.Docente?.Nombre || '').toLowerCase();
        const aula = (cm.Curso?.Aula || '').toLowerCase();
        return materiaName.includes(query) || materiaCode.includes(query) || teacherName.includes(query) || aula.includes(query);
      });
    },
    hasModalError() {
      if (!this.modalError) return false;
      if (Array.isArray(this.modalError)) return this.modalError.length > 0;
      return String(this.modalError).trim().length > 0;
    }
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
      await Promise.all([
        this.loadCursosMaterias(),
        this.loadFormData()
      ]);
      this.loading = false;
    },
    async loadCursosMaterias() {
      try {
        const { data } = await axios.get('/api/cursos-materias');
        this.cursosMaterias = data?.data ?? [];
      } catch (error) {
        this.setMessage('No se pudo cargar la programación de los cursos.', 'error');
      }
    },
    async loadFormData() {
      try {
        const { data } = await axios.get('/api/cursos-materias/form-data');
        this.cursos = data?.cursos ?? [];
        this.materias = data?.materias ?? [];
        this.docentes = data?.docentes ?? [];
        this.turnos = data?.turnos ?? [];
      } catch (error) {
        console.error('Error al cargar datos auxiliares:', error);
      }
    },
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' });
    },
    formatTime(timeString) {
      if (!timeString) return '';
      return timeString.substring(0, 5); // Remueve segundos
    },
    openCreateModal() {
      this.isEditing = false;
      this.modalError = null;
      this.form = {
        IdCursoMateria: null,
        IdCurso: '',
        IdMateria: '',
        IdDocente: '',
        IdTurno: '',
        FechaInicio: '',
        FechaFin: '',
        MaxInscritos: 40,
      };
      this.showModal = true;
    },
    openEditModal(cm) {
      this.isEditing = true;
      this.modalError = null;
      this.form = {
        IdCursoMateria: cm.IdCursoMateria,
        IdCurso: cm.IdCurso,
        IdMateria: cm.IdMateria,
        IdDocente: cm.IdDocente,
        IdTurno: cm.IdTurno,
        FechaInicio: cm.FechaInicio ? cm.FechaInicio.substring(0, 10) : '',
        FechaFin: cm.FechaFin ? cm.FechaFin.substring(0, 10) : '',
        MaxInscritos: cm.MaxInscritos,
      };
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
      this.modalError = null;
    },
    async saveCursoMateria() {
      this.modalError = null;
      this.message = '';

      // Validaciones más seguras y específicas en el cliente
      const errors = [];
      if (!this.form.IdMateria) errors.push('Debe seleccionar una Materia/Asignatura.');
      if (!this.form.IdCurso) errors.push('Debe seleccionar un Aula/Ubicación Física.');
      if (!this.form.IdDocente) errors.push('Debe seleccionar un Docente.');
      if (!this.form.IdTurno) errors.push('Debe seleccionar un Turno/Horario.');
      
      if (!this.form.FechaInicio) {
        errors.push('La fecha de inicio de vigencia es requerida.');
      }
      if (!this.form.FechaFin) {
        errors.push('La fecha de finalización de vigencia es requerida.');
      }

      if (this.form.FechaInicio && this.form.FechaFin) {
        const start = new Date(this.form.FechaInicio);
        const end = new Date(this.form.FechaFin);
        
        if (isNaN(start.getTime())) {
          errors.push('La fecha de inicio ingresada no es válida.');
        }
        if (isNaN(end.getTime())) {
          errors.push('La fecha de finalización ingresada no es válida.');
        }
        
        if (!isNaN(start.getTime()) && !isNaN(end.getTime()) && end < start) {
          errors.push('Conflicto de vigencia: La fecha de finalización (Fin) no puede ser anterior a la fecha de inicio.');
        }
      }

      if (this.form.MaxInscritos === null || this.form.MaxInscritos === undefined || this.form.MaxInscritos < 1) {
        errors.push('El cupo máximo de inscritos debe ser mayor o igual a 1.');
      }

      if (errors.length > 0) {
        this.modalError = errors;
        return;
      }

      this.modalSubmitting = true;
      try {
        const payload = { ...this.form };

        if (this.isEditing) {
          const { data } = await axios.put(`/api/cursos-materias/${this.form.IdCursoMateria}`, payload);
          this.setMessage(data?.message || 'Curso agendado actualizado correctamente.', 'success');
        } else {
          const { data } = await axios.post('/api/cursos-materias', payload);
          this.setMessage(data?.message || 'Curso agendado programado correctamente.', 'success');
        }
        
        this.closeModal();
        await this.loadCursosMaterias();
      } catch (error) {
        if (error?.response?.status === 422) {
          const apiErrors = error.response?.data?.errors;
          if (apiErrors && typeof apiErrors === 'object') {
            this.modalError = Object.values(apiErrors).flat();
          } else {
            this.modalError = [error.response?.data?.message || 'Datos de entrada inválidos.'];
          }
        } else {
          this.modalError = [error?.response?.data?.message || 'Ocurrió un error al guardar los cambios del curso.'];
        }
      } finally {
        this.modalSubmitting = false;
      }
    },
    async toggleStatus(cm) {
      try {
        const { data } = await axios.patch(`/api/cursos-materias/${cm.IdCursoMateria}/toggle-status`);
        cm.Estado = !cm.Estado;
        this.setMessage(data?.message || 'Estado del curso actualizado.', 'success');
      } catch (error) {
        this.setMessage(error?.response?.data?.message || 'No se pudo actualizar el estado.', 'error');
      }
    },
    confirmDelete(cm) {
      this.cmToDelete = cm;
      this.showDeleteModal = true;
    },
    closeDeleteModal() {
      this.showDeleteModal = false;
      this.cmToDelete = null;
    },
    async deleteCursoMateria() {
      this.modalSubmitting = true;
      this.message = '';

      try {
        const { data } = await axios.delete(`/api/cursos-materias/${this.cmToDelete.IdCursoMateria}`);
        // Determinar el tipo de alerta según la acción tomada (desactivado vs eliminado)
        const msgType = data?.action === 'deactivated' ? 'warning' : 'success';
        this.setMessage(data?.message || 'Curso programado procesado correctamente.', msgType);
        this.closeDeleteModal();
        await this.loadCursosMaterias();
      } catch (error) {
        const responseMsg = error?.response?.data?.message;
        this.setMessage(responseMsg || 'No se pudo procesar la eliminación del curso programado.', 'error');
        this.closeDeleteModal();
      } finally {
        this.modalSubmitting = false;
      }
    },
    setMessage(message, type = 'error') {
      this.message = message;
      this.messageType = type;
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  }
};
</script>

<style scoped>
.cursos-management { min-height: 100vh; padding: 32px; background: linear-gradient(180deg, #07111f 0%, #101b2b 100%); color: #eef2ff; }
.cursos-management__header { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 32px; }
.eyebrow { margin: 0 0 8px; color: #fbbf24; text-transform: uppercase; letter-spacing: .18em; font-size: .75rem; }
h1 { margin: 0; font-size: 2rem; }
p { margin: 8px 0 0; color: #cbd5e1; }
.header-actions { display: flex; gap: 12px; }
.back-link, .btn-create, .btn-save, .btn-cancel, .btn-danger { border-radius: 999px; padding: 12px 24px; font-weight: 700; text-decoration: none; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; }
.back-link { background: transparent; border: 1px solid rgba(148, 163, 184, .22); color: #cbd5e1; }
.btn-create { background: #fbbf24; color: #0f172a; }

.alert { position: relative; margin: 20px 0; padding: 16px 40px 16px 18px; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; }
.alert--success { background: rgba(16, 185, 129, .16); color: #d1fae5; border: 1px solid rgba(16, 185, 129, .3); }
.alert--error { background: rgba(239, 68, 68, .16); color: #fecaca; border: 1px solid rgba(239, 68, 68, .3); }
.alert--warning { background: rgba(245, 158, 11, .16); color: #fef3c7; border: 1px solid rgba(245, 158, 11, .3); }
.alert--info { background: rgba(59, 130, 246, .16); color: #dbeafe; border: 1px solid rgba(59, 130, 246, .3); }
.alert__close { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: transparent; border: none; font-size: 1.5rem; color: inherit; cursor: pointer; }

/* Filtros */
.filters { display: flex; gap: 16px; margin-bottom: 24px; }
.filters__search { flex: 1; border-radius: 14px; border: 1px solid rgba(148, 163, 184, .22); background: rgba(30, 41, 59, .82); color: #f8fafc; padding: 12px 16px; font-size: 1rem; outline: none; }
.filters__select { width: 220px; border-radius: 14px; border: 1px solid rgba(148, 163, 184, .22); background: rgba(30, 41, 59, .82); color: #f8fafc; padding: 12px 16px; font-size: 1rem; outline: none; }
.filters__search:focus, .filters__select:focus { border-color: #fbbf24; box-shadow: 0 0 0 3px rgba(251, 191, 36, .18); }

/* Tabla */
.table-container { background: rgba(15, 23, 42, .86); border: 1px solid rgba(148, 163, 184, .18); border-radius: 24px; overflow: hidden; box-shadow: 0 20px 60px rgba(0, 0, 0, .25); margin-bottom: 24px; }
.cursos-table { width: 100%; border-collapse: collapse; text-align: left; }
.cursos-table th { background: rgba(30, 41, 59, .5); color: #e2e8f0; font-weight: 700; padding: 16px 20px; border-bottom: 1px solid rgba(148, 163, 184, .12); }
.cursos-table td { padding: 16px 20px; border-bottom: 1px solid rgba(148, 163, 184, .08); vertical-align: middle; }
.cursos-table tbody tr:hover { background: rgba(255, 255, 255, .02); }

.materia-info { display: flex; flex-direction: column; }
.materia-info__name { font-weight: 700; color: #f8fafc; font-size: 1.05rem; }
.materia-info__code { color: #fbbf24; font-weight: 600; font-family: monospace; font-size: 0.88rem; margin-top: 2px; }

.classroom-info { display: flex; flex-direction: column; }
.classroom-info__aula { font-weight: 700; color: #e2e8f0; }
.subtext { color: #94a3b8; font-size: 0.82rem; }

.teacher-name { color: #f8fafc; font-weight: 600; }

.schedule-info { display: flex; flex-direction: column; gap: 4px; }
.badge { display: inline-flex; align-items: center; justify-content: center; padding: 4px 10px; border-radius: 999px; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
.badge--turno { background: rgba(139, 92, 246, .16); color: #c4b5fd; border: 1px solid rgba(139, 92, 246, .3); align-self: flex-start; }

.date-info { display: flex; flex-direction: column; color: #e2e8f0; }
.slots-info { display: flex; flex-direction: column; }
.slots-info strong { font-size: 1.15rem; color: #34d399; }
.text-danger { color: #ef4444 !important; }

/* Status button */
.status-btn { padding: 6px 14px; border-radius: 999px; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s ease; font-size: 0.82rem; }
.status-btn--active { background: rgba(16, 185, 129, .16); color: #34d399; border: 1px solid rgba(16, 185, 129, .3); }
.status-btn--active:hover { background: rgba(16, 185, 129, .28); }
.status-btn--inactive { background: rgba(239, 68, 68, .16); color: #f87171; border: 1px solid rgba(239, 68, 68, .3); }
.status-btn--inactive:hover { background: rgba(239, 68, 68, .28); }

.actions { display: flex; gap: 8px; }
.actions button { border-radius: 8px; padding: 6px 12px; font-weight: 700; border: none; cursor: pointer; font-size: 0.82rem; }
.btn-edit { background: rgba(251, 191, 36, .16); color: #facc15; border: 1px solid rgba(251, 191, 36, .3); }
.btn-edit:hover { background: rgba(251, 191, 36, .26); }
.btn-delete { background: rgba(239, 68, 68, .16); color: #f87171; border: 1px solid rgba(239, 68, 68, .3); }
.btn-delete:hover { background: rgba(239, 68, 68, .26); }

/* Modal Styles */
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(7, 11, 25, 0.85); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px; backdrop-filter: blur(8px); }
.modal-card { background: #0f172a; border: 1px solid rgba(148, 163, 184, .25); border-radius: 24px; width: 100%; max-width: 750px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); animation: zoomIn 0.25s ease-out; }
.modal-card--danger { max-width: 500px; border-color: rgba(239, 68, 68, 0.4); }
.modal-card__header { padding: 20px 24px; border-bottom: 1px solid rgba(148, 163, 184, .14); display: flex; justify-content: space-between; align-items: center; background: rgba(30, 41, 59, .4); }
.modal-card__header h2 { margin: 0; font-size: 1.3rem; color: #f8fafc; }
.btn-close-modal { background: transparent; border: none; color: #94a3b8; font-size: 2rem; cursor: pointer; line-height: 1; }
.btn-close-modal:hover { color: #f8fafc; }
.modal-card__body { padding: 24px; overflow-y: auto; max-height: 80vh; }

.modal-form .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
.modal-form label { display: flex; flex-direction: column; gap: 6px; font-weight: 600; color: #e2e8f0; font-size: 0.9rem; }
.modal-form input, .modal-form select { border-radius: 12px; border: 1px solid rgba(148, 163, 184, .22); background: rgba(30, 41, 59, .82); color: #f8fafc; padding: 12px 14px; font-size: 0.95rem; outline: none; }
.modal-form input:focus, .modal-form select:focus { border-color: #fbbf24; box-shadow: 0 0 0 3px rgba(251, 191, 36, .18); }

.modal-actions { display: flex; gap: 12px; margin-top: 24px; justify-content: flex-end; }
.btn-save { background: #fbbf24; color: #0f172a; }
.btn-cancel { background: transparent; border: 1px solid rgba(148, 163, 184, .22); color: #e2e8f0; }
.btn-danger { background: #ef4444; color: #ffffff; }
.btn-save:disabled, .btn-danger:disabled { opacity: 0.7; cursor: not-allowed; }

.course-block { background: rgba(30, 41, 59, .6); border-radius: 16px; padding: 16px; margin: 16px 0; display: flex; flex-direction: column; align-items: center; border: 1px solid rgba(148, 163, 184, .12); gap: 4px; }
.warning-text { color: #fca5a5; font-size: 0.88rem; line-height: 1.5; margin-top: 16px; }

@keyframes zoomIn {
  from { transform: scale(0.95); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

@media (max-width: 900px) {
  .cursos-management__header { flex-direction: column; align-items: stretch; gap: 20px; }
  .filters { flex-direction: column; }
  .filters__select { width: 100%; }
  .modal-form .grid { grid-template-columns: 1fr; }
  .cursos-table th:nth-child(2), .cursos-table td:nth-child(2),
  .cursos-table th:nth-child(4), .cursos-table td:nth-child(4),
  .cursos-table th:nth-child(5), .cursos-table td:nth-child(5) { display: none; }
}
</style>
