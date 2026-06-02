<template>
  <section class="dashboard-panel">
    
    <!-- Cabecera del Panel (Se oculta al imprimir reportes en PDF) -->
    <header class="dashboard-panel__header no-print">
      <div class="user-welcome">
        <div class="avatar-circle">
          {{ userInitials }}
        </div>
        <div>
          <p class="eyebrow">RF08 • Panel de Usuario</p>
          <h1>¡Bienvenido de vuelta, {{ user?.Nombre1 || 'Usuario' }}!</h1>
          <p class="lead">Perfil: <strong class="text-warning">{{ dashboardData.rol || 'General' }}</strong></p>
        </div>
      </div>
      <div class="header-actions">
        <!-- Navegación interactiva rápida para el Admin si no está en resumen -->
        <button v-if="activeTab !== 'summary'" class="btn-action btn-action--secondary" @click="activeTab = 'summary'">
          📊 Ir al Panel Principal
        </button>
        <a class="btn-action btn-action--secondary" href="/index">Volver al Panel</a>
        <a class="btn-action btn-action--primary" href="/perfil">Mi Perfil</a>
      </div>
    </header>

    <!-- Estado de Carga -->
    <div v-if="loading" class="loading-state no-print">
      <div class="spinner"></div>
      <p>Cargando tus estadísticas y actividades recientes...</p>
    </div>

    <div v-else class="dashboard-content">
      
      <!-- Banner de Navegación de Regreso para Reportes/Notificaciones (no imprimible) -->
      <div v-if="activeTab !== 'summary'" class="tab-back-banner no-print">
        <button class="back-tab-btn" @click="activeTab = 'summary'">
          ⬅ Volver al Resumen General
        </button>
      </div>

      <!-- VISTA 1: Resumen General (Métricas + Timeline + Accesos Rápidos) -->
      <div v-if="activeTab === 'summary'">
        
        <!-- Fila 1: Resumen de Métricas (Stats Cards) -->
        <div class="stats-grid">
          <div 
            v-for="(stat, idx) in dashboardData.resumen" 
            :key="idx" 
            class="stat-card"
            :style="{ background: stat.gradiente }"
          >
            <div class="stat-card__overlay">
              <span class="stat-card__icon">{{ stat.icono }}</span>
              <div class="stat-card__info">
                <span class="stat-card__label">{{ stat.titulo }}</span>
                <strong class="stat-card__value">{{ stat.valor }}</strong>
              </div>
            </div>
          </div>
        </div>

        <!-- Fila 2: Actividades e Información Relevante -->
        <div class="main-grid">
          
          <!-- Columna Izquierda: Actividades Recientes -->
          <div class="card card--timeline">
            <div class="card__header">
              <h3>⚡ Actividades Recientes</h3>
              <span class="badge">Historial</span>
            </div>
            <div class="card__body">
              <div v-if="!dashboardData.actividades || dashboardData.actividades.length === 0" class="empty-feed">
                <p>No se registran actividades recientes en tu panel académico.</p>
              </div>
              
              <ul v-else class="timeline">
                <li 
                  v-for="(act, index) in dashboardData.actividades" 
                  :key="index" 
                  class="timeline-item"
                >
                  <div class="timeline-badge" :class="`timeline-badge--${act.tipo}`">
                    {{ getIconForType(act.tipo) }}
                  </div>
                  <div class="timeline-panel">
                    <div class="timeline-heading">
                      <h4>{{ act.titulo }}</h4>
                      <span class="timeline-time">{{ formatDateTime(act.fecha) }}</span>
                    </div>
                    <p class="timeline-desc">{{ act.descripcion }}</p>
                  </div>
                </li>
              </ul>
            </div>
          </div>

          <!-- Columna Derecha: Información Relevante y Accesos Rápidos -->
          <div class="sidebar-grid">
            
            <!-- Información Relevante -->
            <div class="card card--info">
              <div class="card__header">
                <h3>ℹ️ Información Relevante</h3>
              </div>
              <div class="card__body">
                <div class="info-block">
                  <p class="info-msg">{{ dashboardData.info_relevante?.mensaje }}</p>
                  <div class="info-alert">
                    <span class="info-alert__icon">💡</span>
                    <p class="info-alert__text">{{ dashboardData.info_relevante?.ayuda }}</p>
                  </div>
                </div>

                <!-- Ficha de Perfil Rápida (Para Estudiantes) -->
                <div v-if="user?.IdRol === 3" class="student-meta-box">
                  <h4>Ficha Académica Activa</h4>
                  <div class="meta-row">
                    <span class="meta-label">Carrera:</span>
                    <span class="meta-value text-warning">{{ academicCareer }}</span>
                  </div>
                  <div class="meta-row">
                    <span class="meta-label">Modalidad:</span>
                    <span class="meta-value text-info">{{ academicModalidad }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Accesos Rápidos -->
            <div class="card card--shortcuts">
              <div class="card__header">
                <h3>🚀 Accesos Rápidos</h3>
              </div>
              <div class="card__body shortcuts-body">
                <!-- Accesos Admin -->
                <template v-if="user?.IdRol === 1">
                  <button @click="activeTab = 'notifications'" class="shortcut-btn shortcut-btn--highlight">
                    🔔 Bandeja de Notificaciones
                  </button>
                  <button @click="activeTab = 'reports'" class="shortcut-btn shortcut-btn--highlight">
                    📊 Reporte de Materias
                  </button>
                  <button @click="activeTab = 'reports-v2'" class="shortcut-btn shortcut-btn--premium">
                    📋 Reportes del Sistema
                  </button>
                  <a href="/usuarios/create" class="shortcut-btn">Registrar Usuario</a>
                  <a href="/usuarios" class="shortcut-btn">Gestionar Usuarios</a>
                  <a href="/cursos" class="shortcut-btn">Gestionar Cursos</a>
                  <a href="/cursos/visualizacion" class="shortcut-btn">Ver Cursos por Usuario</a>
                </template>

                <template v-if="user?.IdRol === 2">
                  <button @click="activeTab = 'reports-v2'" class="shortcut-btn shortcut-btn--premium">
                    📋 Reportes
                  </button>
                  <a href="/docente/cursos" class="shortcut-btn">Mis Cursos y Alumnos</a>
                  <a href="/docente/notas" class="shortcut-btn">Gestionar Notas</a>
                  <a href="/perfil" class="shortcut-btn">Actualizar mis Datos</a>
                </template>

                <template v-if="user?.IdRol === 3">
                  <button @click="activeTab = 'reports-v2'" class="shortcut-btn shortcut-btn--premium">
                    📋 Mis Reportes
                  </button>
                  <a href="/inscripciones" class="shortcut-btn">Inscribirme a Cursos</a>
                  <a href="/perfil" class="shortcut-btn">Cambiar Contraseña</a>
                </template>
              </div>
            </div>

          </div>

        </div>
      </div>

      <!-- VISTA 2: Bandeja de Notificaciones (Diseño Todo-List) -->
      <div v-else-if="activeTab === 'notifications'">
        <AdminNotificacion :userRole="user?.IdRol || 1" />
      </div>

      <!-- VISTA 3: Reporte de Materias por Carrera (Pattern Strategy + Excel + PDF) -->
      <div v-else-if="activeTab === 'reports'">
        <AdminReporte :user="user" />
      </div>

      <!-- VISTA 4: Reportes del Sistema (Multirrol) -->
      <div v-else-if="activeTab === 'reports-v2'">
        <ReportesApp :user="user" />
      </div>

    </div>

  </section>
</template>

<script>
import axios from 'axios';
import AdminNotificacion from './AdminNotificacion.vue';
import AdminReporte from './AdminReporte.vue';
import ReportesApp from './ReportesApp.vue';

export default {
  name: 'Dashboard',
  components: {
    AdminNotificacion,
    AdminReporte,
    ReportesApp
  },
  data() {
    return {
      user: null,
      activeTab: 'summary', // summary, notifications, reports, reports-v2
      dashboardData: {
        rol: '',
        resumen: [],
        actividades: [],
        info_relevante: {
          mensaje: '',
          ayuda: '',
        }
      },
      loading: false,
    };
  },
  computed: {
    userInitials() {
      if (!this.user) return 'U';
      const n = this.user.Nombre1 ? this.user.Nombre1[0] : '';
      const a = this.user.Apellido1 ? this.user.Apellido1[0] : '';
      return (n + a).toUpperCase();
    },
    academicCareer() {
      const stored = localStorage.getItem('auth_user');
      if (stored) {
        const u = JSON.parse(stored);
        if (u.IdCarrera === 1) return 'Ingeniería de Sistemas';
        if (u.IdCarrera === 2) return 'Medicina';
        if (u.IdCarrera === 3) return 'Administración de Empresas';
      }
      return 'Carrera General';
    },
    academicModalidad() {
      const stored = localStorage.getItem('auth_user');
      if (stored) {
        const u = JSON.parse(stored);
        if (u.IdModalidad === 1) return 'Presencial';
        if (u.IdModalidad === 2) return 'Semipresencial';
        if (u.IdModalidad === 3) return 'Virtual';
      }
      return 'Regular';
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

      // Cargar perfil rápido desde LocalStorage
      const stored = localStorage.getItem('auth_user');
      if (stored) {
        this.user = JSON.parse(stored);
      }

      await this.loadStats();
      this.loading = false;
    },
    async loadStats() {
      try {
        const { data } = await axios.get('/api/dashboard/stats');
        this.dashboardData = data?.data ?? { rol: '', resumen: [], actividades: [], info_relevante: {} };
      } catch (error) {
        console.error('Error al cargar estadísticas del panel:', error);
      }
    },
    getIconForType(type) {
      const iconMap = {
        'usuario': '👤',
        'curso': '🗓️',
        'inscripcion': '🎓',
        'inscripcion_estudiante': '✍️',
      };
      return iconMap[type] || '⚡';
    },
    formatDateTime(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString('es-ES', { 
        day: 'numeric', 
        month: 'short', 
        hour: '2-digit', 
        minute: '2-digit' 
      });
    }
  }
};
</script>

<style scoped>
.dashboard-panel { min-height: 100vh; padding: 32px; background: linear-gradient(180deg, #07111f 0%, #101b2b 100%); color: #eef2ff; }
.dashboard-panel__header { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 32px; }
.user-welcome { display: flex; align-items: center; gap: 18px; }
.avatar-circle { width: 62px; height: 62px; border-radius: 50%; background: #fbbf24; color: #0f172a; font-weight: 700; font-size: 1.4rem; display: flex; align-items: center; justify-content: center; text-transform: uppercase; box-shadow: 0 4px 12px rgba(251, 191, 36, 0.25); }
.eyebrow { margin: 0 0 4px; color: #fbbf24; text-transform: uppercase; letter-spacing: .18em; font-size: .75rem; }
h1 { margin: 0; font-size: 1.8rem; }
.lead { margin: 6px 0 0; color: #cbd5e1; }

.header-actions { display: flex; gap: 12px; }
.btn-action { border-radius: 999px; padding: 12px 24px; font-weight: 700; text-decoration: none; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; }
.btn-action--primary { background: #fbbf24; color: #0f172a; }
.btn-action--primary:hover { background: #f59e0b; }
.btn-action--secondary { background: transparent; border: 1px solid rgba(148, 163, 184, .22); color: #cbd5e1; }
.btn-action--secondary:hover { background: rgba(255, 255, 255, 0.05); color: #f8fafc; }

.loading-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 80px 40px; }
.spinner { width: 40px; height: 40px; border: 4px solid rgba(251, 191, 36, 0.1); border-top-color: #fbbf24; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 16px; }

/* Banner navegación tab */
.tab-back-banner {
  margin-bottom: 20px;
}
.back-tab-btn {
  background: rgba(251, 191, 36, 0.1);
  border: 1px solid rgba(251, 191, 36, 0.3);
  color: #fbbf24;
  padding: 8px 16px;
  border-radius: 12px;
  font-weight: 700;
  cursor: pointer;
  font-size: 0.85rem;
  transition: all 0.2s;
}
.back-tab-btn:hover {
  background: #fbbf24;
  color: #0f172a;
}

/* Grid de Métricas (Stats) */
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 32px; }
.stat-card { border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); transition: transform 0.25s; }
.stat-card:hover { transform: translateY(-3px); }
.stat-card__overlay { background: rgba(15, 23, 42, 0.35); padding: 20px; display: flex; align-items: center; gap: 16px; height: 100%; box-sizing: border-box; }
.stat-card__icon { font-size: 2.2rem; }
.stat-card__info { display: flex; flex-direction: column; }
.stat-card__label { font-size: 0.8rem; text-transform: uppercase; color: rgba(238, 242, 255, 0.8); font-weight: 600; letter-spacing: 0.02em; }
.stat-card__value { font-size: 1.8rem; color: #ffffff; margin-top: 4px; font-weight: 800; }

/* Grid de Contenido Principal */
.main-grid { display: grid; grid-template-columns: 1fr 360px; gap: 24px; }

/* Estructura de Tarjetas */
.card { background: rgba(15, 23, 42, .86); border: 1px solid rgba(148, 163, 184, .18); border-radius: 24px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); margin-bottom: 24px; }
.card__header { padding: 18px 24px; border-bottom: 1px solid rgba(148, 163, 184, .12); display: flex; justify-content: space-between; align-items: center; background: rgba(30, 41, 59, .4); }
.card__header h3 { margin: 0; font-size: 1.1rem; color: #fbbf24; }
.card__body { padding: 24px; }

.badge { background: rgba(251, 191, 36, 0.15); color: #fbbf24; border: 1px solid rgba(251, 191, 36, 0.25); border-radius: 8px; padding: 4px 10px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; }

/* Timeline (Actividades Recientes) */
.empty-feed { text-align: center; color: #94a3b8; padding: 40px; }
.timeline { list-style: none; padding: 0; margin: 0; position: relative; }
.timeline::before { content: ''; position: absolute; top: 0; bottom: 0; left: 20px; width: 2px; background: rgba(148, 163, 184, 0.15); }

.timeline-item { position: relative; margin-bottom: 24px; padding-left: 56px; }
.timeline-item:last-child { margin-bottom: 0; }

.timeline-badge { position: absolute; left: 0; top: 2px; width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25); box-sizing: border-box; }
.timeline-badge--usuario { background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 2px solid rgba(56, 189, 248, 0.35); }
.timeline-badge--curso { background: rgba(167, 139, 250, 0.15); color: #a78bfa; border: 2px solid rgba(167, 139, 250, 0.35); }
.timeline-badge--inscripcion, .timeline-badge--inscripcion_estudiante { background: rgba(52, 211, 153, 0.15); color: #34d399; border: 2px solid rgba(52, 211, 153, 0.35); }

.timeline-panel { background: rgba(30, 41, 59, 0.3); border: 1px solid rgba(148, 163, 184, 0.08); border-radius: 16px; padding: 16px; }
.timeline-heading { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 6px; }
.timeline-heading h4 { margin: 0; font-size: 0.95rem; color: #f8fafc; font-weight: 700; }
.timeline-time { font-size: 0.75rem; color: #94a3b8; font-weight: 600; white-space: nowrap; }
.timeline-desc { margin: 0; font-size: 0.88rem; color: #cbd5e1; line-height: 1.5; }

/* Información Relevante */
.info-block { display: flex; flex-direction: column; gap: 14px; }
.info-msg { margin: 0; font-size: 0.92rem; color: #cbd5e1; line-height: 1.6; }
.info-alert { display: flex; gap: 12px; background: rgba(251, 191, 36, 0.08); border: 1px solid rgba(251, 191, 36, 0.22); padding: 12px; border-radius: 14px; }
.info-alert__icon { font-size: 1.15rem; }
.info-alert__text { margin: 0; font-size: 0.85rem; color: #fef3c7; line-height: 1.5; }

/* Ficha Académica Estudiante */
.student-meta-box { margin-top: 24px; border-top: 1px solid rgba(148, 163, 184, 0.12); padding-top: 20px; }
.student-meta-box h4 { margin: 0 0 12px; font-size: 0.88rem; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.05em; }
.meta-row { display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 8px; }
.meta-label { color: #cbd5e1; }
.meta-value { font-weight: 700; }

/* Accesos Rápidos */
.shortcuts-body { display: flex; flex-direction: column; gap: 10px; }
.shortcut-btn { display: flex; align-items: center; justify-content: center; padding: 12px; border-radius: 12px; background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(148, 163, 184, 0.15); color: #cbd5e1; text-decoration: none; font-weight: 700; font-size: 0.88rem; transition: all 0.2s; text-align: center; cursor: pointer; }
.shortcut-btn:hover { background: rgba(251, 191, 36, 0.08); border-color: #fbbf24; color: #fbbf24; }

.shortcut-btn--highlight {
  background: rgba(251, 191, 36, 0.08);
  border-color: rgba(251, 191, 36, 0.35);
  color: #fbbf24;
}
.shortcut-btn--highlight:hover {
  background: #fbbf24;
  color: #0f172a;
}

.shortcut-btn--premium {
  background: rgba(167, 139, 250, 0.12);
  border-color: rgba(167, 139, 250, 0.35);
  color: #c4b5fd;
}
.shortcut-btn--premium:hover {
  background: #a78bfa;
  color: #0f172a;
}

/* Animations */
@keyframes spin {
  to { transform: rotate(360deg); }
}

@media (max-width: 900px) {
  .dashboard-panel__header { flex-direction: column; align-items: stretch; gap: 20px; }
  .user-welcome { flex-direction: column; text-align: center; }
  .header-actions { justify-content: center; }
  .main-grid { grid-template-columns: 1fr; }
}

/* IMPRESIÓN DEL REPORTE */
@media print {
  .no-print {
    display: none !important;
  }
  .dashboard-panel {
    background: transparent !important;
    padding: 0 !important;
  }
}
</style>
