<script setup>
import { computed } from 'vue';
import { AlertCircle, CheckCircle2, Info, AlertTriangle, X } from '@lucide/vue';

const props = defineProps({
  variant: {
    type: String,
    default: 'info',
    validator: (v) => ['info', 'success', 'warning', 'danger', 'neutral'].includes(v),
  },
  title: { type: String, default: '' },
  dismissible: { type: Boolean, default: false },
});

const emit = defineEmits(['dismiss']);

const icon = computed(() => {
  const map = {
    info: Info,
    success: CheckCircle2,
    warning: AlertTriangle,
    danger: AlertCircle,
    neutral: Info,
  };
  return map[props.variant];
});
</script>

<template>
  <div :class="['app-alert', `app-alert--${variant}`]" role="alert">
    <component :is="icon" :size="20" class="app-alert__icon" />
    <div class="app-alert__content">
      <strong v-if="title" class="app-alert__title">{{ title }}</strong>
      <div class="app-alert__body"><slot /></div>
    </div>
    <button
      v-if="dismissible"
      type="button"
      class="app-alert__close"
      aria-label="Cerrar"
      @click="emit('dismiss')"
    >
      <X :size="16" />
    </button>
  </div>
</template>

<style scoped>
.app-alert {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 14px 18px;
  border-radius: var(--radius-lg);
  border: 1px solid;
  font-size: 0.9rem;
  line-height: 1.5;
}

.app-alert__icon {
  flex-shrink: 0;
  margin-top: 1px;
}

.app-alert__content {
  flex: 1;
  min-width: 0;
}

.app-alert__title {
  display: block;
  font-weight: 700;
  margin-bottom: 2px;
}

.app-alert__body {
  font-weight: 500;
}

.app-alert__close {
  display: grid;
  place-items: center;
  width: 28px;
  height: 28px;
  background: transparent;
  border: 0;
  color: inherit;
  border-radius: var(--radius-sm);
  cursor: pointer;
  flex-shrink: 0;
  opacity: 0.7;
  transition: opacity var(--duration-fast) var(--ease-out);
}

.app-alert__close:hover {
  opacity: 1;
  background: rgba(0, 0, 0, 0.1);
}

.app-alert--info {
  background: var(--color-info-soft);
  border-color: var(--color-info-border);
  color: var(--color-info);
}

.app-alert--success {
  background: var(--color-success-soft);
  border-color: var(--color-success-border);
  color: var(--color-success);
}

.app-alert--warning {
  background: var(--color-warning-soft);
  border-color: var(--color-warning-border);
  color: var(--color-warning);
}

.app-alert--danger {
  background: var(--color-danger-soft);
  border-color: var(--color-danger-border);
  color: var(--color-danger);
}

.app-alert--neutral {
  background: var(--color-surface-2);
  border-color: var(--color-border-default);
  color: var(--color-text-primary);
}
</style>
