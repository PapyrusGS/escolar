<script setup>
defineProps({
  modelValue: { type: [String, Number, null], default: '' },
  label: { type: String, default: '' },
  options: { type: Array, required: true },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  error: { type: String, default: '' },
  hint: { type: String, default: '' },
  placeholder: { type: String, default: 'Seleccionar...' },
  id: { type: String, default: () => `sel-${Math.random().toString(36).slice(2, 9)}` },
  valueKey: { type: String, default: 'Id' },
  labelKey: { type: String, default: 'Nombre' },
  size: { type: String, default: 'md' },
});

defineEmits(['update:modelValue']);
</script>

<template>
  <label :for="id" :class="['app-select', `app-select--${size}`, error && 'app-select--error']">
    <span v-if="label" class="app-select__label">
      {{ label }}
      <span v-if="required" class="app-select__required" aria-hidden="true">*</span>
    </span>
    <div class="app-select__wrap">
      <select
        :id="id"
        :value="modelValue"
        :required="required"
        :disabled="disabled"
        class="app-select__field"
        @change="$emit('update:modelValue', $event.target.value)"
      >
        <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
        <option
          v-for="opt in options"
          :key="opt[valueKey]"
          :value="opt[valueKey]"
        >
          {{ opt[labelKey] }}
        </option>
      </select>
      <span class="app-select__chevron" aria-hidden="true">▾</span>
    </div>
    <span v-if="error || hint" class="app-select__msg" :class="error && 'app-select__msg--error'">
      {{ error || hint }}
    </span>
  </label>
</template>

<style scoped>
.app-select {
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-weight: 600;
  font-size: 0.85rem;
  color: var(--color-text-primary);
}

.app-select__label {
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.app-select__required {
  color: var(--color-danger);
  font-weight: 700;
}

.app-select__wrap {
  position: relative;
}

.app-select__field {
  width: 100%;
  padding: 11px 38px 11px 14px;
  font-family: inherit;
  font-size: 0.95rem;
  font-weight: 400;
  color: var(--color-text-primary);
  background: var(--color-surface-2);
  border: 1px solid var(--color-border-default);
  border-radius: var(--radius-md);
  transition: all var(--duration-fast) var(--ease-out);
  min-height: 44px;
  box-sizing: border-box;
  appearance: none;
  -webkit-appearance: none;
  cursor: pointer;
}

.app-select__field:hover:not(:disabled) {
  border-color: var(--color-border-strong);
}

.app-select__field:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: var(--shadow-focus);
  background: var(--color-surface-1);
}

.app-select__field:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.app-select--error .app-select__field {
  border-color: var(--color-danger);
}

.app-select__chevron {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--color-text-muted);
  pointer-events: none;
  font-size: 0.85rem;
}

.app-select--sm .app-select__field {
  padding: 8px 32px 8px 12px;
  font-size: 0.88rem;
  min-height: 36px;
}

.app-select__msg {
  font-size: 0.78rem;
  font-weight: 500;
  color: var(--color-text-muted);
}

.app-select__msg--error {
  color: var(--color-danger);
}
</style>
