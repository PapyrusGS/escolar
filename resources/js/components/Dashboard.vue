<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import AppShell from './layout/AppShell.vue';
import PageTransition from './layout/PageTransition.vue';
import EstudianteDashboard from './EstudianteDashboard.vue';
import DocenteDashboard from './DocenteDashboard.vue';
import AdminDashboard from './AdminDashboard.vue';
import { toast } from '../lib/toast.js';

const user = ref(null);
const loading = ref(true);

const roleMap = {
  1: { label: 'Administrador' },
  2: { label: 'Docente' },
  3: { label: 'Estudiante' },
};
const userRole = computed(() => roleMap[Number(user.value?.IdRol)] || { label: 'Usuario' });

onMounted(async () => {
  const token = localStorage.getItem('auth_token');
  if (!token) {
    window.location.href = '/';
    return;
  }
  axios.defaults.headers.common.Authorization = `Bearer ${token}`;

  try {
    const stored = localStorage.getItem('auth_user');
    if (stored) user.value = JSON.parse(stored);

    const { data } = await axios.get('/api/auth/perfil');
    if (data?.data?.user) {
      user.value = data.data.user;
      localStorage.setItem('auth_user', JSON.stringify(user.value));
    }
  } catch (err) {
    toast.error('No se pudo cargar la información del usuario.');
    localStorage.removeItem('auth_token');
    localStorage.removeItem('auth_user');
    delete axios.defaults.headers.common.Authorization;
    window.location.href = '/';
  } finally {
    loading.value = false;
  }
});

const handleLogout = async () => {
  try {
    if (localStorage.getItem('auth_token')) await axios.post('/api/auth/logout');
  } catch {}
  localStorage.clear();
  delete axios.defaults.headers.common.Authorization;
  window.location.href = '/';
};
</script>

<template>
  <AppShell
    v-if="user"
    :user="user"
    :page-title="`Dashboard · ${userRole.label}`"
    @logout="handleLogout"
  >
    <PageTransition>
      <EstudianteDashboard v-if="Number(user.IdRol) === 3" />
      <DocenteDashboard v-else-if="Number(user.IdRol) === 2" />
      <AdminDashboard v-else-if="Number(user.IdRol) === 1" />
    </PageTransition>
  </AppShell>
</template>
