<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { X, CheckCircle2, AlertCircle, Info, AlertTriangle } from '@lucide/vue';

const props = defineProps({
  message: { type: String, required: true },
  variant: {
    type: String,
    default: 'info',
    validator: (v) => ['info', 'success', 'warning', 'danger'].includes(v),
  },
  duration: { type: Number, default: 4000 },
});

const emit = defineEmits(['dismiss']);

const visible = ref(true);
let timer = null;

const icon = {
  success: CheckCircle2,
  danger: AlertCircle,
  warning: AlertTriangle,
  info: Info,
}[props.variant];

onMounted(() => {
  if (props.duration > 0) {
    timer = setTimeout(() => {
      visible.value = false;
      setTimeout(() => emit('dismiss'), 200);
    }, props.duration);
  }
});

onUnmounted(() => {
  if (timer) clearTimeout(timer);
});
</script>

<template>
  <Transition name="toast">
    <div v-if="visible" :class="['app-toast', `app-toast--${variant}`]" role="status">
      <component :is="icon" :size="20" class="app-toast__icon" />
      <span class="app-toast__msg">{{ message }}</span>
      <button class="app-toast__close" aria-label="Cerrar" @click="visible = false; emit('dismiss')">
        <X :size="16" />
      </button>
    </div>
  </Transition>
</template>

<style scoped>
.app-toast {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 18px;
  border-radius: var(--radius-lg);
  border: 1px solid;
  background: var(--color-surface-2);
  box-shadow: var(--shadow-lg);
  min-width: 280px;
  max-width: 380px;
  font-size: 0.9rem;
  font-weight: 500;
}

.app-toast--info {
  border-color: var(--color-info-border);
  color: var(--color-info);
}
.app-toast--success {
  border-color: var(--color-success-border);
  color: var(--color-success);
}
.app-toast--warning {
  border-color: var(--color-warning-border);
  color: var(--color-warning);
}
.app-toast--danger {
  border-color: var(--color-danger-border);
  color: var(--color-danger);
}

.app-toast__icon { flex-shrink: 0; }
.app-toast__msg { flex: 1; color: var(--color-text-primary); }

.app-toast__close {
  display: grid;
  place-items: center;
  width: 28px;
  height: 28px;
  background: transparent;
  border: 0;
  color: var(--color-text-muted);
  border-radius: var(--radius-sm);
  cursor: pointer;
  flex-shrink: 0;
}

.app-toast__close:hover {
  background: var(--color-surface-3);
  color: var(--color-text-primary);
}

.toast-enter-active,
.toast-leave-active {
  transition: all var(--duration-base) var(--ease-out);
}
.toast-enter-from {
  opacity: 0;
  transform: translateX(20px);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(20px);
}
</style>
