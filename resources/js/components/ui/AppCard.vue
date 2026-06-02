<script setup>
defineProps({
  elevated: { type: Boolean, default: false },
  padding: { type: String, default: 'md' },
  interactive: { type: Boolean, default: false },
});
</script>

<template>
  <div
    :class="[
      'app-card',
      elevated && 'app-card--elevated',
      `app-card--pad-${padding}`,
      interactive && 'app-card--interactive',
    ]"
  >
    <header v-if="$slots.header || $slots.title" class="app-card__header">
      <slot name="header">
        <h3 class="app-card__title"><slot name="title" /></h3>
      </slot>
      <div v-if="$slots.actions" class="app-card__actions">
        <slot name="actions" />
      </div>
    </header>
    <div class="app-card__body">
      <slot />
    </div>
    <footer v-if="$slots.footer" class="app-card__footer">
      <slot name="footer" />
    </footer>
  </div>
</template>

<style scoped>
.app-card {
  background: linear-gradient(180deg, rgba(28, 39, 66, 0.55) 0%, rgba(19, 28, 48, 0.65) 100%);
  border: 1px solid var(--color-border-default);
  border-radius: var(--radius-2xl);
  box-shadow: var(--shadow-sm);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  transition: transform var(--duration-base) var(--ease-out),
    border-color var(--duration-base) var(--ease-out),
    box-shadow var(--duration-base) var(--ease-out);
}

.app-card--elevated {
  background: linear-gradient(180deg, rgba(40, 52, 80, 0.65) 0%, rgba(28, 39, 66, 0.75) 100%);
  box-shadow: var(--shadow-md);
}

.app-card--interactive {
  cursor: pointer;
}

.app-card--interactive:hover {
  transform: translateY(-3px);
  border-color: var(--color-primary-border);
  box-shadow: var(--shadow-lg);
}

.app-card__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 18px 22px;
  border-bottom: 1px solid var(--color-border-subtle);
  background: rgba(28, 39, 66, 0.3);
}

.app-card__title {
  margin: 0;
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--color-text-primary);
}

.app-card__actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.app-card__body {
  flex: 1;
  padding: 22px;
}

.app-card--pad-none .app-card__body {
  padding: 0;
}

.app-card--pad-sm .app-card__body {
  padding: 14px;
}

.app-card--pad-lg .app-card__body {
  padding: 28px;
}

.app-card__footer {
  padding: 14px 22px;
  border-top: 1px solid var(--color-border-subtle);
  background: rgba(28, 39, 66, 0.2);
}
</style>
