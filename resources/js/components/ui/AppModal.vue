<script setup>
import { computed } from 'vue';
import { X } from '@lucide/vue';

const props = defineProps({
  open: { type: Boolean, default: false },
  title: { type: String, default: '' },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg', 'xl'].includes(v),
  },
  closeOnBackdrop: { type: Boolean, default: true },
});
const emit = defineEmits(['close']);

const sizes = {
  sm: '420px',
  md: '600px',
  lg: '820px',
  xl: '1100px',
};

const maxWidth = computed(() => sizes[props.size]);

const onBackdropClick = () => {
  if (props.closeOnBackdrop) emit('close');
};
</script>

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="open" class="app-modal" role="dialog" aria-modal="true" :aria-label="title">
        <div class="app-modal__backdrop" @click="onBackdropClick"></div>
        <div class="app-modal__dialog" :style="{ maxWidth }" @click.stop>
          <header v-if="title || $slots.header" class="app-modal__header">
            <slot name="header">
              <h2 class="app-modal__title">{{ title }}</h2>
            </slot>
            <button
              type="button"
              class="app-modal__close"
              aria-label="Cerrar modal"
              @click="emit('close')"
            >
              <X :size="20" />
            </button>
          </header>
          <div class="app-modal__body">
            <slot />
          </div>
          <footer v-if="$slots.footer" class="app-modal__footer">
            <slot name="footer" />
          </footer>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.app-modal {
  position: fixed;
  inset: 0;
  z-index: 100;
  display: grid;
  place-items: center;
  padding: 16px;
}

.app-modal__backdrop {
  position: absolute;
  inset: 0;
  background: rgba(6, 9, 18, 0.78);
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
}

.app-modal__dialog {
  position: relative;
  width: 100%;
  background: linear-gradient(180deg, var(--color-surface-2) 0%, var(--color-surface-1) 100%);
  border: 1px solid var(--color-border-default);
  border-radius: var(--radius-2xl);
  box-shadow: var(--shadow-xl);
  display: flex;
  flex-direction: column;
  max-height: calc(100vh - 32px);
  overflow: hidden;
}

.app-modal__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 18px 22px;
  border-bottom: 1px solid var(--color-border-subtle);
  background: rgba(28, 39, 66, 0.3);
}

.app-modal__title {
  margin: 0;
  font-size: 1.15rem;
  font-weight: 700;
  color: var(--color-text-primary);
}

.app-modal__close {
  display: grid;
  place-items: center;
  width: 36px;
  height: 36px;
  background: transparent;
  border: 0;
  color: var(--color-text-muted);
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: all var(--duration-fast) var(--ease-out);
}

.app-modal__close:hover {
  background: var(--color-surface-3);
  color: var(--color-text-primary);
}

.app-modal__body {
  flex: 1;
  padding: 22px;
  overflow-y: auto;
}

.app-modal__footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 16px 22px;
  border-top: 1px solid var(--color-border-subtle);
  background: rgba(28, 39, 66, 0.2);
}

/* Transitions */
.modal-enter-active,
.modal-leave-active {
  transition: opacity var(--duration-base) var(--ease-out);
}
.modal-enter-active .app-modal__dialog,
.modal-leave-active .app-modal__dialog {
  transition: transform var(--duration-base) var(--ease-out),
    opacity var(--duration-base) var(--ease-out);
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
.modal-enter-from .app-modal__dialog,
.modal-leave-to .app-modal__dialog {
  opacity: 0;
  transform: scale(0.96) translateY(10px);
}
</style>
