<script setup>
defineProps({
  modelValue: { type: [String, Number], default: '' },
  label: { type: String, default: '' },
  type: { type: String, default: 'text' },
  placeholder: { type: String, default: '' },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  error: { type: String, default: '' },
  hint: { type: String, default: '' },
  id: { type: String, default: () => `inp-${Math.random().toString(36).slice(2, 9)}` },
  min: { type: [String, Number], default: undefined },
  max: { type: [String, Number], default: undefined },
  step: { type: [String, Number], default: undefined },
  autocomplete: { type: String, default: 'off' },
  size: { type: String, default: 'md' },
  icon: { type: [Object, Function], default: null },
});

defineEmits(['update:modelValue']);
</script>

<template>
  <label :for="id" :class="['app-input', `app-input--${size}`, error && 'app-input--error', icon && 'app-input--with-icon']">
    <span v-if="label" class="app-input__label">
      {{ label }}
      <span v-if="required" class="app-input__required" aria-hidden="true">*</span>
    </span>
    <div v-if="icon" class="app-input__wrap">
      <span class="app-input__icon">
        <component :is="icon" :size="18" />
      </span>
      <input
        :id="id"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :min="min"
        :max="max"
        :step="step"
        :autocomplete="autocomplete"
        :aria-invalid="error ? 'true' : 'false'"
        :aria-describedby="error || hint ? `${id}-msg` : undefined"
        class="app-input__field"
        @input="$emit('update:modelValue', $event.target.value)"
      />
    </div>
    <input
      v-else
      :id="id"
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :required="required"
      :disabled="disabled"
      :min="min"
      :max="max"
      :step="step"
      :autocomplete="autocomplete"
      :aria-invalid="error ? 'true' : 'false'"
      :aria-describedby="error || hint ? `${id}-msg` : undefined"
      class="app-input__field"
      @input="$emit('update:modelValue', $event.target.value)"
    />
    <span v-if="error || hint" :id="`${id}-msg`" :class="['app-input__msg', error && 'app-input__msg--error']">
      {{ error || hint }}
    </span>
  </label>
</template>

<style scoped>
.app-input {
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-weight: 600;
  font-size: 0.85rem;
  color: var(--color-text-primary);
}

.app-input__label {
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.app-input__required {
  color: var(--color-danger);
  font-weight: 700;
}

.app-input__wrap {
  position: relative;
  display: flex;
  align-items: center;
}

.app-input__icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--color-text-muted);
  pointer-events: none;
  display: grid;
  place-items: center;
}

.app-input--with-icon .app-input__field {
  padding-left: 44px;
}

.app-input__field {
  width: 100%;
  padding: 11px 14px;
  font-family: inherit;
  font-size: 0.95rem;
  font-weight: 400;
  color: var(--color-text-primary);
  background: var(--color-surface-2);
  border: 1px solid var(--color-border-default);
  border-radius: var(--radius-md);
  transition: border-color var(--duration-fast) var(--ease-out),
    box-shadow var(--duration-fast) var(--ease-out),
    background var(--duration-fast) var(--ease-out);
  min-height: 44px;
  box-sizing: border-box;
}

.app-input__field::placeholder {
  color: var(--color-text-disabled);
}

.app-input__field:hover:not(:disabled) {
  border-color: var(--color-border-strong);
}

.app-input__field:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: var(--shadow-focus);
  background: var(--color-surface-1);
}

.app-input--error .app-input__field {
  border-color: var(--color-danger);
}

.app-input--error .app-input__field:focus {
  box-shadow: var(--shadow-focus-danger);
}

.app-input__field:disabled {
  background: var(--color-surface-1);
  color: var(--color-text-disabled);
  cursor: not-allowed;
}

.app-input--sm .app-input__field {
  padding: 8px 12px;
  font-size: 0.88rem;
  min-height: 36px;
}

.app-input--sm.app-input--with-icon .app-input__field {
  padding-left: 36px;
}

.app-input__msg {
  font-size: 0.78rem;
  font-weight: 500;
  color: var(--color-text-muted);
}

.app-input__msg--error {
  color: var(--color-danger);
}
</style>
