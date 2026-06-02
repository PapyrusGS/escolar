<script setup>
import { computed } from 'vue';
import { ShieldCheck, GraduationCap, UserCircle } from '@lucide/vue';
import AppBadge from './AppBadge.vue';

const props = defineProps({
  role: {
    type: String,
    default: 'default',
    validator: (v) => ['admin', 'teacher', 'student', 'default'].includes(v),
  },
  label: { type: String, default: '' },
  size: { type: String, default: 'md' },
  withIcon: { type: Boolean, default: true },
});

const icon = computed(() => {
  const map = { admin: ShieldCheck, teacher: GraduationCap, student: UserCircle, default: UserCircle };
  return map[props.role];
});
</script>

<template>
  <AppBadge :variant="role" :size="size" :class="['app-role-badge', `app-role-badge--${role}`]">
    <component v-if="withIcon" :is="icon" :size="size === 'sm' ? 12 : 14" />
    <span>{{ label }}</span>
  </AppBadge>
</template>

<style scoped>
.app-role-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-weight: 800;
  letter-spacing: 0.05em;
}

.app-role-badge--admin {
  color: #fde68a;
  background: rgba(251, 191, 36, 0.22);
  border-color: rgba(251, 191, 36, 0.7);
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.35);
}

.app-role-badge--teacher {
  color: #bae6fd;
  background: rgba(56, 189, 248, 0.22);
  border-color: rgba(56, 189, 248, 0.7);
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.35);
}

.app-role-badge--student {
  color: #e9d5ff;
  background: rgba(192, 132, 252, 0.26);
  border-color: rgba(192, 132, 252, 0.75);
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.35);
}

.app-role-badge--default {
  color: var(--color-text-secondary);
  background: var(--color-surface-3);
  border-color: var(--color-border-default);
}
</style>
