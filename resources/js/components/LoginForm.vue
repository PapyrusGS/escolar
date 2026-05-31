<template>
  <section class="login-card">
    <div class="login-card__copy">
      <p class="eyebrow">Sistema escolar</p>
      <h1>Iniciar sesión</h1>
      <p class="lead">Accede con tu correo institucional y tu contraseña para continuar.</p>
    </div>

    <form class="login-form" @submit.prevent="submit">
      <label class="field">
        <span>Correo</span>
        <input
          v-model.trim="form.Correo"
          class="input"
          type="email"
          autocomplete="username"
          placeholder="correo@dominio.com"
        >
        <small v-if="errors.Correo" class="error">{{ errors.Correo[0] }}</small>
      </label>

      <label class="field">
        <span>Contraseña</span>
        <input
          v-model="form.Contrasena"
          class="input"
          type="password"
          autocomplete="current-password"
          placeholder="••••••••"
        >
        <small v-if="errors.Contrasena" class="error">{{ errors.Contrasena[0] }}</small>
      </label>

      <div v-if="message" :class="['alert', messageType === 'success' ? 'alert--success' : 'alert--error']">
        {{ message }}
      </div>

      <button class="button" type="submit" :disabled="loading">
        {{ loading ? 'Ingresando...' : 'Entrar' }}
      </button>
    </form>
  </section>
</template>

<script>
import axios from 'axios';

export default {
  name: 'LoginForm',
  data() {
    return {
      loading: false,
      message: '',
      messageType: 'success',
      errors: {},
      form: {
        Correo: '',
        Contrasena: '',
      },
    };
  },
  methods: {
    async submit() {
      try {
        this.loading = true;
        this.message = '';
        this.errors = {};

        const response = await axios.post('/api/auth/login', this.form);
        const payload = response.data;

        if (!payload.status) {
          this.message = payload.message || 'No se pudo iniciar sesión.';
          this.messageType = 'error';
          return;
        }

        localStorage.setItem('auth_token', payload.data.token);
        localStorage.setItem('auth_user', JSON.stringify(payload.data.user));
        axios.defaults.headers.common.Authorization = `Bearer ${payload.data.token}`;
        window.location.href = '/index';
      } catch (error) {
        console.error(error);
        this.message = error.response?.data?.message || 'Credenciales inválidas o usuario inactivo.';
        this.messageType = 'error';
        this.errors = error.response?.data?.data || {};
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.login-card {
  width: min(100%, 460px);
  margin: 0 auto;
  padding: 2rem;
  border-radius: 28px;
  background: rgba(15, 23, 42, 0.82);
  border: 1px solid rgba(255, 255, 255, 0.08);
  color: #e5e7eb;
  box-shadow: 0 24px 60px rgba(0, 0, 0, 0.25);
}

.login-card__copy {
  margin-bottom: 1.25rem;
}

.eyebrow {
  margin: 0 0 0.35rem;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  font-size: 0.72rem;
  color: #fbbf24;
}

h1 {
  margin: 0;
  font-size: 2rem;
}

.lead {
  margin: 0.5rem 0 0;
  color: #cbd5e1;
}

.login-form {
  display: grid;
  gap: 1rem;
}

.field {
  display: grid;
  gap: 0.35rem;
}

.field span {
  font-size: 0.95rem;
  font-weight: 600;
}

.input,
.button {
  border-radius: 14px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  background: rgba(255, 255, 255, 0.06);
  color: #e5e7eb;
  padding: 0.9rem 1rem;
  font: inherit;
}

.input {
  width: 100%;
  box-sizing: border-box;
}

.button {
  cursor: pointer;
  font-weight: 700;
  background: linear-gradient(135deg, #fbbf24, #f97316);
  color: #111827;
  border: 0;
}

.button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.alert {
  border-radius: 16px;
  padding: 0.85rem 1rem;
  font-weight: 600;
}

.alert--success {
  background: rgba(16, 185, 129, 0.16);
  color: #a7f3d0;
  border: 1px solid rgba(16, 185, 129, 0.25);
}

.alert--error {
  background: rgba(239, 68, 68, 0.16);
  color: #fecaca;
  border: 1px solid rgba(239, 68, 68, 0.25);
}

.error {
  color: #fca5a5;
}
</style>
