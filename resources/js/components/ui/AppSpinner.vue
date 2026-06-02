<script setup>
import { computed } from 'vue';
import { Loader2 } from '@lucide/vue';

const props = defineProps({
  size: { type: String, default: 'md' },
  label: { type: String, default: 'Cargando...' },
  fullscreen: { type: Boolean, default: false },
});

const sizes = { sm: 18, md: 28, lg: 40 };
const sizePx = computed(() => sizes[props.size] || sizes.md);
</script>

<template>
  <div v-if="fullscreen" class="app-spinner app-spinner--fullscreen" role="status">
    <Loader2 :size="sizePx" class="app-spinner__ring" />
    <span v-if="label" class="app-spinner__label">{{ label }}</span>
  </div>
  <span v-else class="app-spinner" role="status" :aria-label="label">
    <Loader2 :size="sizePx" class="app-spinner__ring" />
  </span>
</template>

<style scoped>
.app-spinner {
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.app-spinner--fullscreen {
  flex-direction: column;
  gap: 14px;
  padding: 60px 20px;
}

.app-spinner__ring {
  color: var(--color-primary);
  animation: spin 0.8s linear infinite;
}

.app-spinner__label {
  color: var(--color-text-muted);
  font-weight: 500;
  font-size: 0.9rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
