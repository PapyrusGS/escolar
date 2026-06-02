<template>
  <section class="cursos-visualizacion">
    <header class="cursos-visualizacion__header">
      <div>
        <p class="eyebrow">RF06</p>
        <h1>Visualización de cursos por usuario</h1>
        <p>Consulta el historial y programación de clases de estudiantes y docentes en el ciclo lectivo.</p>
      </div>
      <div class="header-actions">
        <a class="back-link" href="/index">Volver al panel</a>
      </div>
    </header>

    <div v-if="globalMessage" class="alert" :class="`alert--${globalMessageType}`">
      {{ globalMessage }}
      <button class="alert__close" @click="globalMessage = ''">&times;</button>
    </div>

    <!-- Layout Dividido (Split Pane) -->
    <div class="layout-container">
      
      <!-- Panel Izquierdo: Lista de Usuarios -->
      <aside class="sidebar">
        <div class="sidebar__search-box">
          <input 
            v-model="userSearchQuery" 
            type="text" 
            placeholder="Buscar usuario por nombre o CI..." 
            class="search-input"
          />
          <div class="role-tabs">
            <button 
              :class="{ 'role-tabs__btn--active': activeRoleFilter === '' }"
              @click="activeRoleFilter = ''"
            >
              Todos
            </button>
            <button 
              :class="{ 'role-tabs__btn--active': activeRoleFilter === '2' }"
              @click="activeRoleFilter = '2'"
            >
              Docentes
            </button>
            <button 
              :class="{ 'role-tabs__btn--active': activeRoleFilter === '3' }"
              @click="activeRoleFilter = '3'"
            >
              Estudiantes
            </button>
          </div>
        </div>

        <ul class="user-list">
          <li v-if="loadingUsers" class="user-list__loading">
            Cargando usuarios...
          </li>
          <li v-else-if="filteredUsers.length === 0" class="user-list__empty">
            No se encontraron usuarios.
          </li>
          <li 
            v-for="u in filteredUsers" 
            :key="u.IdUsuario" 
            class="user-item"
            :class="{ 'user-item--selected': selectedUser?.IdUsuario === u.IdUsuario }"
            @click="selectUser(u)"
          >
            <div class="user-item__info">
              <span class="user-name">{{ u.Nombre1 }} {{ u.Apellido1 }}</span>
              <span class="user-sub text-muted">CI: {{ u.CI }}</span>
            </div>
            <span class="badge" :class="u.IdRol === 2 ? 'badge--teacher' : 'badge--student'">
              {{ u.IdRol === 2 ? 'Docente' : 'Estudiante' }}
            </span>
          </li>
        </ul>
      </aside>

      <!-- Panel Derecho: Detalles e Información Académica -->
      <main class="main-content">
        
        <!-- Estado Inicial: Ningún Usuario Seleccionado -->
        <div v-if="!selectedUser" class="empty-state">
          <div class="empty-state__icon">🔍</div>
          <h2>Selecciona un usuario de la lista</h2>
          <p>Elige un estudiante o docente en el panel lateral para examinar sus cursos agendados y su información relevante.</p>
        </div>

        <!-- Estado de Carga -->
        <div v-else-if="loadingDetails" class="loading-state">
          <div class="spinner"></div>
          <p>Obteniendo información académica y cursos asignados...</p>
        </div>

        <!-- Perfil y Cursos Asignados -->
        <div v-else class="details-view">
          
          <!-- Tarjeta de Perfil -->
          <div class="profile-card">
            <div class="profile-card__header">
              <div class="avatar-circle">
                {{ selectedUser.Nombre1[0] }}{{ selectedUser.Apellido1[0] }}
              </div>
              <div>
                <h2>{{ selectedUser.Nombre1 }} {{ selectedUser.Nombre2 || '' }} {{ selectedUser.Apellido1 }} {{ selectedUser.Apellido2 || '' }}</h2>
                <span class="badge" :class="selectedUser.IdRol === 2 ? 'badge--teacher' : 'badge--student'">
                  {{ selectedUser.IdRol === 2 ? 'Docente Asignado' : 'Estudiante Regular' }}
                </span>
              </div>
            </div>
            
            <div class="profile-grid">
              <div class="profile-info-item">
                <span class="label">Cédula de Identidad (CI)</span>
                <span class="value">{{ selectedUser.CI }}</span>
              </div>
              <div class="profile-info-item">
                <span class="label">Correo Electrónico</span>
                <span class="value">{{ selectedUser.Correo }}</span>
              </div>
              <div class="profile-info-item" v-if="selectedUser.Telefono">
                <span class="label">Teléfono / Celular</span>
                <span class="value">{{ selectedUser.Telefono }}</span>
              </div>
              <div class="profile-info-item" v-if="selectedUser.IdRol === 3">
                <span class="label">Carrera Universitaria</span>
                <span class="value text-warning">{{ selectedUser.Carrera || 'No registrada' }}</span>
              </div>
              <div class="profile-info-item" v-if="selectedUser.IdRol === 3">
                <span class="label">Modalidad de Estudios</span>
                <span class="value text-info">{{ selectedUser.Modalidad || 'No registrada' }}</span>
              </div>
            </div>
          </div>

          <!-- Cabecera de Cursos -->
          <div class="courses-header">
            <h3>Asignaturas y Cursos Programados ({{ academicData.cursos.length }})</h3>
          </div>

          <!-- Sin Cursos Registrados -->
          <div v-if="academicData.cursos.length === 0" class="no-courses-state">
            <p>Este usuario no tiene materias registradas en su currícula para el ciclo actual.</p>
          </div>

          <!-- Cuadrícula de Cursos -->
          <div v-else class="courses-grid">
            <div 
              v-for="c in academicData.cursos" 
              :key="c.IdCursoMateria" 
              class="course-card"
              :class="{ 'course-card--inactive': !c.Estado }"
            >
              <div class="course-card__header">
                <div>
                  <span class="course-code">{{ c.Materia?.CodigoMateria }}</span>
                  <h4>{{ c.Materia?.Nombre }}</h4>
                </div>
                <span class="status-indicator" :class="c.Estado ? 'status-indicator--active' : 'status-indicator--inactive'">
                  {{ c.Estado ? 'Activo' : 'Desactivado' }}
                </span>
              </div>

              <div class="course-card__body">
                <!-- Información Común -->
                <div class="info-row">
                  <span class="icon">📍</span>
                  <span>
                    <strong>Aula:</strong> 
                    {{ c.Curso?.Nombre || `Aula ${c.Curso?.Aula}` }} (Piso {{ c.Curso?.Piso }})
                  </span>
                </div>
                <div class="info-row">
                  <span class="icon">🕒</span>
                  <span>
                    <strong>Turno:</strong> 
                    <span class="turno-badge">{{ c.Turno?.Nombre }}</span> 
                    <span class="subtext">({{ formatTime(c.Turno?.HoraInicio) }} - {{ formatTime(c.Turno?.HoraFin) }})</span>
                  </span>
                </div>
                <div class="info-row">
                  <span class="icon">📅</span>
                  <span>
                    <strong>Vigencia:</strong> 
                    {{ formatDate(c.FechaInicio) }} al {{ formatDate(c.FechaFin) }}
                  </span>
                </div>

                <!-- Detalles Exclusivos de Estudiante -->
                <template v-if="selectedUser.IdRol === 3">
                  <div class="divider"></div>
                  <div class="info-row">
                    <span class="icon">👤</span>
                    <span>
                      <strong>Docente:</strong> {{ c.Docente?.Nombre }}
                    </span>
                  </div>
                  <div class="info-row">
                    <span class="icon">📝</span>
                    <span>
                      <strong>Fecha Inscripción:</strong> {{ formatDate(c.Inscripcion?.Fecha) }}
                    </span>
                  </div>
                  <div class="info-row">
                    <span class="icon">🎓</span>
                    <span>
                      <strong>Estado Académico:</strong> 
                      <span 
                        class="academic-status"
                        :class="c.Inscripcion?.Aprobado ? 'academic-status--approved' : 'academic-status--in-progress'"
                      >
                        {{ c.Inscripcion?.Aprobado ? 'Aprobada' : 'En curso' }}
                      </span>
                    </span>
                  </div>
                </template>

                <!-- Detalles Exclusivos de Docente -->
                <template v-if="selectedUser.IdRol === 2">
                  <div class="divider"></div>
                  <div class="slots-container">
                    <span>
                      <strong>Cupos:</strong> {{ c.Inscritos }} / {{ c.MaxInscritos }} alumnos
                    </span>
                    <div class="progress-bar">
                      <div 
                        class="progress-bar__fill" 
                        :style="{ width: `${Math.min((c.Inscritos / c.MaxInscritos) * 100, 100)}%` }"
                        :class="{ 'progress-bar__fill--full': c.Inscritos >= c.MaxInscritos }"
                      ></div>
                    </div>
                  </div>
                </template>
              </div>

              <!-- Botones de Acción de Tarjeta -->
              <div class="course-card__actions">
                <button 
                  v-if="selectedUser.IdRol === 2" 
                  class="btn-action-primary" 
                  @click="openEnrolledModal(c)"
                >
                  Ver alumnos inscritos
                </button>
                <button 
                  v-else 
                  class="btn-action-secondary" 
                  @click="openCourseDetailsModal(c)"
                >
                  Ver detalles completos
                </button>
              </div>
            </div>
          </div>

        </div>
      </main>

    </div>

    <!-- Modal 1: Alumnos Inscritos (Docente) -->
    <div v-if="showEnrolledModal" class="modal-overlay" @click.self="closeEnrolledModal">
      <div class="modal-card">
        <header class="modal-card__header">
          <div>
            <h2>Alumnos Inscritos</h2>
            <p class="subtext">{{ selectedCourse?.Materia?.Nombre }} ({{ selectedCourse?.Materia?.CodigoMateria }})</p>
          </div>
          <button class="btn-close-modal" @click="closeEnrolledModal">&times;</button>
        </header>
        <div class="modal-card__body">
          <div v-if="!selectedCourse?.Alumnos || selectedCourse.Alumnos.length === 0" class="empty-modal-state">
            <p>No hay estudiantes registrados en este curso actualmente.</p>
          </div>
          <div v-else class="table-container">
            <table class="students-table">
              <thead>
                <tr>
                  <th>Estudiante</th>
                  <th>CI</th>
                  <th>Carrera</th>
                  <th>Modalidad</th>
                  <th>Inscripto el</th>
                  <th>Calificación</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="s in selectedCourse.Alumnos" :key="s.IdUsuario">
                  <td>
                    <div class="student-cell">
                      <span class="student-name">{{ s.Nombre }}</span>
                      <span class="student-email">{{ s.Correo }}</span>
                    </div>
                  </td>
                  <td>{{ s.CI }}</td>
                  <td><span class="text-warning font-semibold">{{ s.Carrera }}</span></td>
                  <td><span class="text-info">{{ s.Modalidad }}</span></td>
                  <td>{{ formatDate(s.FechaInscripcion) }}</td>
                  <td>
                    <span 
                      class="academic-status" 
                      :class="s.Aprobado ? 'academic-status--approved' : 'academic-status--in-progress'"
                    >
                      {{ s.Aprobado ? 'Aprobado' : 'Cursando' }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal 2: Detalles Completos del Curso (Estudiante) -->
    <div v-if="showDetailsModal" class="modal-overlay" @click.self="closeDetailsModal">
      <div class="modal-card modal-card--narrow">
        <header class="modal-card__header">
          <div>
            <h2>Detalles del Curso</h2>
            <p class="subtext">{{ selectedCourse?.Materia?.Nombre }}</p>
          </div>
          <button class="btn-close-modal" @click="closeDetailsModal">&times;</button>
        </header>
        <div class="modal-card__body">
          <div class="detail-block">
            <div class="detail-item">
              <span class="label">Asignatura</span>
              <span class="value">{{ selectedCourse?.Materia?.Nombre }} ({{ selectedCourse?.Materia?.CodigoMateria }})</span>
            </div>
            <div class="detail-item">
              <span class="label">Cátedra a cargo</span>
              <span class="value">{{ selectedCourse?.Docente?.Nombre }} ({{ selectedCourse?.Docente?.Correo }})</span>
            </div>
            <div class="detail-item">
              <span class="label">Aula y Ubicación Física</span>
              <span class="value">{{ selectedCourse?.Curso?.Nombre || `Aula ${selectedCourse?.Curso?.Aula}` }} (Piso {{ selectedCourse?.Curso?.Piso }})</span>
            </div>
            <div class="detail-item">
              <span class="label">Turno y Horario</span>
              <span class="value">{{ selectedCourse?.Turno?.Nombre }} ({{ formatTime(selectedCourse?.Turno?.HoraInicio) }} - {{ formatTime(selectedCourse?.Turno?.HoraFin) }})</span>
            </div>
            <div class="detail-item">
              <span class="label">Vigencia del Calendario</span>
              <span class="value">{{ formatDate(selectedCourse?.FechaInicio) }} al {{ formatDate(selectedCourse?.FechaFin) }}</span>
            </div>
            <div class="detail-item">
              <span class="label">Fecha en la que se inscribió</span>
              <span class="value">{{ formatDate(selectedCourse?.Inscripcion?.Fecha) }}</span>
            </div>
            <div class="detail-item">
              <span class="label">Estado de Aprobación Escolar</span>
              <span class="value">
                <span 
                  class="academic-status" 
                  :class="selectedCourse?.Inscripcion?.Aprobado ? 'academic-status--approved' : 'academic-status--in-progress'"
                >
                  {{ selectedCourse?.Inscripcion?.Aprobado ? 'Aprobada' : 'En Curso / Regular' }}
                </span>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

  </section>
</template>

<script>
import axios from 'axios';

export default {
  name: 'CursosVisualizacion',
  data() {
    return {
      users: [],
      selectedUser: null,
      academicData: {
        usuario: null,
        cursos: [],
      },
      userSearchQuery: '',
      activeRoleFilter: '', // '' (todos), '2' (docentes), '3' (estudiantes)
      loadingUsers: false,
      loadingDetails: false,
      globalMessage: '',
      globalMessageType: 'error',
      
      // Modales
      showEnrolledModal: false,
      showDetailsModal: false,
      selectedCourse: null,
    };
  },
  computed: {
    filteredUsers() {
      const query = this.userSearchQuery.toLowerCase().trim();
      return this.users.filter(u => {
        // Filtrar por rol
        if (this.activeRoleFilter && String(u.IdRol) !== String(this.activeRoleFilter)) {
          return false;
        }
        // Filtrar por texto
        if (!query) return true;
        const fullName = `${u.Nombre1} ${u.Apellido1}`.toLowerCase();
        const ci = String(u.CI).toLowerCase();
        return fullName.includes(query) || ci.includes(query);
      });
    }
  },
  mounted() {
    this.init();
  },
  methods: {
    async init() {
      const token = localStorage.getItem('auth_token');
      if (token) {
        axios.defaults.headers.common.Authorization = `Bearer ${token}`;
      } else {
        window.location.href = '/';
        return;
      }

      // Defensa en profundidad: si el usuario es docente, redirigir a su panel
      const stored = localStorage.getItem('auth_user');
      if (stored) {
        const user = JSON.parse(stored);
        if (user.IdRol === 2) {
          window.location.href = '/docente/cursos';
          return;
        }
      }

      await this.loadUsers();
    },
    async loadUsers() {
      this.loadingUsers = true;
      try {
        const { data } = await axios.get('/api/usuarios');
        this.users = data?.data ?? [];
      } catch (error) {
        this.showGlobalAlert('Error al cargar la lista de usuarios del sistema.', 'error');
      } finally {
        this.loadingUsers = false;
      }
    },
    async selectUser(user) {
      this.selectedUser = user;
      this.loadingDetails = true;
      this.academicData.cursos = [];
      try {
        const { data } = await axios.get(`/api/cursos-materias/usuario/${user.IdUsuario}`);
        this.academicData = data?.data ?? { usuario: null, cursos: [] };
      } catch (error) {
        this.showGlobalAlert('No se pudo obtener la programación académica del usuario.', 'error');
      } finally {
        this.loadingDetails = false;
      }
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
    showGlobalAlert(message, type = 'error') {
      this.globalMessage = message;
      this.globalMessageType = type;
    },
    openEnrolledModal(course) {
      this.selectedCourse = course;
      this.showEnrolledModal = true;
    },
    closeEnrolledModal() {
      this.showEnrolledModal = false;
      this.selectedCourse = null;
    },
    openCourseDetailsModal(course) {
      this.selectedCourse = course;
      this.showDetailsModal = true;
    },
    closeDetailsModal() {
      this.showDetailsModal = false;
      this.selectedCourse = null;
    }
  }
};
</script>

<style scoped>
.cursos-visualizacion { min-height: 100vh; padding: 32px; background: linear-gradient(180deg, #07111f 0%, #101b2b 100%); color: #eef2ff; }
.cursos-visualizacion__header { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 32px; }
.eyebrow { margin: 0 0 8px; color: #fbbf24; text-transform: uppercase; letter-spacing: .18em; font-size: .75rem; }
h1 { margin: 0; font-size: 2rem; }
p { margin: 8px 0 0; color: #cbd5e1; }
.back-link { border-radius: 999px; padding: 12px 24px; font-weight: 700; text-decoration: none; border: 1px solid rgba(148, 163, 184, .22); color: #cbd5e1; display: inline-flex; align-items: center; justify-content: center; background: transparent; cursor: pointer; }

/* Alert standard classes */
.alert { position: relative; margin: 20px 0; padding: 16px 40px 16px 18px; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; }
.alert--success { background: rgba(16, 185, 129, .16); color: #d1fae5; border: 1px solid rgba(16, 185, 129, .3); }
.alert--error { background: rgba(239, 68, 68, .16); color: #fecaca; border: 1px solid rgba(239, 68, 68, .3); }
.alert__close { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: transparent; border: none; font-size: 1.5rem; color: inherit; cursor: pointer; }

/* Split Pane Layout */
.layout-container { display: grid; grid-template-columns: 330px 1fr; gap: 24px; min-height: 70vh; }

/* Panel Lateral de Usuarios */
.sidebar { background: rgba(15, 23, 42, .86); border: 1px solid rgba(148, 163, 184, .18); border-radius: 24px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); height: 75vh; }
.sidebar__search-box { padding: 16px; border-bottom: 1px solid rgba(148, 163, 184, .12); display: flex; flex-direction: column; gap: 12px; }
.search-input { width: 100%; border-radius: 12px; border: 1px solid rgba(148, 163, 184, .22); background: rgba(30, 41, 59, .82); color: #f8fafc; padding: 10px 14px; font-size: 0.9rem; outline: none; box-sizing: border-box; }
.search-input:focus { border-color: #fbbf24; }

.role-tabs { display: flex; gap: 6px; background: rgba(30, 41, 59, 0.5); padding: 4px; border-radius: 10px; }
.role-tabs button { flex: 1; border: none; background: transparent; color: #94a3b8; padding: 6px; font-size: 0.8rem; font-weight: 700; border-radius: 8px; cursor: pointer; transition: all 0.2s; }
.role-tabs__btn--active { background: #fbbf24 !important; color: #0f172a !important; }

.user-list { list-style: none; margin: 0; padding: 8px; overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 4px; }
.user-list__loading, .user-list__empty { padding: 24px; text-align: center; color: #94a3b8; font-size: 0.9rem; }

.user-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; border-radius: 14px; cursor: pointer; transition: all 0.2s; border: 1px solid transparent; }
.user-item:hover { background: rgba(255, 255, 255, 0.03); }
.user-item--selected { background: rgba(251, 191, 36, 0.08) !important; border-color: rgba(251, 191, 36, 0.25) !important; }
.user-item__info { display: flex; flex-direction: column; gap: 2px; }
.user-name { font-weight: 700; color: #f8fafc; font-size: 0.92rem; }
.user-sub { font-size: 0.78rem; color: #94a3b8; }

.badge { display: inline-flex; align-items: center; justify-content: center; padding: 4px 8px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
.badge--teacher { background: rgba(139, 92, 246, .16); color: #c4b5fd; border: 1px solid rgba(139, 92, 246, .3); }
.badge--student { background: rgba(52, 211, 153, .16); color: #a7f3d0; border: 1px solid rgba(52, 211, 153, .3); }

/* Panel Principal de Contenido */
.main-content { min-width: 0; }

/* Empty state styling */
.empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 60px 40px; background: rgba(15, 23, 42, 0.4); border: 1px dashed rgba(148, 163, 184, 0.2); border-radius: 24px; min-height: 50vh; }
.empty-state__icon { font-size: 3rem; margin-bottom: 16px; }
.empty-state h2 { margin: 0 0 10px; color: #f8fafc; font-size: 1.4rem; }
.empty-state p { margin: 0; color: #cbd5e1; max-width: 460px; line-height: 1.6; }

.loading-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 80px 40px; }
.spinner { width: 40px; height: 40px; border: 4px solid rgba(251, 191, 36, 0.1); border-top-color: #fbbf24; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 16px; }

/* Vista Detallada del Usuario */
.details-view { animation: fadeIn 0.3s ease-out; }

/* Tarjeta Perfil de Usuario */
.profile-card { background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(148, 163, 184, 0.18); border-radius: 24px; padding: 24px; margin-bottom: 24px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); }
.profile-card__header { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; border-bottom: 1px solid rgba(148, 163, 184, 0.12); padding-bottom: 16px; }
.avatar-circle { width: 56px; height: 56px; border-radius: 50%; background: #fbbf24; color: #0f172a; font-weight: 700; font-size: 1.25rem; display: flex; align-items: center; justify-content: center; text-transform: uppercase; box-shadow: 0 4px 10px rgba(251, 191, 36, 0.2); }
.profile-card__header h2 { margin: 0 0 6px; font-size: 1.35rem; color: #f8fafc; }

.profile-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
.profile-info-item { display: flex; flex-direction: column; gap: 4px; }
.profile-info-item .label { font-size: 0.78rem; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.05em; font-weight: 600; }
.profile-info-item .value { font-size: 0.95rem; color: #f8fafc; font-weight: 600; }

.courses-header { margin: 32px 0 16px; border-bottom: 1px solid rgba(148, 163, 184, 0.12); padding-bottom: 8px; }
.courses-header h3 { margin: 0; font-size: 1.15rem; color: #fbbf24; }

.no-courses-state { padding: 32px; text-align: center; color: #94a3b8; background: rgba(15, 23, 42, 0.3); border-radius: 16px; border: 1px dashed rgba(148, 163, 184, 0.1); }

/* Cuadrícula de Tarjetas de Cursos */
.courses-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; }
.course-card { background: rgba(15, 23, 42, .86); border: 1px solid rgba(148, 163, 184, .18); border-radius: 20px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 10px 25px rgba(0, 0, 0, .1); transition: all 0.25s; }
.course-card:hover { transform: translateY(-3px); border-color: rgba(251, 191, 36, 0.3); box-shadow: 0 12px 30px rgba(0, 0, 0, .2); }
.course-card--inactive { opacity: 0.6; }

.course-card__header { padding: 16px 20px; background: rgba(30, 41, 59, 0.4); border-bottom: 1px solid rgba(148, 163, 184, 0.08); display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }
.course-code { font-family: monospace; font-size: 0.8rem; color: #fbbf24; font-weight: 700; text-transform: uppercase; }
.course-card__header h4 { margin: 2px 0 0; font-size: 1.02rem; color: #f8fafc; line-height: 1.4; }

.status-indicator { font-size: 0.72rem; padding: 2px 6px; border-radius: 4px; font-weight: 700; text-transform: uppercase; }
.status-indicator--active { background: rgba(52, 211, 153, 0.15); color: #34d399; }
.status-indicator--inactive { background: rgba(239, 68, 68, 0.15); color: #f87171; }

.course-card__body { padding: 20px; flex: 1; display: flex; flex-direction: column; gap: 12px; }
.info-row { display: flex; align-items: flex-start; gap: 10px; font-size: 0.88rem; color: #e2e8f0; line-height: 1.4; }
.info-row .icon { font-size: 1rem; width: 16px; text-align: center; }

.turno-badge { background: rgba(147, 51, 234, 0.15); color: #c084fc; border: 1px solid rgba(147, 51, 234, 0.25); border-radius: 4px; padding: 1px 6px; font-size: 0.75rem; font-weight: 700; }

.divider { height: 1px; background: rgba(148, 163, 184, 0.1); margin: 6px 0; }

.academic-status { font-weight: 700; font-size: 0.78rem; padding: 2px 8px; border-radius: 6px; text-transform: uppercase; display: inline-flex; }
.academic-status--approved { background: rgba(16, 185, 129, 0.18); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
.academic-status--in-progress { background: rgba(245, 158, 11, 0.18); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }

.slots-container { display: flex; flex-direction: column; gap: 6px; font-size: 0.88rem; color: #cbd5e1; }
.progress-bar { height: 8px; background: rgba(30, 41, 59, 0.8); border-radius: 999px; overflow: hidden; border: 1px solid rgba(148, 163, 184, 0.1); }
.progress-bar__fill { height: 100%; background: #34d399; border-radius: 999px; transition: width 0.3s ease; }
.progress-bar__fill--full { background: #ef4444 !important; }

.course-card__actions { padding: 12px 20px 20px; display: flex; justify-content: stretch; }
.btn-action-primary, .btn-action-secondary { width: 100%; border-radius: 10px; padding: 10px; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; }
.btn-action-primary { background: #fbbf24; color: #0f172a; }
.btn-action-primary:hover { background: #f59e0b; }
.btn-action-secondary { background: rgba(255, 255, 255, 0.06); color: #cbd5e1; border: 1px solid rgba(148, 163, 184, 0.2); }
.btn-action-secondary:hover { background: rgba(255, 255, 255, 0.1); color: #f8fafc; }

/* Modales */
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(7, 11, 25, 0.85); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px; backdrop-filter: blur(8px); }
.modal-card { background: #0f172a; border: 1px solid rgba(148, 163, 184, .25); border-radius: 24px; width: 100%; max-width: 850px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); animation: zoomIn 0.22s ease-out; }
.modal-card--narrow { max-width: 520px; }
.modal-card__header { padding: 20px 24px; border-bottom: 1px solid rgba(148, 163, 184, .14); display: flex; justify-content: space-between; align-items: center; background: rgba(30, 41, 59, .4); }
.modal-card__header h2 { margin: 0 0 4px; font-size: 1.25rem; color: #f8fafc; }
.modal-card__header p { margin: 0; }
.btn-close-modal { background: transparent; border: none; color: #94a3b8; font-size: 2rem; cursor: pointer; line-height: 1; padding: 0; }
.btn-close-modal:hover { color: #f8fafc; }
.modal-card__body { padding: 24px; overflow-y: auto; max-height: 75vh; }

.empty-modal-state { padding: 40px; text-align: center; color: #94a3b8; }

/* Tabla Estudiantes */
.table-container { background: rgba(15, 23, 42, .4); border: 1px solid rgba(148, 163, 184, .12); border-radius: 16px; overflow: hidden; }
.students-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.88rem; }
.students-table th { background: rgba(30, 41, 59, .6); color: #f8fafc; font-weight: 700; padding: 12px 16px; border-bottom: 1px solid rgba(148, 163, 184, .12); }
.students-table td { padding: 12px 16px; border-bottom: 1px solid rgba(148, 163, 184, .06); vertical-align: middle; color: #e2e8f0; }
.students-table tbody tr:hover { background: rgba(255, 255, 255, .01); }

.student-cell { display: flex; flex-direction: column; }
.student-name { font-weight: 700; color: #f8fafc; }
.student-email { font-size: 0.78rem; color: #94a3b8; margin-top: 1px; }

/* Detalles del Curso */
.detail-block { display: flex; flex-direction: column; gap: 16px; }
.detail-item { display: flex; flex-direction: column; gap: 4px; padding-bottom: 12px; border-bottom: 1px solid rgba(148, 163, 184, 0.08); }
.detail-item:last-child { border-bottom: none; }
.detail-item .label { font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #94a3b8; }
.detail-item .value { font-size: 0.95rem; color: #f8fafc; font-weight: 600; line-height: 1.5; }

/* Animations */
@keyframes spin {
  to { transform: rotate(360deg); }
}
@keyframes zoomIn {
  from { transform: scale(0.95); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@media (max-width: 900px) {
  .layout-container { grid-template-columns: 1fr; }
  .sidebar { height: auto; max-height: 40vh; }
  .profile-grid { grid-template-columns: 1fr; }
}
</style>
