<script setup>
import { computed } from 'vue';

const props = defineProps({
  name: { type: String, default: '' },
  src: { type: String, default: '' },
  size: { type: String, default: 'md' },
  variant: {
    type: String,
    default: 'primary',
    validator: (v) => ['primary', 'admin', 'teacher', 'student', 'neutral'].includes(v),
  },
});

const sizes = {
  xs: 28,
  sm: 36,
  md: 44,
  lg: 64,
  xl: 96,
};

const sizePx = computed(() => sizes[props.size] || sizes.md);

const initials = computed(() => {
  if (!props.name) return '?';
  return props.name
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((s) => s[0])
    .join('')
    .toUpperCase();
});

const gradient = computed(() => {
  const map = {
    primary: 'linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%)',
    admin: 'linear-gradient(135deg, #f59e0b 0%, #ef4444 100%)',
    teacher: 'linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%)',
    student: 'linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%)',
    neutral: 'linear-gradient(135deg, #475569 0%, #334155 100%)',
  };
  return map[props.variant];
});
</script>

<template>
  <div
    v-if="!src"
    :class="['app-avatar', `app-avatar--${size}`]"
    :style="{
      width: sizePx + 'px',
      height: sizePx + 'px',
      background: gradient,
      fontSize: Math.max(11, sizePx * 0.4) + 'px',
    }"
    :aria-label="name || 'Avatar'"
  >
    {{ initials }}
  </div>
  <img
    v-else
    :src="src"
    :alt="name"
    :style="{ width: sizePx + 'px', height: sizePx + 'px' }"
    class="app-avatar app-avatar--img"
  />
</template>

<style scoped>
.app-avatar {
  display: grid;
  place-items: center;
  border-radius: 50%;
  color: white;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.02em;
  flex-shrink: 0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  user-select: none;
}

.app-avatar--img {
  object-fit: cover;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}
</style>
