<script setup>
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';
import {
  Users,
  Shield,
  BarChart3,
  Settings,
  BookOpen,
  ClipboardList,
  Award,
  Calendar,
  GraduationCap,
  ArrowRight,
  Sparkles,
} from '@lucide/vue';
import AppShell from './layout/AppShell.vue';
import PageTransition from './layout/PageTransition.vue';
import AppCard from './ui/AppCard.vue';
import AppRoleBadge from './ui/AppRoleBadge.vue';
import AppBadge from './ui/AppBadge.vue';
import AppEmptyState from './ui/AppEmptyState.vue';
import { toast } from '../lib/toast.js';

const user = ref(null);
const notifications = ref([]);
const showNotifications = ref(false);

const unreadCount = computed(() => notifications.value.filter((n) => n.Estado).length);

const roleMap = {
  1: {
    label: 'Administrador',
    className: 'admin',
    welcome: 'Tienes acceso completo al sistema y sus módulos de gestión.',
    gradient: 'gradient-role-admin',
  },
  2: {
    label: 'Docente',
    className: 'teacher',
    welcome: 'Gestiona tus cursos, materias y calificaciones asignadas.',
    gradient: 'gradient-role-teacher',
  },
  3: {
    label: 'Estudiante',
    className: 'student',
    welcome: 'Consulta tus materias, avances y notificaciones personales.',
    gradient: 'gradient-role-student',
  },
};

const currentRole = computed(() => roleMap[user.value?.IdRol] || {
  label: 'Usuario',
  className: 'default',
  welcome: 'Acceso general al sistema.',
  gradient: 'gradient-primary',
});

const quickActions = computed(() => {
  const rol = user.value?.IdRol;
  const items = [];

  if (rol === 1) {
    items.push({ to: '/usuarios', label: 'Gestionar usuarios', icon: Users, variant: 'primary' });
    items.push({ to: '/cursos', label: 'Gestionar cursos', icon: GraduationCap, variant: 'primary' });
    items.push({ to: '/reportes', label: 'Ver reportes', icon: BarChart3, variant: 'primary' });
  } else if (rol === 2) {
    items.push({ to: '/docente/cursos', label: 'Mis cursos', icon: BookOpen, variant: 'primary' });
    items.push({ to: '/docente/notas', label: 'Gestionar notas', icon: ClipboardList, variant: 'primary' });
  } else if (rol === 3) {
    items.push({ to: '/cursos/visualizacion', label: 'Mis cursos', icon: BookOpen, variant: 'primary' });
    items.push({ to: '/perfil', label: 'Mi perfil', icon: Award, variant: 'primary' });
  }

  return items;
});

const featureCards = computed(() => {
  const rol = user.value?.IdRol;
  if (rol === 1) {
    return [
      { title: 'Usuarios', description: 'Crear, editar, activar y desactivar cuentas.', icon: Users, color: 'primary' },
      { title: 'Reportes', description: 'Visualizar estadísticas y control general.', icon: BarChart3, color: 'info' },
      { title: 'Roles y permisos', description: 'Administrar accesos por perfil de usuario.', icon: Shield, color: 'admin' },
      { title: 'Configuración', description: 'Ajustes globales del sistema.', icon: Settings, color: 'neutral' },
    ];
  }
  if (rol === 2) {
    return [
      { title: 'Mis cursos', description: 'Ver las clases asignadas en el periodo actual.', icon: BookOpen, color: 'teacher' },
      { title: 'Registro de notas', description: 'Cargar y actualizar calificaciones.', icon: ClipboardList, color: 'teacher' },
      { title: 'Estudiantes', description: 'Consultar listas e información de inscritos.', icon: Users, color: 'teacher' },
      { title: 'Horario', description: 'Revisar tus horarios y turnos.', icon: Calendar, color: 'info' },
    ];
  }
  if (rol === 3) {
    return [
      { title: 'Mis inscripciones', description: 'Ver materias y cursos en los que estás inscrito.', icon: BookOpen, color: 'student' },
      { title: 'Mis notas', description: 'Consultar tus calificaciones registradas.', icon: Award, color: 'student' },
      { title: 'Horario', description: 'Revisar tus horarios y turnos.', icon: Calendar, color: 'student' },
      { title: 'Notificaciones', description: 'Leer mensajes importantes del sistema.', icon: Sparkles, color: 'info' },
    ];
  }
  return [];
});

onMounted(async () => {
  const token = localStorage.getItem('auth_token');
  if (!token) {
    window.location.href = '/';
    return;
  }
  axios.defaults.headers.common.Authorization = `Bearer ${token}`;

  try {
    const stored = localStorage.getItem('auth_user');
    if (stored) user.value = JSON.parse(stored);

    const { data } = await axios.get('/api/auth/perfil');
    user.value = data.data.user;
    localStorage.setItem('auth_user', JSON.stringify(user.value));

    await fetchNotifications();
  } catch (err) {
    console.error(err);
    localStorage.removeItem('auth_token');
    localStorage.removeItem('auth_user');
    delete axios.defaults.headers.common.Authorization;
    window.location.href = '/';
  }
});

const fetchNotifications = async () => {
  try {
    const { data } = await axios.get('/api/notificaciones');
    notifications.value = data.data || [];
  } catch (err) {
    console.error('Error cargando notificaciones:', err);
  }
};

const handleLogout = async () => {
  try {
    if (localStorage.getItem('auth_token')) {
      await axios.post('/api/auth/logout');
    }
  } catch (err) {
    console.error(err);
  } finally {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('auth_user');
    delete axios.defaults.headers.common.Authorization;
    window.location.href = '/';
  }
};

const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value;
};

const markAllAsRead = async () => {
  try {
    await Promise.all(
      notifications.value
        .filter((n) => n.Estado)
        .map((n) => axios.patch(`/api/notificaciones/${n.IdNotificacion}/toggle`))
    );
    notifications.value.forEach((n) => (n.Estado = false));
    toast.success('Notificaciones marcadas como leídas');
  } catch (err) {
    toast.error('No se pudieron actualizar las notificaciones');
  }
};

const formatDateTime = (d) => {
  if (!d) return '';
  return new Date(d).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
  <AppShell
    v-if="user"
    :user="user"
    :unread-notifications="unreadCount"
    @logout="handleLogout"
    @open-notifications="toggleNotifications"
  >
    <PageTransition>
      <div class="index">
        <!-- Hero / Welcome banner -->
        <section :class="['index__hero', currentRole.gradient]">
          <div class="index__hero-content">
            <div class="index__hero-meta">
              <AppRoleBadge :role="currentRole.className" :label="currentRole.label" />
              <span class="index__hero-greeting">
                <Sparkles :size="14" />
                {{ new Date().toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long' }) }}
              </span>
            </div>
            <h1 class="index__hero-title">
              ¡Hola, {{ user.Nombre1 }}!
            </h1>
            <p class="index__hero-sub">{{ currentRole.welcome }}</p>

            <div class="index__hero-actions">
              <a v-for="action in quickActions" :key="action.to" :href="action.to" class="index__hero-btn">
                <component :is="action.icon" :size="18" />
                {{ action.label }}
                <ArrowRight :size="16" />
              </a>
            </div>
          </div>
          <div class="index__hero-glow"></div>
        </section>

        <!-- Notificaciones inline -->
        <section v-if="showNotifications" class="index__notif">
          <div class="index__notif-head">
            <h3>Bandeja de Notificaciones</h3>
            <div class="index__notif-actions">
              <button v-if="unreadCount > 0" class="index__notif-btn" @click="markAllAsRead">
                Marcar todas como leídas
              </button>
              <button class="index__notif-btn index__notif-btn--ghost" @click="showNotifications = false">
                Cerrar
              </button>
            </div>
          </div>

          <div v-if="notifications.length === 0">
            <AppEmptyState title="Sin notificaciones" description="No tienes notificaciones en este momento." />
          </div>

          <ul v-else class="index__notif-list">
            <li
              v-for="noti in notifications"
              :key="noti.IdNotificacion"
              :class="['index__notif-item', noti.Estado && 'index__notif-item--unread']"
            >
              <div class="index__notif-body">
                <div class="index__notif-top">
                  <strong>{{ noti.Titulo }}</strong>
                  <span class="index__notif-date">{{ formatDateTime(noti.FechaEnvio) }}</span>
                </div>
                <p>{{ noti.Contenido }}</p>
              </div>
              <AppBadge v-if="noti.Estado" variant="primary" size="sm">Nueva</AppBadge>
            </li>
          </ul>
        </section>

        <!-- Feature cards -->
        <section v-if="featureCards.length" class="index__grid">
          <AppCard
            v-for="(card, i) in featureCards"
            :key="card.title"
            interactive
            class="index__feature-card"
            :style="{ animationDelay: `${i * 50}ms` }"
          >
            <div :class="['index__feature-icon', `index__feature-icon--${card.color}`]">
              <component :is="card.icon" :size="22" :stroke-width="2" />
            </div>
            <h3>{{ card.title }}</h3>
            <p>{{ card.description }}</p>
          </AppCard>
        </section>
      </div>
    </PageTransition>
  </AppShell>
</template>

<style scoped>
.index {
  display: flex;
  flex-direction: column;
  gap: 28px;
}

.index__hero {
  position: relative;
  padding: 36px 40px;
  border-radius: var(--radius-2xl);
  overflow: hidden;
  color: white;
  box-shadow: var(--shadow-lg);
}

.index__hero-content {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.index__hero-meta {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.index__hero-greeting {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.78rem;
  font-weight: 500;
  text-transform: capitalize;
  color: rgba(255, 255, 255, 0.85);
}

.index__hero-title {
  margin: 0;
  font-size: 2.1rem;
  font-weight: 800;
  letter-spacing: -0.02em;
  color: white;
}

.index__hero-sub {
  margin: 0;
  font-size: 1rem;
  color: rgba(255, 255, 255, 0.92);
  max-width: 640px;
}

.index__hero-actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  margin-top: 8px;
}

.index__hero-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  background: rgba(255, 255, 255, 0.18);
  border: 1px solid rgba(255, 255, 255, 0.28);
  color: white;
  border-radius: var(--radius-md);
  font-weight: 600;
  font-size: 0.88rem;
  text-decoration: none;
  transition: all var(--duration-fast) var(--ease-out);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}

.index__hero-btn:hover {
  background: rgba(255, 255, 255, 0.28);
  transform: translateY(-1px);
}

.index__hero-glow {
  position: absolute;
  top: -50%;
  right: -10%;
  width: 60%;
  height: 200%;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.18) 0%, transparent 60%);
  pointer-events: none;
}

.index__notif {
  background: var(--color-surface-2);
  border: 1px solid var(--color-border-default);
  border-radius: var(--radius-2xl);
  padding: 24px;
  box-shadow: var(--shadow-sm);
}

.index__notif-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 16px;
  padding-bottom: 16px;
  border-bottom: 1px solid var(--color-border-subtle);
  flex-wrap: wrap;
}

.index__notif-head h3 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 700;
}

.index__notif-actions {
  display: flex;
  gap: 8px;
}

.index__notif-btn {
  padding: 8px 14px;
  background: var(--color-primary-soft);
  border: 1px solid var(--color-primary-border);
  color: var(--color-primary);
  border-radius: var(--radius-sm);
  font-size: 0.82rem;
  font-weight: 600;
  cursor: pointer;
  transition: all var(--duration-fast) var(--ease-out);
  min-height: 36px;
}

.index__notif-btn:hover {
  background: var(--color-primary);
  color: white;
}

.index__notif-btn--ghost {
  background: transparent;
  border-color: var(--color-border-default);
  color: var(--color-text-muted);
}

.index__notif-btn--ghost:hover {
  background: var(--color-surface-3);
  color: var(--color-text-primary);
}

.index__notif-list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.index__notif-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 14px 16px;
  background: var(--color-surface-1);
  border: 1px solid var(--color-border-subtle);
  border-radius: var(--radius-md);
  transition: all var(--duration-fast) var(--ease-out);
}

.index__notif-item--unread {
  border-color: var(--color-primary-border);
  background: var(--color-primary-soft);
}

.index__notif-item:hover {
  border-color: var(--color-border-default);
}

.index__notif-body {
  flex: 1;
  min-width: 0;
}

.index__notif-top {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  gap: 8px;
  margin-bottom: 4px;
}

.index__notif-top strong {
  font-size: 0.92rem;
  color: var(--color-text-primary);
}

.index__notif-date {
  font-size: 0.72rem;
  color: var(--color-text-muted);
  white-space: nowrap;
}

.index__notif-item p {
  margin: 0;
  font-size: 0.85rem;
  color: var(--color-text-secondary);
  line-height: 1.5;
}

.index__grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 16px;
}

.index__feature-card {
  animation: fadeIn 0.4s var(--ease-out) backwards;
}

.index__feature-icon {
  display: grid;
  place-items: center;
  width: 48px;
  height: 48px;
  border-radius: var(--radius-md);
  background: var(--color-primary-soft);
  color: var(--color-primary);
  margin-bottom: 12px;
}

.index__feature-icon--primary { background: var(--color-primary-soft); color: var(--color-primary); }
.index__feature-icon--info { background: var(--color-info-soft); color: var(--color-info); }
.index__feature-icon--admin { background: var(--color-role-admin-soft); color: var(--color-role-admin); }
.index__feature-icon--teacher { background: var(--color-role-teacher-soft); color: var(--color-role-teacher); }
.index__feature-icon--student { background: var(--color-role-student-soft); color: var(--color-role-student); }
.index__feature-icon--neutral { background: var(--color-surface-3); color: var(--color-text-secondary); }

.index__feature-card h3 {
  margin: 0 0 6px;
  font-size: 1.05rem;
  font-weight: 700;
}

.index__feature-card p {
  margin: 0;
  font-size: 0.88rem;
  color: var(--color-text-secondary);
  line-height: 1.5;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 768px) {
  .index__hero { padding: 24px 20px; }
  .index__hero-title { font-size: 1.6rem; }
}
</style>
