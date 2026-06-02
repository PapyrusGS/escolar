<script setup>
import { Inbox } from '@lucide/vue';

defineProps({
  icon: { type: [Object, Function], default: () => Inbox },
  title: { type: String, default: 'Sin datos' },
  description: { type: String, default: '' },
});
</script>

<template>
  <div class="app-empty">
    <div class="app-empty__icon">
      <component :is="icon" :size="36" :stroke-width="1.5" />
    </div>
    <h3 class="app-empty__title">{{ title }}</h3>
    <p v-if="description" class="app-empty__desc">{{ description }}</p>
    <div v-if="$slots.default" class="app-empty__actions">
      <slot />
    </div>
  </div>
</template>

<style scoped>
.app-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 56px 24px;
  background: rgba(12, 19, 34, 0.4);
  border: 1px dashed var(--color-border-default);
  border-radius: var(--radius-2xl);
  min-height: 220px;
}

.app-empty__icon {
  display: grid;
  place-items: center;
  width: 72px;
  height: 72px;
  background: var(--color-surface-2);
  border: 1px solid var(--color-border-default);
  border-radius: 50%;
  color: var(--color-text-muted);
  margin-bottom: 16px;
}

.app-empty__title {
  margin: 0 0 6px;
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--color-text-primary);
}

.app-empty__desc {
  margin: 0 0 12px;
  max-width: 420px;
  color: var(--color-text-muted);
  font-size: 0.9rem;
  line-height: 1.5;
}

.app-empty__actions {
  margin-top: 14px;
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  justify-content: center;
}
</style>
