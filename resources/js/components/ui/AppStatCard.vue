<script setup>
import { ref, onMounted, watch } from 'vue';
import { useGsap } from '../../composables/useGsap.js';

const props = defineProps({
  label: { type: String, required: true },
  value: { type: [Number, String], required: true },
  icon: { type: [Object, Function], default: null },
  variant: {
    type: String,
    default: 'primary',
    validator: (v) => ['primary', 'success', 'warning', 'danger', 'info', 'admin', 'teacher', 'student'].includes(v),
  },
  countUp: { type: Boolean, default: false },
});

const displayValue = ref(props.countUp && typeof props.value === 'number' ? 0 : props.value);
const valueRef = ref(null);
const { countUp: animateCount } = useGsap();

const gradientMap = {
  primary: 'linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%)',
  success: 'linear-gradient(135deg, #10b981 0%, #0ea5e9 100%)',
  warning: 'linear-gradient(135deg, #f59e0b 0%, #ef4444 100%)',
  danger: 'linear-gradient(135deg, #ef4444 0%, #ec4899 100%)',
  info: 'linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%)',
  admin: 'linear-gradient(135deg, #f59e0b 0%, #ef4444 100%)',
  teacher: 'linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%)',
  student: 'linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%)',
};

onMounted(() => {
  if (props.countUp && typeof props.value === 'number') {
    animateCount(valueRef.value, props.value);
  }
});

watch(
  () => props.value,
  (v) => {
    if (!props.countUp || typeof v !== 'number') {
      displayValue.value = v;
      return;
    }
    animateCount(valueRef.value, v);
  }
);
</script>

<template>
  <div class="app-stat-card" :style="{ background: gradientMap[variant] }">
    <div class="app-stat-card__overlay">
      <div v-if="icon || $slots.icon" class="app-stat-card__icon">
        <slot name="icon">
          <component :is="icon" :size="26" :stroke-width="2" />
        </slot>
      </div>
      <div class="app-stat-card__info">
        <span class="app-stat-card__label">{{ label }}</span>
        <strong ref="valueRef" class="app-stat-card__value">{{ displayValue }}</strong>
      </div>
    </div>
  </div>
</template>

<style scoped>
.app-stat-card {
  position: relative;
  border-radius: var(--radius-xl);
  overflow: hidden;
  box-shadow: var(--shadow-md);
  transition: transform var(--duration-base) var(--ease-out),
    box-shadow var(--duration-base) var(--ease-out);
}

.app-stat-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-lg);
}

.app-stat-card__overlay {
  position: relative;
  background: rgba(6, 9, 18, 0.32);
  padding: 22px;
  display: flex;
  align-items: center;
  gap: 16px;
  min-height: 100px;
  height: 100%;
  box-sizing: border-box;
  backdrop-filter: blur(2px);
}

.app-stat-card__icon {
  display: grid;
  place-items: center;
  width: 52px;
  height: 52px;
  background: rgba(255, 255, 255, 0.18);
  border-radius: var(--radius-lg);
  color: white;
  flex-shrink: 0;
}

.app-stat-card__info {
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.app-stat-card__label {
  font-size: 0.78rem;
  text-transform: uppercase;
  font-weight: 600;
  letter-spacing: 0.05em;
  color: rgba(255, 255, 255, 0.85);
}

.app-stat-card__value {
  font-size: 1.85rem;
  color: white;
  font-weight: 800;
  line-height: 1.1;
  margin-top: 4px;
  font-variant-numeric: tabular-nums;
}
</style>
