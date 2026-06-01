<template>
  <section class="dashboard">
    <header class="dashboard__header">
      <div>
        <p class="eyebrow">Panel principal</p>
        <h1>Bienvenido{{ user?.Nombre1 ? `, ${user.Nombre1}` : '' }}</h1>
        <p class="lead">
          {{ roleLabel }} | {{ user?.Rol?.Descripcion || 'Acceso al sistema escolar' }}
        </p>
      </div>
      <button class="logout" @click="logout" :disabled="loading">
        {{ loading ? 'Saliendo...' : 'Cerrar sesión' }}
      </button>
    </header>

    <div class="role-banner" :class="roleClass">
      <p class="role-banner__title">{{ roleLabel }}</p>
      <p class="role-banner__text">{{ roleWelcome }}</p>
    </div>

    <!-- Acciones exclusivas Admin -->
    <div class="role-actions" v-if="user?.IdRol === 1">
      <a class="role-link" href="/usuarios/create">Registrar usuarios</a>
      <a class="role-link" href="/usuarios">Gestionar usuarios</a>
      <a class="role-link" href="/cursos">Gestionar cursos</a>
    </div>

    <!-- Acciones generales -->
    <div class="role-actions">
      <a class="role-link" href="/perfil">Mi perfil</a>
      <a class="role-link" href="/dashboard">Dashboard</a>
      <a class="role-link" href="/reportes">Reportes</a>
      <a class="role-link" href="/cursos/visualizacion">Ver cursos</a>
      
      <!-- Botón de Notificaciones con contador -->
      <button 
        class="role-link notification-btn" 
        :class="{ 'notification-btn--active': showNotificaciones }"
        @click="toggleNotificationPanel"
        style="cursor: pointer; border: 0;"
      >
        🔔 Notificaciones 
        <span v-if="unreadCount > 0" class="badge-count">{{ unreadCount }}</span>
      </button>
    </div>

    <div class="role-actions" v-if="user?.IdRol === 3">
      <a class="role-link" href="/inscripciones">Inscribirme a cursos</a>
    </div>

    <div class="role-actions" v-if="user?.IdRol === 2">
      <a class="role-link" href="/docente/inscripciones">Ver inscritos</a>
    </div>

    <!-- PANEL DE NOTIFICACIONES (Sección Acordeón Desplegable) -->
    <transition name="fade">
      <div v-if="showNotificaciones" class="notifications-section">
        <div class="noti-header">
          <h3>Bandeja de Notificaciones ({{ notifications.length }})</h3>
          <button class="close-noti-btn" @click="showNotificaciones = false">✕ Cerrar</button>
        </div>

        <!-- Creador de notificaciones (Solo para Admin) -->
        <div v-if="user?.IdRol === 1" class="admin-noti-composer">
          <button class="toggle-composer-btn" @click="showComposer = !showComposer">
            {{ showComposer ? '✕ Cancelar Envío' : '➕ Redactar Nueva Notificación' }}
          </button>
          
          <transition name="slide">
            <form v-if="showComposer" @submit.prevent="sendNotification" class="composer-form">
              <h4>Enviar Notificación Directa</h4>
              <div class="form-group">
                <label>Usuario Destino:</label>
                <select v-model="composerData.IdUsuario" required>
                  <option value="" disabled>Seleccione un usuario...</option>
                  <option v-for="u in usuariosList" :key="u.IdUsuario" :value="u.IdUsuario">
                    [{{ getRoleName(u.IdRol) }}] {{ u.Nombre1 }} {{ u.Apellido1 }} ({{ u.Correo }})
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label>Título:</label>
                <input type="text" v-model="composerData.Titulo" placeholder="Ej. Recordatorio de Examen" required />
              </div>
              <div class="form-group">
                <label>Mensaje / Contenido:</label>
                <textarea v-model="composerData.Contenido" placeholder="Escribe el mensaje..." required></textarea>
              </div>
              <button type="submit" class="send-btn" :disabled="sendingNoti">
                {{ sendingNoti ? 'Enviando...' : 'Enviar Notificación' }}
              </button>
            </form>
          </transition>
        </div>

        <div v-if="loadingNoti" class="noti-loading">
          <div class="mini-spinner"></div>
          <p>Cargando notificaciones...</p>
        </div>

        <div v-else>
          <div v-if="notifications.length === 0" class="noti-empty">
            <p>No tienes notificaciones en este momento.</p>
          </div>

          <!-- Acordeón interactivo -->
          <ul v-else class="noti-accordion">
            <li 
              v-for="noti in notifications" 
              :key="noti.IdNotificacion" 
              class="noti-item"
              :class="{ 'noti-item--unread': noti.Estado }"
            >
              <!-- Cabecera de Notificación (Click para expandir) -->
              <div class="noti-item__header" @click="expandNotification(noti)">
                <div class="noti-item__title-box">
                  <span class="noti-status-dot" v-if="noti.Estado"></span>
                  <span class="noti-item__title">{{ noti.Titulo }}</span>
                </div>
                <div class="noti-item__meta">
                  <span class="noti-item__date">{{ formatDateTime(noti.FechaEnvio) }}</span>
                  <span class="noti-chevron">{{ expandedNotis[noti.IdNotificacion] ? '▲' : '▼' }}</span>
                </div>
              </div>

              <!-- Contenido de Notificación (Desplegable) -->
              <transition name="expand">
                <div v-if="expandedNotis[noti.IdNotificacion]" class="noti-item__body">
                  <p class="noti-item__content">{{ noti.Contenido }}</p>
                  <div class="noti-item__actions">
                    <button class="noti-read-toggle-btn" @click.stop="toggleRead(noti)">
                      {{ noti.Estado ? 'Marcar como leída' : 'Marcar como no leída' }}
                    </button>
                    <button class="noti-delete-btn" @click.stop="deleteNotification(noti.IdNotificacion)">
                      🗑️ Eliminar
                    </button>
                  </div>
                </div>
              </transition>
            </li>
          </ul>
        </div>
      </div>
    </transition>

    <div class="cards" v-if="!showNotificaciones">
      <article v-for="card in visibleCards" :key="card.title" class="card">
        <h2>{{ card.title }}</h2>
        <p>{{ card.description }}</p>
      </article>
    </div>
  </section>
</template>

<script>
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';

export default {
  name: 'IndexPage',
  setup() {
    const user = ref(null);
    const loading = ref(false);

    // Estados de Notificación
    const showNotificaciones = ref(false);
    const notifications = ref([]);
    const expandedNotis = ref({});
    const loadingNoti = ref(false);
    
    // Estados de Redactor Admin
    const showComposer = ref(false);
    const usuariosList = ref([]);
    const sendingNoti = ref(false);
    const composerData = ref({
      IdUsuario: '',
      Titulo: '',
      Contenido: ''
    });

    const roleMap = {
      1: {
        label: 'Administrador',
        welcome: 'Tienes acceso completo al sistema y sus módulos de gestión.',
        className: 'role-banner--admin',
        cards: [
          { title: 'Usuarios', description: 'Crear, editar, activar y desactivar cuentas.' },
          { title: 'Roles y permisos', description: 'Administrar accesos por perfil de usuario.' },
          { title: 'Reportes', description: 'Visualizar estadísticas y control general.' },
          { title: 'Configuración', description: 'Ajustes globales del sistema.' },
        ],
      },
      2: {
        label: 'Docente',
        welcome: 'Gestiona tus cursos, materias y calificaciones asignadas.',
        className: 'role-banner--teacher',
        cards: [
          { title: 'Mis cursos', description: 'Ver las clases asignadas en el periodo actual.' },
          { title: 'Registro de notas', description: 'Cargar y actualizar calificaciones.' },
          { title: 'Estudiantes', description: 'Consultar listas e información de inscritos.' },
          { title: 'Notificaciones', description: 'Revisar avisos académicos y del sistema.' },
        ],
      },
      3: {
        label: 'Estudiante',
        welcome: 'Consulta tus materias, avances y notificaciones personales.',
        className: 'role-banner--student',
        cards: [
          { title: 'Mis inscripciones', description: 'Ver materias y cursos en los que estás inscrito.' },
          { title: 'Mis notas', description: 'Consultar tus calificaciones registradas.' },
          { title: 'Horario', description: 'Revisar tus horarios y turnos.' },
          { title: 'Notificaciones', description: 'Leer mensajes importantes del sistema.' },
        ],
      },
    };

    const currentRole = computed(() => roleMap[user.value?.IdRol] ?? {
      label: 'Usuario',
      welcome: 'Acceso general al sistema.',
      className: 'role-banner--default',
      cards: [
        { title: 'Panel', description: 'Contenido disponible según permisos.' },
      ],
    });

    const roleLabel = computed(() => currentRole.value.label);
    const roleWelcome = computed(() => currentRole.value.welcome);
    const roleClass = computed(() => currentRole.value.className);
    const visibleCards = computed(() => currentRole.value.cards);
    
    // Contador de notificaciones no leídas
    const unreadCount = computed(() => notifications.value.filter(n => n.Estado).length);

    onMounted(async () => {
      const token = localStorage.getItem('auth_token');

      if (!token) {
        window.location.href = '/';
        return;
      }

      axios.defaults.headers.common.Authorization = `Bearer ${token}`;

      try {
        const storedUser = localStorage.getItem('auth_user');
        if (storedUser) {
          user.value = JSON.parse(storedUser);
        }

        const response = await axios.get('/api/auth/perfil');
        user.value = response.data.data.user;
        localStorage.setItem('auth_user', JSON.stringify(user.value));

        // Cargar notificaciones
        await fetchNotifications();

        // Cargar usuarios si es administrador
        if (user.value.IdRol === 1) {
          await fetchUsuarios();
        }
      } catch (error) {
        console.error(error);
        localStorage.removeItem('auth_token');
        localStorage.removeItem('auth_user');
        delete axios.defaults.headers.common.Authorization;
        window.location.href = '/';
      }
    });

    // Métodos para Notificaciones
    const fetchNotifications = async () => {
      loadingNoti.value = true;
      try {
        const { data } = await axios.get('/api/notificaciones');
        notifications.value = data.data || [];
      } catch (error) {
        console.error('Error cargando notificaciones:', error);
      } finally {
        loadingNoti.value = false;
      }
    };

    const fetchUsuarios = async () => {
      try {
        const { data } = await axios.get('/api/usuarios');
        usuariosList.value = data.data || [];
      } catch (error) {
        console.error('Error cargando usuarios:', error);
      }
    };

    const toggleNotificationPanel = () => {
      showNotificaciones.value = !showNotificaciones.value;
      if (showNotificaciones.value) {
        fetchNotifications();
      }
    };

    const expandNotification = async (noti) => {
      const id = noti.IdNotificacion;
      expandedNotis.value[id] = !expandedNotis.value[id];

      // Al expandir una notificación no leída, la marcamos automáticamente como leída
      if (expandedNotis.value[id] && noti.Estado) {
        await toggleRead(noti);
      }
    };

    const toggleRead = async (noti) => {
      try {
        noti.Estado = !noti.Estado;
        await axios.patch(`/api/notificaciones/${noti.IdNotificacion}/toggle`);
      } catch (error) {
        console.error('Error al alternar lectura:', error);
        noti.Estado = !noti.Estado; // revertir
      }
    };

    const deleteNotification = async (id) => {
      try {
        notifications.value = notifications.value.filter(n => n.IdNotificacion !== id);
        await axios.delete(`/api/notificaciones/${id}`);
      } catch (error) {
        console.error('Error al eliminar notificación:', error);
        fetchNotifications();
      }
    };

    const sendNotification = async () => {
      sendingNoti.value = true;
      try {
        await axios.post('/api/notificaciones', composerData.value);
        composerData.value.Titulo = '';
        composerData.value.Contenido = '';
        showComposer.value = false;
        alert('¡Notificación enviada con éxito!');
        await fetchNotifications();
      } catch (error) {
        console.error('Error enviando notificación:', error);
        alert('Error al enviar. Inténtalo de nuevo.');
      } finally {
        sendingNoti.value = false;
      }
    };

    const getRoleName = (idRol) => {
      const roles = { 1: 'Admin', 2: 'Docente', 3: 'Estudiante' };
      return roles[idRol] || 'Usuario';
    };

    const formatDateTime = (dateString) => {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString('es-ES', { 
        day: 'numeric', 
        month: 'short', 
        hour: '2-digit', 
        minute: '2-digit' 
      });
    };

    const logout = async () => {
      try {
        loading.value = true;

        if (localStorage.getItem('auth_token')) {
          await axios.post('/api/auth/logout');
        }
      } catch (error) {
        console.error(error);
      } finally {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('auth_user');
        delete axios.defaults.headers.common.Authorization;
        window.location.href = '/';
        loading.value = false;
      }
    };

    return {
      user,
      loading,
      roleLabel,
      roleWelcome,
      roleClass,
      visibleCards,
      logout,
      
      // Retornos de notificaciones
      showNotificaciones,
      notifications,
      expandedNotis,
      loadingNoti,
      unreadCount,
      showComposer,
      usuariosList,
      sendingNoti,
      composerData,
      toggleNotificationPanel,
      expandNotification,
      toggleRead,
      deleteNotification,
      sendNotification,
      getRoleName,
      formatDateTime
    };
  },
};
</script>

<style scoped>
.dashboard {
  display: grid;
  gap: 1.5rem;
  color: #e5e7eb;
}

.dashboard__header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
}

.eyebrow {
  margin: 0 0 0.35rem;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  font-size: 0.72rem;
  color: #fbbf24;
}

h1 {
  margin: 0;
  font-size: 1.9rem;
}

.lead {
  margin: 0.5rem 0 0;
  color: #cbd5e1;
}

.logout {
  border: 0;
  border-radius: 999px;
  padding: 0.8rem 1.1rem;
  background: linear-gradient(135deg, #f97316, #fbbf24);
  color: #111827;
  font-weight: 700;
  cursor: pointer;
}

.role-banner {
  padding: 1rem 1.1rem;
  border-radius: 18px;
  color: #111827;
}

.role-banner--admin {
  background: linear-gradient(135deg, #f59e0b, #ef4444);
}

.role-banner--teacher {
  background: linear-gradient(135deg, #38bdf8, #2563eb);
}

.role-banner--student {
  background: linear-gradient(135deg, #34d399, #10b981);
}

.role-banner--default {
  background: linear-gradient(135deg, #fbbf24, #f97316);
}

.role-banner__title {
  margin: 0 0 0.35rem;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.72rem;
  font-weight: 700;
}

.role-banner__text {
  margin: 0;
  font-weight: 600;
}

.cards {
  display: grid;
  gap: 1rem;
}

.card {
  padding: 1rem;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.card h2 {
  margin: 0 0 0.5rem;
  font-size: 1.05rem;
}

.card p {
  margin: 0;
  color: #cbd5e1;
}

.role-actions {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.role-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.8rem 1rem;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.08);
  color: #e5e7eb;
  text-decoration: none;
  font-weight: 700;
}

/* Botón Notificaciones con estilos dinámicos */
.notification-btn {
  background: rgba(251, 191, 36, 0.1);
  border: 1px solid rgba(251, 191, 36, 0.25);
  color: #fbbf24;
}
.notification-btn:hover, .notification-btn--active {
  background: #fbbf24;
  color: #111827;
}

.badge-count {
  background: #ef4444;
  color: #ffffff;
  font-size: 0.75rem;
  font-weight: 800;
  padding: 1px 6px;
  border-radius: 999px;
  margin-left: 6px;
}

/* SECCIÓN DE NOTIFICACIONES */
.notifications-section {
  background: rgba(15, 23, 42, 0.85);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 24px;
  padding: 20px;
  margin-top: 10px;
}

.noti-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  padding-bottom: 12px;
  margin-bottom: 16px;
}

.noti-header h3 {
  margin: 0;
  color: #fbbf24;
  font-size: 1.2rem;
}

.close-noti-btn {
  background: rgba(255, 255, 255, 0.1);
  border: none;
  color: #cbd5e1;
  padding: 6px 12px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 700;
}
.close-noti-btn:hover {
  background: rgba(255, 255, 255, 0.2);
}

.noti-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 30px;
}

.mini-spinner {
  width: 24px;
  height: 24px;
  border: 3px solid rgba(251, 191, 36, 0.1);
  border-top-color: #fbbf24;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 8px;
}

.noti-empty {
  text-align: center;
  color: #94a3b8;
  padding: 30px;
}

/* Acordeón Notificaciones */
.noti-accordion {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.noti-item {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 14px;
  overflow: hidden;
  transition: border-color 0.2s;
}

.noti-item:hover {
  border-color: rgba(251, 191, 36, 0.2);
}

.noti-item--unread {
  background: rgba(251, 191, 36, 0.04);
  border-color: rgba(251, 191, 36, 0.15);
}

.noti-item__header {
  padding: 14px 18px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  cursor: pointer;
  gap: 12px;
}

.noti-item__title-box {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 0;
}

.noti-status-dot {
  width: 8px;
  height: 8px;
  background: #fbbf24;
  border-radius: 50%;
  flex-shrink: 0;
}

.noti-item__title {
  font-weight: 700;
  color: #f3f4f6;
  font-size: 0.95rem;
}

.noti-item__meta {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-shrink: 0;
}

.noti-item__date {
  font-size: 0.72rem;
  color: #94a3b8;
}

.noti-chevron {
  font-size: 0.75rem;
  color: #6b7280;
}

/* Cuerpo expandible */
.noti-item__body {
  padding: 14px 18px;
  background: rgba(0, 0, 0, 0.2);
  border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.noti-item__content {
  margin: 0 0 12px 0;
  font-size: 0.88rem;
  color: #cbd5e1;
  line-height: 1.5;
  white-space: pre-line;
}

.noti-item__actions {
  display: flex;
  gap: 10px;
}

.noti-read-toggle-btn {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #cbd5e1;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 0.75rem;
  font-weight: 700;
  cursor: pointer;
}
.noti-read-toggle-btn:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #ffffff;
}

.noti-delete-btn {
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.2);
  color: #ef4444;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 0.75rem;
  font-weight: 700;
  cursor: pointer;
}
.noti-delete-btn:hover {
  background: rgba(239, 68, 68, 0.2);
}

/* Redactor de notificaciones Admin */
.admin-noti-composer {
  background: rgba(255, 255, 255, 0.02);
  border: 1px dashed rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  padding: 16px;
  margin-bottom: 16px;
}

.toggle-composer-btn {
  background: rgba(56, 189, 248, 0.1);
  border: 1px solid rgba(56, 189, 248, 0.3);
  color: #38bdf8;
  padding: 8px 16px;
  border-radius: 10px;
  font-size: 0.8rem;
  font-weight: 700;
  cursor: pointer;
  width: 100%;
}
.toggle-composer-btn:hover {
  background: rgba(56, 189, 248, 0.2);
}

.composer-form {
  margin-top: 14px;
}

.composer-form h4 {
  margin: 0 0 12px;
  color: #38bdf8;
  font-size: 0.95rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 5px;
  margin-bottom: 12px;
}

.form-group label {
  font-size: 0.75rem;
  color: #94a3b8;
  font-weight: 700;
}

.form-group select,
.form-group input,
.form-group textarea {
  background: rgba(15, 23, 42, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #ffffff;
  padding: 8px 12px;
  border-radius: 8px;
  font-size: 0.85rem;
}
.form-group textarea {
  min-height: 70px;
  resize: vertical;
}

.send-btn {
  background: #38bdf8;
  color: #0f172a;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  font-weight: 700;
  font-size: 0.8rem;
  cursor: pointer;
}
.send-btn:hover {
  background: #0ea5e9;
}

/* Animaciones */
@keyframes spin {
  to { transform: rotate(360deg); }
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.expand-enter-active, .expand-leave-active {
  transition: max-height 0.25s ease-out, opacity 0.2s;
  max-height: 200px;
}
.expand-enter-from, .expand-leave-to {
  max-height: 0;
  opacity: 0;
  overflow: hidden;
}
</style>
