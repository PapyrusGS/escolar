<template>
  <section class="profile-page">
    <header class="hero">
      <div>
        <p class="eyebrow">RF03</p>
        <h1>Mi perfil</h1>
        <p class="lead">Visualiza y actualiza tus datos personales y contraseña.</p>
      </div>
      <a class="btn btn--ghost" href="/index">Volver al panel</a>
    </header>

    <div v-if="message" class="alert" :class="alertClass">{{ message }}</div>

    <div class="layout">
      <section class="card">
        <h2>Información personal</h2>
        <div class="info-grid">
          <div><span>Nombre</span><strong>{{ user?.Nombre1 }} {{ user?.Nombre2 || '' }}</strong></div>
          <div><span>Apellido</span><strong>{{ user?.Apellido1 }} {{ user?.Apellido2 || '' }}</strong></div>
          <div><span>Correo</span><strong>{{ user?.Correo }}</strong></div>
          <div><span>Teléfono</span><strong>{{ user?.Telefono || 'No registrado' }}</strong></div>
          <div><span>Rol</span><strong>{{ user?.Rol?.Nombre }}</strong></div>
          <div><span>Fecha registro</span><strong>{{ user?.FechaRegistro || 'N/A' }}</strong></div>
        </div>
      </section>

      <section class="card">
        <h2>Editar datos personales</h2>
        <form class="form" @submit.prevent="saveProfile">
          <label>Primer nombre<input v-model="profileForm.Nombre1" type="text" required /></label>
          <label>Segundo nombre<input v-model="profileForm.Nombre2" type="text" /></label>
          <label>Primer apellido<input v-model="profileForm.Apellido1" type="text" required /></label>
          <label>Segundo apellido<input v-model="profileForm.Apellido2" type="text" /></label>
          <label>Teléfono<input v-model="profileForm.Telefono" type="number" /></label>
          <label>Correo<input v-model="profileForm.Correo" type="email" required /></label>
          <button class="btn" type="submit" :disabled="savingProfile">{{ savingProfile ? 'Guardando...' : 'Guardar cambios' }}</button>
        </form>
      </section>

      <section class="card">
        <h2>Cambiar contraseña</h2>
        <form class="form" @submit.prevent="changePassword">
          <label>Contraseña actual<input v-model="passwordForm.current_password" type="password" required /></label>
          <label>Nueva contraseña<input v-model="passwordForm.password" type="password" minlength="6" required /></label>
          <label>Confirmar nueva contraseña<input v-model="passwordForm.password_confirmation" type="password" minlength="6" required /></label>
          <button class="btn" type="submit" :disabled="savingPassword">{{ savingPassword ? 'Actualizando...' : 'Cambiar contraseña' }}</button>
        </form>
      </section>
    </div>
  </section>
</template>

<script>
import { computed, onMounted, reactive, ref } from 'vue';
import axios from 'axios';

export default {
  name: 'ProfilePage',
  setup() {
    const user = ref(null);
    const message = ref('');
    const messageType = ref('success');
    const savingProfile = ref(false);
    const savingPassword = ref(false);

    const profileForm = reactive({
      Nombre1: '',
      Nombre2: '',
      Apellido1: '',
      Apellido2: '',
      Telefono: '',
      Correo: '',
    });

    const passwordForm = reactive({
      current_password: '',
      password: '',
      password_confirmation: '',
    });

    const alertClass = computed(() => (messageType.value === 'success' ? 'alert--success' : 'alert--error'));

    const loadProfile = async () => {
      const token = localStorage.getItem('auth_token');
      if (!token) {
        window.location.href = '/';
        return;
      }

      axios.defaults.headers.common.Authorization = `Bearer ${token}`;

      const response = await axios.get('/api/perfil');
      user.value = response.data.data.user;
      profileForm.Nombre1 = user.value.Nombre1 || '';
      profileForm.Nombre2 = user.value.Nombre2 || '';
      profileForm.Apellido1 = user.value.Apellido1 || '';
      profileForm.Apellido2 = user.value.Apellido2 || '';
      profileForm.Telefono = user.value.Telefono || '';
      profileForm.Correo = user.value.Correo || '';
    };

    const saveProfile = async () => {
      try {
        savingProfile.value = true;
        message.value = '';

        const payload = {
          ...profileForm,
          Telefono: profileForm.Telefono ? Number(profileForm.Telefono) : null,
        };

        const response = await axios.put('/api/perfil', payload);
        user.value = response.data.data.user;
        localStorage.setItem('auth_user', JSON.stringify(user.value));
        messageType.value = 'success';
        message.value = response.data.message;
      } catch (error) {
        console.error(error);
        messageType.value = 'error';
        message.value = error.response?.data?.message ?? 'No se pudo actualizar el perfil.';
      } finally {
        savingProfile.value = false;
      }
    };

    const changePassword = async () => {
      try {
        savingPassword.value = true;
        message.value = '';

        const response = await axios.put('/api/perfil/password', passwordForm);
        messageType.value = 'success';
        message.value = response.data.message;
        passwordForm.current_password = '';
        passwordForm.password = '';
        passwordForm.password_confirmation = '';
      } catch (error) {
        console.error(error);
        messageType.value = 'error';
        message.value = error.response?.data?.message ?? 'No se pudo cambiar la contraseña.';
      } finally {
        savingPassword.value = false;
      }
    };

    onMounted(loadProfile);

    return {
      user,
      profileForm,
      passwordForm,
      message,
      alertClass,
      savingProfile,
      savingPassword,
      saveProfile,
      changePassword,
    };
  },
};
</script>

<style scoped>
.profile-page {
  display: grid;
  gap: 1rem;
  color: #e5e7eb;
}

.hero, .layout {
  display: grid;
  gap: 1rem;
}

.hero {
  grid-template-columns: 1fr auto;
  align-items: start;
}

.eyebrow {
  margin: 0 0 0.35rem;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  font-size: 0.72rem;
  color: #fbbf24;
}

h1, h2 { margin: 0; }
.lead { margin: 0.4rem 0 0; color: #cbd5e1; }

.btn {
  border: 0;
  border-radius: 999px;
  padding: 0.8rem 1rem;
  font-weight: 700;
  cursor: pointer;
  background: linear-gradient(135deg, #f97316, #fbbf24);
  color: #111827;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn--ghost {
  background: rgba(255, 255, 255, 0.08);
  color: #e5e7eb;
}

.alert {
  padding: 0.9rem 1rem;
  border-radius: 14px;
}

.alert--success {
  background: rgba(16, 185, 129, 0.15);
  border: 1px solid rgba(16, 185, 129, 0.35);
}

.alert--error {
  background: rgba(239, 68, 68, 0.15);
  border: 1px solid rgba(239, 68, 68, 0.35);
}

.layout {
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
}

.card {
  padding: 1.2rem;
  border-radius: 22px;
  background: rgba(15, 23, 42, 0.72);
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
}

.info-grid {
  display: grid;
  gap: 0.9rem;
  margin-top: 1rem;
}

.info-grid span {
  display: block;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #fbbf24;
  margin-bottom: 0.25rem;
}

.form {
  display: grid;
  gap: 0.9rem;
  margin-top: 1rem;
}

label {
  display: grid;
  gap: 0.4rem;
}

input {
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 14px;
  padding: 0.85rem 0.95rem;
  background: rgba(255, 255, 255, 0.05);
  color: #e5e7eb;
}
</style>
