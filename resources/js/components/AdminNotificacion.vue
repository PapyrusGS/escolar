<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import {
  ArrowLeft,
  Bell,
  Send,
  X,
  Inbox,
  Check,
  Trash2,
  Plus,
} from '@lucide/vue';
import AppShell from './layout/AppShell.vue';
import PageTransition from './layout/PageTransition.vue';
import AppPageHeader from './ui/AppPageHeader.vue';
import AppButton from './ui/AppButton.vue';
import AppCard from './ui/AppCard.vue';
import AppBadge from './ui/AppBadge.vue';
import AppInput from './ui/AppInput.vue';
import AppSelect from './ui/AppSelect.vue';
import AppSpinner from './ui/AppSpinner.vue';
import AppEmptyState from './ui/AppEmptyState.vue';
import { toast } from '../lib/toast.js';

const props = defineProps({
  userRole: { type: Number, default: null },
});

const currentUser = ref(null);
const notifications = ref([]);
const usuarios = ref([]);
const loading = ref(false);
const currentFilter = ref('all');
const showComposer = ref(false);
const sending = ref(false);
const composerData = ref({ IdUsuario: '', Titulo: '', Contenido: '' });

const filteredNotifications = computed(() => {
  if (currentFilter.value === 'unread') return notifications.value.filter((n) => n.Estado);
  if (currentFilter.value === 'read') return notifications.value.filter((n) => !n.Estado);
  return notifications.value;
});

const counts = computed(() => ({
  all: notifications.value.length,
  unread: notifications.value.filter((n) => n.Estado).length,
  read: notifications.value.filter((n) => !n.Estado).length,
}));

const formatDateTime = (d) => {
  if (!d) return '';
  return new Date(d).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
};

const getRoleName = (id) => ({ 1: 'Admin', 2: 'Docente', 3: 'Estudiante' }[id] || 'Usuario');

onMounted(async () => {
  if (!currentUser.value) {
    const stored = localStorage.getItem('auth_user');
    if (stored) currentUser.value = JSON.parse(stored);
  }
  await loadNotifications();
  const rol = props.userRole ?? Number(currentUser.value?.IdRol);
  if (rol === 1) await loadUsuarios();
});

const handleLogout = async () => {
  try {
    if (localStorage.getItem('auth_token')) await axios.post('/api/auth/logout');
  } catch {}
  localStorage.clear();
  delete axios.defaults.headers.common.Authorization;
  window.location.href = '/';
};

const loadNotifications = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/notificaciones');
    notifications.value = data.data || [];
  } catch (err) {
    toast.error('No se pudieron cargar las notificaciones');
  } finally {
    loading.value = false;
  }
};

const loadUsuarios = async () => {
  try {
    const { data } = await axios.get('/api/usuarios');
    usuarios.value = data.data || [];
  } catch (err) {
    console.error(err);
  }
};

const toggleRead = async (noti) => {
  try {
    noti.Estado = !noti.Estado;
    await axios.patch(`/api/notificaciones/${noti.IdNotificacion}/toggle`);
  } catch (err) {
    noti.Estado = !noti.Estado;
    toast.error('No se pudo actualizar el estado');
  }
};

const deleteNotification = async (id) => {
  try {
    notifications.value = notifications.value.filter((n) => n.IdNotificacion !== id);
    await axios.delete(`/api/notificaciones/${id}`);
    toast.success('Notificación eliminada');
  } catch (err) {
    toast.error('No se pudo eliminar');
    loadNotifications();
  }
};

const sendNotification = async () => {
  if (sending.value) return;
  sending.value = true;
  try {
    await axios.post('/api/notificaciones', composerData.value);
    composerData.value = { IdUsuario: '', Titulo: '', Contenido: '' };
    showComposer.value = false;
    toast.success('Notificación enviada correctamente');
    await loadNotifications();
  } catch (err) {
    toast.error(err.response?.data?.message || 'Error al enviar la notificación');
  } finally {
    sending.value = false;
  }
};

const userOptions = computed(() =>
  usuarios.value.map((u) => ({
    Id: u.IdUsuario,
    Nombre: `[${getRoleName(u.IdRol)}] ${u.Nombre1} ${u.Apellido1} (${u.Correo})`,
  }))
);

const effectiveRole = computed(() => props.userRole ?? Number(currentUser.value?.IdRol));
</script>

<template>
  <AppShell v-if="currentUser" :user="currentUser" page-title="Notificaciones" @logout="handleLogout">
    <PageTransition>
      <div class="notif-page">
        <AppPageHeader
          title="Notificaciones"
          description="Gestiona tus avisos académicos y mensajes del sistema."
        >
          <template #actions>
            <AppButton variant="secondary" :icon="ArrowLeft" @click="window.location.href = '/dashboard'">
              Volver al Dashboard
            </AppButton>
          </template>
        </AppPageHeader>

        <AppCard padding="lg">
          <!-- Header -->
          <div class="notif__header">
            <div class="notif__title">
              <div class="notif__icon">
                <Bell :size="22" />
              </div>
              <div>
                <h2>Bandeja de notificaciones</h2>
                <p>Gestiona tus avisos académicos y del sistema</p>
              </div>
            </div>

            <div class="notif__filters">
              <button
                v-for="filter in ['all', 'unread', 'read']"
                :key="filter"
                :class="['notif__filter', currentFilter === filter && 'notif__filter--active']"
                @click="currentFilter = filter"
              >
                {{ { all: 'Todas', unread: 'Pendientes', read: 'Archivadas' }[filter] }}
                <span v-if="counts[filter] > 0" class="notif__filter-count">{{ counts[filter] }}</span>
              </button>
            </div>
          </div>

          <!-- Admin composer -->
          <div v-if="effectiveRole === 1" class="notif__composer-zone">
            <AppButton
              :variant="showComposer ? 'secondary' : 'primary'"
              :icon="showComposer ? X : Plus"
              @click="showComposer = !showComposer"
            >
              {{ showComposer ? 'Cancelar envío' : 'Redactar notificación' }}
            </AppButton>

      <Transition name="slide">
        <form v-if="showComposer" @submit.prevent="sendNotification" class="notif__composer">
          <h4>Enviar notificación directa</h4>
          <AppSelect
            v-model="composerData.IdUsuario"
            label="Usuario destino"
            :options="userOptions"
            required
          />
          <AppInput
            v-model="composerData.Titulo"
            label="Título"
            placeholder="Ej. Recordatorio de examen"
            required
          />
          <label class="notif__textarea-label">
            <span>Mensaje</span>
            <textarea
              v-model="composerData.Contenido"
              placeholder="Escribe el contenido aquí..."
              required
              rows="4"
              class="notif__textarea"
            ></textarea>
          </label>
          <AppButton type="submit" variant="primary" :icon="Send" :loading="sending">
            Enviar notificación
          </AppButton>
        </form>
      </Transition>
    </div>

    <AppSpinner v-if="loading" :fullscreen="true" label="Cargando notificaciones..." />

    <div v-else-if="filteredNotifications.length === 0" class="notif__empty">
      <AppEmptyState
        :icon="Inbox"
        title="Sin notificaciones"
        :description="currentFilter === 'unread' ? 'No tienes notificaciones pendientes.' : 'No hay notificaciones en esta categoría.'"
      />
    </div>

    <ul v-else class="notif__list">
      <li
        v-for="noti in filteredNotifications"
        :key="noti.IdNotificacion"
        :class="['notif__item', noti.Estado ? 'notif__item--unread' : 'notif__item--read']"
      >
        <button
          type="button"
          :class="['notif__check', !noti.Estado && 'notif__check--done']"
          :aria-label="noti.Estado ? 'Marcar como leída' : 'Marcar como no leída'"
          @click="toggleRead(noti)"
        >
          <Check v-if="!noti.Estado" :size="14" />
        </button>

        <div class="notif__body">
          <div class="notif__top">
            <strong>{{ noti.Titulo }}</strong>
            <span class="notif__date">{{ formatDateTime(noti.FechaEnvio) }}</span>
          </div>
          <p>{{ noti.Contenido }}</p>
        </div>

        <button
          class="notif__delete"
          aria-label="Eliminar notificación"
          @click="deleteNotification(noti.IdNotificacion)"
        >
          <Trash2 :size="16" />
        </button>
      </li>
    </ul>
        </AppCard>
      </div>
    </PageTransition>
  </AppShell>
</template>

<style scoped>
.notif-page {
  display: flex;
  flex-direction: column;
  gap: 22px;
}

.notif__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}

.notif__title {
  display: flex;
  align-items: center;
  gap: 12px;
}

.notif__icon {
  display: grid;
  place-items: center;
  width: 44px;
  height: 44px;
  background: var(--color-primary-soft);
  color: var(--color-primary);
  border-radius: var(--radius-md);
}

.notif__title h2 {
  margin: 0;
  font-size: 1.15rem;
  font-weight: 700;
}

.notif__title p {
  margin: 2px 0 0;
  font-size: 0.85rem;
  color: var(--color-text-muted);
}

.notif__filters {
  display: flex;
  gap: 6px;
  background: var(--color-surface-1);
  border: 1px solid var(--color-border-subtle);
  border-radius: var(--radius-md);
  padding: 4px;
}

.notif__filter {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 14px;
  background: transparent;
  border: 0;
  color: var(--color-text-muted);
  font-size: 0.82rem;
  font-weight: 600;
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: all var(--duration-fast) var(--ease-out);
  min-height: 36px;
}

.notif__filter:hover {
  color: var(--color-text-primary);
}

.notif__filter--active {
  background: var(--color-primary-soft);
  color: var(--color-primary);
}

.notif__filter-count {
  background: var(--color-primary);
  color: white;
  font-size: 0.65rem;
  padding: 1px 6px;
  border-radius: var(--radius-full);
  font-weight: 800;
  min-width: 18px;
  text-align: center;
}

.notif__filter--active .notif__filter-count {
  background: var(--color-primary);
}

.notif__composer-zone {
  margin-bottom: 20px;
  padding-bottom: 20px;
  border-bottom: 1px solid var(--color-border-subtle);
}

.notif__composer {
  margin-top: 14px;
  display: flex;
  flex-direction: column;
  gap: 14px;
  padding: 20px;
  background: var(--color-surface-1);
  border: 1px solid var(--color-border-subtle);
  border-radius: var(--radius-lg);
}

.notif__composer h4 {
  margin: 0;
  font-size: 0.92rem;
  font-weight: 700;
  color: var(--color-primary);
}

.notif__textarea-label {
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-weight: 600;
  font-size: 0.85rem;
  color: var(--color-text-primary);
}

.notif__textarea {
  width: 100%;
  padding: 11px 14px;
  font-family: inherit;
  font-size: 0.95rem;
  color: var(--color-text-primary);
  background: var(--color-surface-2);
  border: 1px solid var(--color-border-default);
  border-radius: var(--radius-md);
  resize: vertical;
  min-height: 100px;
  transition: all var(--duration-fast) var(--ease-out);
  box-sizing: border-box;
}

.notif__textarea:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: var(--shadow-focus);
}

.notif__list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.notif__item {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding: 14px 16px;
  background: var(--color-surface-1);
  border: 1px solid var(--color-border-subtle);
  border-radius: var(--radius-md);
  transition: all var(--duration-fast) var(--ease-out);
}

.notif__item--unread {
  background: var(--color-primary-soft);
  border-color: var(--color-primary-border);
}

.notif__item--read .notif__body strong {
  color: var(--color-text-muted);
  font-weight: 500;
}

.notif__item:hover {
  border-color: var(--color-border-default);
}

.notif__check {
  display: grid;
  place-items: center;
  width: 24px;
  height: 24px;
  background: transparent;
  border: 2px solid var(--color-border-strong);
  color: white;
  border-radius: 50%;
  cursor: pointer;
  transition: all var(--duration-fast) var(--ease-out);
  flex-shrink: 0;
  margin-top: 2px;
}

.notif__check:hover {
  border-color: var(--color-primary);
}

.notif__check--done {
  background: var(--color-success);
  border-color: var(--color-success);
}

.notif__body {
  flex: 1;
  min-width: 0;
}

.notif__top {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  gap: 8px;
  margin-bottom: 4px;
}

.notif__top strong {
  font-size: 0.92rem;
  font-weight: 700;
  color: var(--color-text-primary);
}

.notif__date {
  font-size: 0.72rem;
  color: var(--color-text-muted);
  white-space: nowrap;
}

.notif__body p {
  margin: 0;
  font-size: 0.85rem;
  color: var(--color-text-secondary);
  line-height: 1.5;
}

.notif__delete {
  display: grid;
  place-items: center;
  width: 32px;
  height: 32px;
  background: transparent;
  border: 0;
  color: var(--color-text-muted);
  border-radius: var(--radius-sm);
  cursor: pointer;
  opacity: 0.4;
  transition: all var(--duration-fast) var(--ease-out);
  flex-shrink: 0;
}

.notif__delete:hover {
  background: var(--color-danger-soft);
  color: var(--color-danger);
  opacity: 1;
}

.notif__empty {
  margin-top: 20px;
}

.slide-enter-active,
.slide-leave-active {
  transition: all var(--duration-base) var(--ease-out);
  overflow: hidden;
}
.slide-enter-from,
.slide-leave-to {
  opacity: 0;
  max-height: 0;
  transform: translateY(-8px);
}
.slide-enter-to,
.slide-leave-from {
  max-height: 500px;
}
</style>
