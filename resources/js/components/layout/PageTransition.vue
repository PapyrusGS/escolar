<script setup>
import { ref, onMounted } from 'vue';
import { useGsap } from '../../composables/useGsap.js';

const root = ref(null);
const { pageEnter } = useGsap();

onMounted(() => {
  if (root.value) pageEnter(root.value);
});
</script>

<template>
  <div ref="root" class="page-transition">
    <slot />
  </div>
</template>

<style scoped>
.page-transition {
  width: 100%;
}
</style>
