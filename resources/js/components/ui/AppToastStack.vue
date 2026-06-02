<script setup>
import { ref, onMounted, onUnmounted, watch, computed } from 'vue';
import AppToast from './AppToast.vue';

const toasts = ref([]);
let counter = 0;

const push = (message, variant = 'info', duration = 4000) => {
  const id = ++counter;
  toasts.value.push({ id, message, variant, duration });
};

const remove = (id) => {
  toasts.value = toasts.value.filter((t) => t.id !== id);
};

defineExpose({ push, remove });

// Escuchar eventos globales window
onMounted(() => {
  window.addEventListener('toast', onToastEvent);
});
onUnmounted(() => {
  window.removeEventListener('toast', onToastEvent);
});

const onToastEvent = (e) => {
  if (e.detail) push(e.detail.message, e.detail.variant, e.detail.duration);
};

// Helper para uso programático
const show = (message, variant = 'info', duration = 4000) => push(message, variant, duration);
</script>

<template>
  <Teleport to="body">
    <div class="app-toast-stack" aria-live="polite" aria-atomic="false">
      <TransitionGroup name="toast-stack">
        <AppToast
          v-for="t in toasts"
          :key="t.id"
          :message="t.message"
          :variant="t.variant"
          :duration="t.duration"
          @dismiss="remove(t.id)"
        />
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<style scoped>
.app-toast-stack {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 200;
  display: flex;
  flex-direction: column;
  gap: 8px;
  pointer-events: none;
}

.app-toast-stack > * {
  pointer-events: auto;
}

.toast-stack-enter-active,
.toast-stack-leave-active {
  transition: all var(--duration-base) var(--ease-out);
}
.toast-stack-enter-from {
  opacity: 0;
  transform: translateX(20px);
}
.toast-stack-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
.toast-stack-move {
  transition: transform var(--duration-base) var(--ease-out);
}

@media (max-width: 640px) {
  .app-toast-stack {
    top: 12px;
    right: 12px;
    left: 12px;
  }
  .app-toast-stack > * {
    max-width: 100%;
  }
}
</style>
