<template>
  <div>
    <h2>Login</h2>
    <form @submit.prevent="login">
      <div>
        <label for="correo">Correo</label>
        <input id="correo" v-model="correo" type="email" required />
      </div>
      <div>
        <label for="contrasena">Contrasena</label>
        <input id="contrasena" v-model="contrasena" type="password" required />
      </div>
      <p v-if="message">{{ message }}</p>
      <button type="submit" :disabled="loading">
        {{ loading ? 'Ingresando...' : 'Login' }}
      </button>
    </form>
  </div>
</template>

<script>
import { ref } from 'vue';
import axios from 'axios';

export default {
  name: 'LoginForm',
  emits: ['success'],
  setup(_, { emit }) {
    const correo = ref('');
    const contrasena = ref('');
    const message = ref('');
    const loading = ref(false);

    const login = async () => {
      try {
        loading.value = true;
        message.value = '';

        const response = await axios.post('/api/auth/login', {
          Correo: correo.value,
          Contrasena: contrasena.value,
        });

        const { token, user } = response.data.data;

        localStorage.setItem('auth_token', token);
        localStorage.setItem('auth_user', JSON.stringify(user));
        axios.defaults.headers.common.Authorization = `Bearer ${token}`;
        message.value = `Bienvenido, ${user.Nombre1}`;
        emit('success', user);
        window.location.href = '/index';
      } catch (error) {
        console.error(error);
        message.value = error.response?.data?.message ?? 'No se pudo iniciar sesión.';
      } finally {
        loading.value = false;
      }
    };

    return {
      correo,
      contrasena,
      message,
      loading,
      login,
    };
  },
};
</script>
