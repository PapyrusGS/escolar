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
    <component v-if="withIcon" :is="icon" :size="size === 'sm' ? 11 : 13" />
    <span>{{ label }}</span>
  </AppBadge>
</template>

<style scoped>
.app-role-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
}
</style>
