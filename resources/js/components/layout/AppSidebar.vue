<script setup>
import { computed } from 'vue';
import {
  LayoutDashboard,
  Users,
  UserPlus,
  GraduationCap,
  BookOpen,
  ClipboardList,
  BarChart3,
  Bell,
  UserCircle,
  LogOut,
  GraduationCap as School,
  X,
  Award,
  UserCheck,
  Calendar,
} from '@lucide/vue';
import AppRoleBadge from '../ui/AppRoleBadge.vue';

const props = defineProps({
  user: { type: Object, required: true },
  collapsed: { type: Boolean, default: false },
  mobileOpen: { type: Boolean, default: false },
  unreadNotifications: { type: Number, default: 0 },
});
const emit = defineEmits(['toggle-mobile', 'logout']);

const route = computed(() => (typeof window !== 'undefined' ? window.location.pathname : '/'));

const roleMap = {
  1: { label: 'Administrador', className: 'admin' },
  2: { label: 'Docente', className: 'teacher' },
  3: { label: 'Estudiante', className: 'student' },
};

const userRole = computed(() => roleMap[Number(props.user?.IdRol)] || { label: 'Usuario', className: 'default' });

const navItems = computed(() => {
  const rol = Number(props.user?.IdRol);
  const items = [
    { to: '/dashboard', label: 'Dashboard', icon: LayoutDashboard, visible: true },
  ];

  if (rol === 1) {
    items.push(
      { to: '/usuarios', label: 'Usuarios', icon: Users, visible: true },
      { to: '/usuarios/create', label: 'Registrar usuario', icon: UserPlus, visible: true },
      { to: '/cursos', label: 'Gestionar cursos', icon: GraduationCap, visible: true },
      { to: '/cursos/visualizacion', label: 'Cursos por usuario', icon: BookOpen, visible: true },
      { to: '/reportes', label: 'Reportes', icon: BarChart3, visible: true },
    );
  }

  if (rol === 2) {
    items.push(
      { to: '/docente/cursos', label: 'Mis cursos', icon: GraduationCap, visible: true },
      { to: '/docente/notas', label: 'Gestionar notas', icon: ClipboardList, visible: true },
      { to: '/reportes', label: 'Reportes', icon: BarChart3, visible: true },
    );
  }

  if (rol === 3) {
    items.push(
      { to: '/cursos/visualizacion', label: 'Mis cursos', icon: BookOpen, visible: true },
      { to: '/cursos/inscripcion', label: 'Inscripción a materias', icon: UserCheck, visible: true },
      { to: '/mis-notas', label: 'Mis calificaciones', icon: Award, visible: true },
      { to: '/reportes', label: 'Reportes', icon: BarChart3, visible: true },
    );
  }

  items.push({
    to: '/notificaciones',
    label: 'Notificaciones',
    icon: Bell,
    visible: true,
    badge: props.unreadNotifications,
  });

  items.push({
    to: '/perfil',
    label: 'Mi perfil',
    icon: UserCircle,
    visible: true,
  });

  return items.filter((i) => i.visible);
});

const isActive = (to) => {
  if (!to || to.startsWith('#')) return false;
  return route.value === to || (to !== '/' && route.value.startsWith(to + '/'));
};

const initials = computed(() => {
  const n = props.user?.Nombre1?.[0] || '';
  const a = props.user?.Apellido1?.[0] || '';
  return (n + a).toUpperCase() || 'U';
});
</script>

<template>
  <aside
    :class="[
      'app-sidebar',
      collapsed && 'app-sidebar--collapsed',
      mobileOpen && 'app-sidebar--mobile-open',
    ]"
    aria-label="Navegación principal"
  >
    <div class="app-sidebar__brand">
      <div class="app-sidebar__logo">
        <School :size="22" :stroke-width="2.5" />
      </div>
      <div v-if="!collapsed" class="app-sidebar__brand-text">
        <span class="app-sidebar__brand-title">Sistema Universitario</span>
        <span class="app-sidebar__brand-sub">Plataforma académica</span>
      </div>
      <button
        v-if="mobileOpen"
        class="app-sidebar__close"
        aria-label="Cerrar menú"
        @click="emit('toggle-mobile', false)"
      >
        <X :size="20" />
      </button>
    </div>

    <nav class="app-sidebar__nav" aria-label="Secciones">
      <ul>
        <li v-for="item in navItems" :key="item.to">
          <a
            :href="item.to"
            :class="['app-sidebar__link', isActive(item.to) && 'app-sidebar__link--active']"
            :aria-current="isActive(item.to) ? 'page' : undefined"
          >
            <component :is="item.icon" :size="20" :stroke-width="2" />
            <span v-if="!collapsed" class="app-sidebar__link-label">{{ item.label }}</span>
            <span
              v-if="!collapsed && item.badge"
              class="app-sidebar__badge"
              :aria-label="`${item.badge} sin leer`"
            >
              {{ item.badge }}
            </span>
          </a>
        </li>
      </ul>
    </nav>

    <div class="app-sidebar__footer">
      <div v-if="!collapsed" class="app-sidebar__user">
        <div class="app-sidebar__avatar" :class="`app-sidebar__avatar--${userRole.className}`">{{ initials }}</div>
        <div class="app-sidebar__user-info">
          <span class="app-sidebar__user-name">{{ user?.Nombre1 }} {{ user?.Apellido1 }}</span>
          <AppRoleBadge :role="userRole.className" :label="userRole.label" size="sm" />
        </div>
      </div>
      <button class="app-sidebar__logout" :aria-label="'Cerrar sesión'" @click="emit('logout')">
        <LogOut :size="18" />
        <span v-if="!collapsed">Cerrar sesión</span>
      </button>
    </div>
  </aside>
</template>

<style scoped>
.app-sidebar {
  display: flex;
  flex-direction: column;
  width: 264px;
  height: 100vh;
  background: linear-gradient(180deg, var(--color-surface-1) 0%, var(--color-surface-0) 100%);
  border-right: 1px solid var(--color-border-default);
  position: sticky;
  top: 0;
  transition: width var(--duration-base) var(--ease-out);
  flex-shrink: 0;
  z-index: 40;
}

.app-sidebar--collapsed {
  width: 76px;
}

.app-sidebar__brand {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 20px 18px;
  border-bottom: 1px solid var(--color-border-subtle);
}

.app-sidebar__logo {
  display: grid;
  place-items: center;
  width: 40px;
  height: 40px;
  border-radius: var(--radius-md);
  background: linear-gradient(135deg, var(--color-primary) 0%, #8b5cf6 100%);
  color: white;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
}

.app-sidebar__brand-text {
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.app-sidebar__brand-title {
  font-weight: 700;
  font-size: 0.95rem;
  color: var(--color-text-primary);
  white-space: nowrap;
}

.app-sidebar__brand-sub {
  font-size: 0.7rem;
  color: var(--color-text-muted);
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

.app-sidebar__close {
  margin-left: auto;
  background: transparent;
  border: 0;
  color: var(--color-text-muted);
  padding: 6px;
  border-radius: var(--radius-sm);
  cursor: pointer;
}

.app-sidebar__nav {
  flex: 1;
  overflow-y: auto;
  padding: 12px 10px;
}

.app-sidebar__nav ul {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.app-sidebar__link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  border-radius: var(--radius-md);
  color: var(--color-text-secondary);
  text-decoration: none;
  font-weight: 500;
  font-size: 0.9rem;
  transition: background var(--duration-fast) var(--ease-out),
    color var(--duration-fast) var(--ease-out);
  min-height: 44px;
  position: relative;
}

.app-sidebar__link:hover {
  background: var(--color-surface-2);
  color: var(--color-text-primary);
}

.app-sidebar__link--active {
  background: var(--color-primary-soft);
  color: var(--color-text-primary);
}

.app-sidebar__link--active::before {
  content: '';
  position: absolute;
  left: 0;
  top: 8px;
  bottom: 8px;
  width: 3px;
  background: var(--color-primary);
  border-radius: 0 3px 3px 0;
}

.app-sidebar__link-label {
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.app-sidebar__badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 22px;
  height: 22px;
  padding: 0 6px;
  background: var(--color-danger);
  color: white;
  font-size: 0.7rem;
  font-weight: 700;
  border-radius: var(--radius-full);
}

.app-sidebar__footer {
  border-top: 1px solid var(--color-border-subtle);
  padding: 12px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.app-sidebar__user {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px;
  border-radius: var(--radius-md);
  background: var(--color-surface-2);
}

.app-sidebar__avatar {
  display: grid;
  place-items: center;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--color-primary) 0%, #8b5cf6 100%);
  color: white;
  font-weight: 700;
  font-size: 0.8rem;
  flex-shrink: 0;
}

.app-sidebar__avatar--admin {
  background: linear-gradient(135deg, #fbbf24 0%, #f97316 100%);
}

.app-sidebar__avatar--teacher {
  background: linear-gradient(135deg, #38bdf8 0%, #2563eb 100%);
}

.app-sidebar__avatar--student {
  background: linear-gradient(135deg, #c084fc 0%, #ec4899 100%);
}

.app-sidebar__user-info {
  display: flex;
  flex-direction: column;
  min-width: 0;
  gap: 2px;
}

.app-sidebar__user-name {
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--color-text-primary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 140px;
}

.app-sidebar__logout {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 10px;
  background: transparent;
  border: 1px solid var(--color-border-default);
  color: var(--color-text-secondary);
  border-radius: var(--radius-md);
  font-weight: 600;
  font-size: 0.85rem;
  cursor: pointer;
  transition: all var(--duration-fast) var(--ease-out);
  min-height: 44px;
}

.app-sidebar__logout:hover {
  background: var(--color-danger-soft);
  border-color: var(--color-danger-border);
  color: var(--color-danger);
}

@media (max-width: 1024px) {
  .app-sidebar {
    position: fixed;
    left: 0;
    top: 0;
    transform: translateX(-100%);
    transition: transform var(--duration-slow) var(--ease-out);
  }
  .app-sidebar--mobile-open {
    transform: translateX(0);
    box-shadow: var(--shadow-xl);
  }
}
</style>
