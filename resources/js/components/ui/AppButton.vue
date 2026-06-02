<script setup>
import { computed } from 'vue';
import { Loader2 } from '@lucide/vue';

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (v) => ['primary', 'secondary', 'ghost', 'danger', 'success', 'outline'].includes(v),
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },
  type: { type: String, default: 'button' },
  disabled: { type: Boolean, default: false },
  loading: { type: Boolean, default: false },
  block: { type: Boolean, default: false },
  icon: { type: [Object, Function], default: null },
  iconRight: { type: [Object, Function], default: null },
});

const classes = computed(() => [
  'app-btn',
  `app-btn--${props.variant}`,
  `app-btn--${props.size}`,
  props.block && 'app-btn--block',
  props.loading && 'app-btn--loading',
]);
</script>

<template>
  <button :type="type" :class="classes" :disabled="disabled || loading">
    <Loader2 v-if="loading" :size="16" class="app-btn__spinner" />
    <component v-else-if="icon" :is="icon" :size="size === 'sm' ? 16 : 18" />
    <span v-if="$slots.default" class="app-btn__label"><slot /></span>
    <component v-if="iconRight && !loading" :is="iconRight" :size="size === 'sm' ? 16 : 18" />
  </button>
</template>

<style scoped>
.app-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-family: inherit;
  font-weight: 600;
  border-radius: var(--radius-md);
  border: 1px solid transparent;
  cursor: pointer;
  transition: all var(--duration-fast) var(--ease-out);
  white-space: nowrap;
  text-decoration: none;
  user-select: none;
  min-height: 44px;
}

.app-btn:disabled,
.app-btn--loading {
  opacity: 0.55;
  cursor: not-allowed;
  pointer-events: none;
}

.app-btn--sm {
  padding: 8px 14px;
  font-size: 0.82rem;
  min-height: 36px;
  border-radius: var(--radius-sm);
}

.app-btn--md {
  padding: 10px 18px;
  font-size: 0.9rem;
}

.app-btn--lg {
  padding: 14px 24px;
  font-size: 1rem;
  min-height: 52px;
  border-radius: var(--radius-lg);
}

.app-btn--block {
  width: 100%;
}

/* Primary */
.app-btn--primary {
  background: var(--color-primary);
  color: white;
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}
.app-btn--primary:hover:not(:disabled) {
  background: var(--color-primary-hover);
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
}

/* Secondary */
.app-btn--secondary {
  background: var(--color-surface-2);
  color: var(--color-text-primary);
  border-color: var(--color-border-default);
}
.app-btn--secondary:hover:not(:disabled) {
  background: var(--color-surface-3);
  border-color: var(--color-border-strong);
}

/* Ghost */
.app-btn--ghost {
  background: transparent;
  color: var(--color-text-secondary);
}
.app-btn--ghost:hover:not(:disabled) {
  background: var(--color-surface-2);
  color: var(--color-text-primary);
}

/* Outline */
.app-btn--outline {
  background: transparent;
  color: var(--color-primary);
  border-color: var(--color-primary-border);
}
.app-btn--outline:hover:not(:disabled) {
  background: var(--color-primary-soft);
  border-color: var(--color-primary);
}

/* Danger */
.app-btn--danger {
  background: var(--color-danger);
  color: white;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}
.app-btn--danger:hover:not(:disabled) {
  background: #dc2626;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
}

/* Success */
.app-btn--success {
  background: var(--color-success);
  color: white;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}
.app-btn--success:hover:not(:disabled) {
  background: #059669;
  transform: translateY(-1px);
}

.app-btn__spinner {
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
