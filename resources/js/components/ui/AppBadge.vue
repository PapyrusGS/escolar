<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'neutral',
    validator: (v) => ['neutral', 'primary', 'success', 'warning', 'danger', 'info', 'admin', 'teacher', 'student'].includes(v),
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md'].includes(v),
  },
  soft: { type: Boolean, default: true },
});
</script>

<template>
  <span
    :class="[
      'app-badge',
      `app-badge--${variant}`,
      `app-badge--${size}`,
      !soft && 'app-badge--solid',
    ]"
  >
    <slot />
  </span>
</template>

<style scoped>
.app-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  border-radius: var(--radius-full);
  white-space: nowrap;
  font-size: 0.72rem;
  padding: 3px 10px;
  border: 1px solid transparent;
}

.app-badge--md {
  font-size: 0.78rem;
  padding: 4px 12px;
}

.app-badge--neutral {
  background: var(--color-surface-3);
  color: var(--color-text-secondary);
  border-color: var(--color-border-default);
}
.app-badge--primary {
  background: var(--color-primary-soft);
  color: var(--color-primary);
  border-color: var(--color-primary-border);
}
.app-badge--primary.app-badge--solid {
  background: var(--color-primary);
  color: white;
}
.app-badge--success {
  background: var(--color-success-soft);
  color: var(--color-success);
  border-color: var(--color-success-border);
}
.app-badge--success.app-badge--solid {
  background: var(--color-success);
  color: white;
}
.app-badge--warning {
  background: var(--color-warning-soft);
  color: var(--color-warning);
  border-color: var(--color-warning-border);
}
.app-badge--warning.app-badge--solid {
  background: var(--color-warning);
  color: var(--color-text-inverse);
}
.app-badge--danger {
  background: var(--color-danger-soft);
  color: var(--color-danger);
  border-color: var(--color-danger-border);
}
.app-badge--danger.app-badge--solid {
  background: var(--color-danger);
  color: white;
}
.app-badge--info {
  background: var(--color-info-soft);
  color: var(--color-info);
  border-color: var(--color-info-border);
}

.app-badge--admin {
  background: var(--color-role-admin-soft);
  color: var(--color-role-admin);
  border-color: var(--color-role-admin-border);
}
.app-badge--teacher {
  background: var(--color-role-teacher-soft);
  color: var(--color-role-teacher);
  border-color: var(--color-role-teacher-border);
}
.app-badge--student {
  background: var(--color-role-student-soft);
  color: var(--color-role-student);
  border-color: var(--color-role-student-border);
}
</style>
