<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import AppSidebar from './AppSidebar.vue';
import AppTopbar from './AppTopbar.vue';

const props = defineProps({
  user: { type: Object, required: true },
  pageTitle: { type: String, default: '' },
  unreadNotifications: { type: Number, default: 0 },
});
const emit = defineEmits(['logout', 'open-notifications']);

const collapsed = ref(false);
const mobileOpen = ref(false);

const onResize = () => {
  if (window.innerWidth > 1024) mobileOpen.value = false;
};

onMounted(() => {
  window.addEventListener('resize', onResize);
});
onUnmounted(() => {
  window.removeEventListener('resize', onResize);
});

const handleToggleMobile = (val) => {
  mobileOpen.value = typeof val === 'boolean' ? val : !mobileOpen.value;
};
</script>

<template>
  <div class="app-shell">
    <div
      v-if="mobileOpen"
      class="app-shell__backdrop"
      @click="handleToggleMobile(false)"
      aria-hidden="true"
    ></div>

    <AppSidebar
      :user="user"
      :collapsed="collapsed"
      :mobile-open="mobileOpen"
      :unread-notifications="unreadNotifications"
      @toggle-mobile="handleToggleMobile"
      @logout="emit('logout')"
    />

    <div class="app-shell__main">
      <AppTopbar
        :user="user"
        :page-title="pageTitle"
        :unread-notifications="unreadNotifications"
        @toggle-mobile="handleToggleMobile(true)"
        @open-notifications="emit('open-notifications')"
      />
      <main class="app-shell__content">
        <slot />
      </main>
    </div>
  </div>
</template>

<style scoped>
.app-shell {
  display: flex;
  min-height: 100vh;
}

.app-shell__backdrop {
  position: fixed;
  inset: 0;
  background: rgba(6, 9, 18, 0.7);
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
  z-index: 35;
  animation: fadeIn 0.2s ease-out;
}

.app-shell__main {
  display: flex;
  flex-direction: column;
  flex: 1;
  min-width: 0;
}

.app-shell__content {
  flex: 1;
  padding: 24px 32px 48px;
  max-width: 1400px;
  width: 100%;
  margin: 0 auto;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@media (max-width: 768px) {
  .app-shell__content {
    padding: 16px 14px 32px;
  }
}
</style>
