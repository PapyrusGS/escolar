<template>
  <div class="notifications-todo">
    <div class="todo-header">
      <div class="todo-header__title">
        <span class="todo-icon">🔔</span>
        <h2>Bandeja de Notificaciones</h2>
      </div>
      <div class="todo-filters">
        <button 
          v-for="filter in ['all', 'unread', 'read']" 
          :key="filter"
          class="filter-btn"
          :class="{ active: currentFilter === filter }"
          @click="currentFilter = filter"
        >
          {{ filterLabel(filter) }}
          <span class="filter-count" v-if="getCount(filter) > 0">
            {{ getCount(filter) }}
          </span>
        </button>
      </div>
    </div>

    <!-- Nueva Notificación (Solo Admin visible como herramienta rápida) -->
    <div v-if="userRole === 1" class="admin-composer">
      <button class="toggle-composer-btn" @click="showComposer = !showComposer">
        {{ showComposer ? '✕ Cerrar Redactor' : '➕ Enviar Nueva Notificación' }}
      </button>
      
      <transition name="slide">
        <form v-if="showComposer" @submit.prevent="sendNotification" class="composer-form">
          <h4>Enviar Notificación Directa</h4>
          <div class="form-group">
            <label>Usuario Destino:</label>
            <select v-model="composerData.IdUsuario" required>
              <option value="" disabled>Seleccione un usuario...</option>
              <option v-for="u in usuarios" :key="u.IdUsuario" :value="u.IdUsuario">
                [{{ getRoleName(u.IdRol) }}] {{ u.Nombre1 }} {{ u.Apellido1 }} ({{ u.Correo }})
              </option>
            </select>
          </div>
          <div class="form-group">
            <label>Título:</label>
            <input type="text" v-model="composerData.Titulo" placeholder="Ej. Recordatorio de Pago" required />
          </div>
          <div class="form-group">
            <label>Mensaje:</label>
            <textarea v-model="composerData.Contenido" placeholder="Escribe el contenido aquí..." required></textarea>
          </div>
          <button type="submit" class="send-btn" :disabled="sending">
            {{ sending ? 'Enviando...' : 'Enviar Notificación' }}
          </button>
        </form>
      </transition>
    </div>

    <div v-if="loading" class="todo-loading">
      <div class="mini-spinner"></div>
      <p>Buscando tus notificaciones...</p>
    </div>

    <div v-else>
      <div v-if="filteredNotifications.length === 0" class="todo-empty">
        <div class="empty-icon">📭</div>
        <p>No tienes notificaciones en esta categoría.</p>
      </div>

      <transition-group name="list" tag="ul" class="todo-list" v-else>
        <li 
          v-for="noti in filteredNotifications" 
          :key="noti.IdNotificacion" 
          class="todo-item"
          :class="{ 'todo-item--read': !noti.Estado }"
        >
          <!-- Círculo de check interactivo -->
          <div class="todo-item__check" @click="toggleRead(noti)">
            <div class="check-circle" :class="{ checked: !noti.Estado }">
              <span v-if="!noti.Estado">✓</span>
            </div>
          </div>

          <!-- Contenido de la notificación -->
          <div class="todo-item__content">
            <div class="todo-item__header">
              <span class="todo-item__title">{{ noti.Titulo }}</span>
              <span class="todo-item__date">{{ formatDateTime(noti.FechaEnvio) }}</span>
            </div>
            <p class="todo-item__desc">{{ noti.Contenido }}</p>
          </div>

          <!-- Acciones rápidas (Eliminar) -->
          <div class="todo-item__actions">
            <button class="delete-btn" @click="deleteNotification(noti.IdNotificacion)" title="Eliminar Notificación">
              🗑️
            </button>
          </div>
        </li>
      </transition-group>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminNotificacion',
  props: {
    userRole: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      notifications: [],
      usuarios: [],
      loading: false,
      currentFilter: 'all',
      showComposer: false,
      sending: false,
      composerData: {
        IdUsuario: '',
        Titulo: '',
        Contenido: ''
      }
    };
  },
  computed: {
    filteredNotifications() {
      if (this.currentFilter === 'unread') {
        return this.notifications.filter(n => n.Estado); // Estado = true es no leída
      }
      if (this.currentFilter === 'read') {
        return this.notifications.filter(n => !n.Estado); // Estado = false es leída
      }
      return this.notifications;
    }
  },
  mounted() {
    this.loadNotifications();
    if (this.userRole === 1) {
      this.loadUsuarios();
    }
  },
  methods: {
    async loadNotifications() {
      this.loading = true;
      try {
        const { data } = await axios.get('/api/notificaciones');
        this.notifications = data.data || [];
      } catch (error) {
        console.error('Error al cargar notificaciones:', error);
      } finally {
        this.loading = false;
      }
    },
    async loadUsuarios() {
      try {
        const { data } = await axios.get('/api/usuarios');
        this.usuarios = data.data || [];
      } catch (error) {
        console.error('Error al cargar usuarios:', error);
      }
    },
    async toggleRead(noti) {
      try {
        const originalStatus = noti.Estado;
        noti.Estado = !noti.Estado; // optimista
        
        await axios.patch(`/api/notificaciones/${noti.IdNotificacion}/toggle`);
      } catch (error) {
        console.error('Error al alternar estado:', error);
        this.loadNotifications(); // revertir
      }
    },
    async deleteNotification(id) {
      try {
        this.notifications = this.notifications.filter(n => n.IdNotificacion !== id); // optimista
        await axios.delete(`/api/notificaciones/${id}`);
      } catch (error) {
        console.error('Error al eliminar notificación:', error);
        this.loadNotifications();
      }
    },
    async sendNotification() {
      this.sending = true;
      try {
        await axios.post('/api/notificaciones', this.composerData);
        this.composerData.Titulo = '';
        this.composerData.Contenido = '';
        this.showComposer = false;
        alert('¡Notificación enviada con éxito!');
        this.loadNotifications(); // Si se la envió a sí mismo, la verá
      } catch (error) {
        console.error('Error al enviar notificación:', error);
        alert('Error al enviar la notificación. Verifica los datos.');
      } finally {
        this.sending = false;
      }
    },
    filterLabel(filter) {
      const labels = {
        all: 'Todas',
        unread: 'Pendientes',
        read: 'Archivadas'
      };
      return labels[filter] || filter;
    },
    getCount(filter) {
      if (filter === 'unread') {
        return this.notifications.filter(n => n.Estado).length;
      }
      if (filter === 'read') {
        return this.notifications.filter(n => !n.Estado).length;
      }
      return this.notifications.length;
    },
    getRoleName(idRol) {
      const roles = {
        1: 'Admin',
        2: 'Docente',
        3: 'Estudiante'
      };
      return roles[idRol] || 'Usuario';
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
.notifications-todo {
  background: rgba(15, 23, 42, 0.95);
  border: 1px solid rgba(148, 163, 184, 0.15);
  border-radius: 24px;
  padding: 24px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.todo-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid rgba(148, 163, 184, 0.12);
  padding-bottom: 18px;
  margin-bottom: 20px;
  flex-wrap: wrap;
  gap: 16px;
}

.todo-header__title {
  display: flex;
  align-items: center;
  gap: 12px;
}

.todo-header__title h2 {
  margin: 0;
  font-size: 1.4rem;
  color: #fbbf24;
}

.todo-icon {
  font-size: 1.6rem;
}

.todo-filters {
  display: flex;
  gap: 8px;
}

.filter-btn {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(148, 163, 184, 0.12);
  color: #cbd5e1;
  padding: 8px 16px;
  border-radius: 12px;
  font-size: 0.85rem;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s;
}

.filter-btn:hover {
  background: rgba(251, 191, 36, 0.05);
  border-color: rgba(251, 191, 36, 0.3);
}

.filter-btn.active {
  background: rgba(251, 191, 36, 0.12);
  border-color: #fbbf24;
  color: #fbbf24;
}

.filter-count {
  background: rgba(251, 191, 36, 0.2);
  color: #fbbf24;
  font-size: 0.72rem;
  padding: 2px 6px;
  border-radius: 999px;
  font-weight: 800;
}

/* Admin Composer */
.admin-composer {
  margin-bottom: 24px;
  border-bottom: 1px solid rgba(148, 163, 184, 0.1);
  padding-bottom: 20px;
}

.toggle-composer-btn {
  background: rgba(56, 189, 248, 0.08);
  border: 1px dashed rgba(56, 189, 248, 0.35);
  color: #38bdf8;
  padding: 10px 18px;
  border-radius: 14px;
  font-weight: 700;
  font-size: 0.88rem;
  cursor: pointer;
  width: 100%;
  transition: all 0.2s;
  text-align: center;
}

.toggle-composer-btn:hover {
  background: rgba(56, 189, 248, 0.15);
  border-color: #38bdf8;
}

.composer-form {
  background: rgba(30, 41, 59, 0.4);
  border: 1px solid rgba(148, 163, 184, 0.12);
  border-radius: 16px;
  padding: 20px;
  margin-top: 12px;
}

.composer-form h4 {
  margin: 0 0 16px;
  color: #38bdf8;
  font-size: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 14px;
}

.form-group label {
  font-size: 0.8rem;
  font-weight: 700;
  color: #94a3b8;
}

.form-group select,
.form-group input,
.form-group textarea {
  background: rgba(15, 23, 42, 0.6);
  border: 1px solid rgba(148, 163, 184, 0.2);
  color: #f8fafc;
  padding: 10px 14px;
  border-radius: 10px;
  font-size: 0.88rem;
  transition: border-color 0.2s;
}

.form-group select:focus,
.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #38bdf8;
}

.form-group textarea {
  min-height: 80px;
  resize: vertical;
}

.send-btn {
  background: #38bdf8;
  color: #0f172a;
  border: none;
  padding: 10px 20px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 0.88rem;
  cursor: pointer;
  transition: all 0.2s;
}

.send-btn:hover:not(:disabled) {
  background: #0ea5e9;
}

.send-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Loading and Empty States */
.todo-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 40px;
}

.mini-spinner {
  width: 24px;
  height: 24px;
  border: 3px solid rgba(251, 191, 36, 0.1);
  border-top-color: #fbbf24;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 12px;
}

.todo-empty {
  text-align: center;
  padding: 48px;
  color: #94a3b8;
}

.empty-icon {
  font-size: 2.8rem;
  margin-bottom: 12px;
}

/* Todo List and Items */
.todo-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.todo-item {
  display: flex;
  align-items: flex-start;
  background: rgba(30, 41, 59, 0.2);
  border: 1px solid rgba(148, 163, 184, 0.08);
  border-radius: 16px;
  padding: 16px;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.todo-item:hover {
  background: rgba(30, 41, 59, 0.4);
  border-color: rgba(251, 191, 36, 0.15);
  transform: translateY(-1px);
}

.todo-item--read {
  opacity: 0.65;
  background: rgba(15, 23, 42, 0.1);
}

.todo-item--read .todo-item__title {
  text-decoration: line-through;
  color: #94a3b8;
}

.todo-item__check {
  padding-right: 16px;
  cursor: pointer;
  display: flex;
  align-self: center;
}

.check-circle {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  border: 2px solid rgba(148, 163, 184, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  color: #fbbf24;
  font-weight: 900;
  transition: all 0.2s;
}

.check-circle.checked {
  border-color: #34d399;
  background: rgba(52, 211, 153, 0.15);
  color: #34d399;
}

.check-circle:hover {
  border-color: #fbbf24;
}

.todo-item__content {
  flex: 1;
  min-width: 0;
}

.todo-item__header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 4px;
}

.todo-item__title {
  font-weight: 700;
  color: #f8fafc;
  font-size: 0.95rem;
}

.todo-item__date {
  font-size: 0.72rem;
  color: #64748b;
  font-weight: 600;
  white-space: nowrap;
}

.todo-item__desc {
  margin: 0;
  font-size: 0.85rem;
  color: #cbd5e1;
  line-height: 1.5;
}

.todo-item__actions {
  padding-left: 16px;
  display: flex;
  align-self: center;
}

.delete-btn {
  background: transparent;
  border: none;
  cursor: pointer;
  font-size: 1rem;
  padding: 6px;
  border-radius: 8px;
  transition: background 0.2s;
  opacity: 0.4;
}

.delete-btn:hover {
  background: rgba(239, 68, 68, 0.12);
  opacity: 1;
}

/* Animations */
@keyframes spin {
  to { transform: rotate(360deg); }
}

/* List Transitions */
.list-enter-active,
.list-leave-active {
  transition: all 0.3s ease;
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>
