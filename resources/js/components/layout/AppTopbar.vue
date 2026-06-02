<script setup>
import { computed } from 'vue';
import { Menu } from '@lucide/vue';
import AppAvatar from '../ui/AppAvatar.vue';
import AppRoleBadge from '../ui/AppRoleBadge.vue';

const props = defineProps({
  user: { type: Object, required: true },
  pageTitle: { type: String, default: '' },
});
const emit = defineEmits(['toggle-mobile']);

const roleMap = {
  1: { label: 'Administrador', className: 'admin' },
  2: { label: 'Docente', className: 'teacher' },
  3: { label: 'Estudiante', className: 'student' },
};
const userRole = computed(() => roleMap[props.user?.IdRol] || { label: 'Usuario', className: 'default' });

const fullName = computed(() => `${props.user?.Nombre1 || ''} ${props.user?.Apellido1 || ''}`.trim());
</script>

<template>
  <header class="app-topbar">
    <div class="app-topbar__left">
      <button
        class="app-topbar__menu-btn"
        aria-label="Abrir menú"
        @click="emit('toggle-mobile')"
      >
        <Menu :size="20" />
      </button>
      <div v-if="pageTitle" class="app-topbar__title">
        <h1>{{ pageTitle }}</h1>
      </div>
    </div>

    <div class="app-topbar__right">
      <div class="app-topbar__user">
        <AppAvatar :name="fullName" size="sm" />
        <div class="app-topbar__user-info">
          <span class="app-topbar__user-name">{{ fullName || 'Usuario' }}</span>
          <AppRoleBadge :role="userRole.className" :label="userRole.label" size="sm" />
        </div>
      </div>
    </div>
  </header>
</template>

<style scoped>
.app-topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 14px 24px;
  background: rgba(12, 19, 34, 0.65);
  backdrop-filter: blur(20px) saturate(140%);
  -webkit-backdrop-filter: blur(20px) saturate(140%);
  border-bottom: 1px solid var(--color-border-default);
  position: sticky;
  top: 0;
  z-index: 30;
  min-height: 64px;
}

.app-topbar__left {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
  flex: 1;
}

.app-topbar__menu-btn {
  display: none;
  background: transparent;
  border: 1px solid var(--color-border-default);
  color: var(--color-text-primary);
  padding: 10px;
  border-radius: var(--radius-md);
  cursor: pointer;
  min-width: 44px;
  min-height: 44px;
  align-items: center;
  justify-content: center;
}

.app-topbar__menu-btn:hover {
  background: var(--color-surface-2);
}

.app-topbar__title h1 {
  font-size: 1.15rem;
  font-weight: 700;
  color: var(--color-text-primary);
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.app-topbar__right {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-shrink: 0;
}

.app-topbar__user {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 6px 12px 6px 6px;
  background: var(--color-surface-2);
  border: 1px solid var(--color-border-default);
  border-radius: var(--radius-full);
}

.app-topbar__user-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
  min-width: 0;
}

.app-topbar__user-name {
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--color-text-primary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 140px;
}

@media (max-width: 1024px) {
  .app-topbar__menu-btn {
    display: flex;
  }
  .app-topbar__user-info {
    display: none;
  }
  .app-topbar {
    padding: 12px 16px;
  }
}
</style>
