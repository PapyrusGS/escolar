<script setup>
defineProps({
  eyebrow: { type: String, default: '' },
  title: { type: String, default: '' },
  description: { type: String, default: '' },
});
</script>

<template>
  <header class="app-page-header">
    <div class="app-page-header__text">
      <span v-if="eyebrow" class="app-page-header__eyebrow">{{ eyebrow }}</span>
      <h1 v-if="title" class="app-page-header__title">{{ title }}</h1>
      <p v-if="description || $slots.description" class="app-page-header__desc">
        <slot name="description">{{ description }}</slot>
      </p>
    </div>
    <div v-if="$slots.actions" class="app-page-header__actions">
      <slot name="actions" />
    </div>
  </header>
</template>

<style scoped>
.app-page-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 28px;
  flex-wrap: wrap;
}

.app-page-header__text {
  min-width: 0;
  flex: 1;
}

.app-page-header__eyebrow {
  display: inline-block;
  text-transform: uppercase;
  letter-spacing: 0.18em;
  font-size: 0.72rem;
  font-weight: 700;
  color: var(--color-primary);
  margin-bottom: 6px;
}

.app-page-header__title {
  margin: 0;
  font-size: 1.7rem;
  font-weight: 800;
  color: var(--color-text-primary);
  letter-spacing: -0.02em;
}

.app-page-header__desc {
  margin: 6px 0 0;
  color: var(--color-text-secondary);
  font-size: 0.95rem;
  line-height: 1.5;
  max-width: 720px;
}

.app-page-header__actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
}

@media (max-width: 640px) {
  .app-page-header__title {
    font-size: 1.35rem;
  }
}
</style>
