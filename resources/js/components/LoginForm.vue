<script setup>
import { ref, onMounted } from 'vue';
import { Mail, Lock, GraduationCap, ArrowRight, Eye, EyeOff } from '@lucide/vue';
import axios from 'axios';
import AppInput from './ui/AppInput.vue';
import AppButton from './ui/AppButton.vue';
import AppAlert from './ui/AppAlert.vue';
import { useGsap } from '../composables/useGsap.js';

const root = ref(null);
const { pageEnter, staggerIn } = useGsap();

const form = ref({ Correo: '', Contrasena: '' });
const showPassword = ref(false);
const loading = ref(false);
const errorMsg = ref('');

onMounted(() => {
  if (root.value) {
    pageEnter(root.value);
    const els = root.value.querySelectorAll('[data-anim]');
    if (els.length) staggerIn(els, { delay: 0.1 });
  }
});

const submit = async () => {
  if (loading.value) return;
  errorMsg.value = '';
  loading.value = true;
  try {
    const { data } = await axios.post('/api/auth/login', form.value);
    if (!data.status) {
      errorMsg.value = data.message || 'No se pudo iniciar sesión.';
      return;
    }
    localStorage.setItem('auth_token', data.data.token);
    localStorage.setItem('auth_user', JSON.stringify(data.data.user));
    axios.defaults.headers.common.Authorization = `Bearer ${data.data.token}`;
    window.location.href = '/index';
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Credenciales inválidas o usuario inactivo.';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div ref="root" class="login">
    <div class="login__card">
      <div class="login__brand" data-anim>
        <div class="login__logo">
          <GraduationCap :size="28" :stroke-width="2.2" />
        </div>
        <div>
          <h1 class="login__title">Sistema Escolar</h1>
          <p class="login__subtitle">Plataforma académica integral</p>
        </div>
      </div>

      <div class="login__welcome" data-anim>
        <h2>Bienvenido de vuelta</h2>
        <p>Ingresa con tus credenciales para continuar</p>
      </div>

      <AppAlert
        v-if="errorMsg"
        variant="danger"
        :title="errorMsg"
        class="login__alert"
        data-anim
      />

      <form class="login__form" @submit.prevent="submit">
        <div data-anim>
          <AppInput
            v-model="form.Correo"
            label="Correo institucional"
            type="email"
            placeholder="usuario@dominio.com"
            autocomplete="username"
            required
            :icon="Mail"
          />
        </div>

        <div data-anim class="login__password-wrap">
          <AppInput
            v-model="form.Contrasena"
            label="Contraseña"
            :type="showPassword ? 'text' : 'password'"
            placeholder="••••••••"
            autocomplete="current-password"
            required
            :icon="Lock"
          />
          <button
            type="button"
            class="login__password-toggle"
            :aria-label="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'"
            @click="showPassword = !showPassword"
          >
            <component :is="showPassword ? EyeOff : Eye" :size="18" />
          </button>
        </div>

        <div data-anim>
          <AppButton type="submit" variant="primary" size="lg" :loading="loading" block>
            <template v-if="!loading">
              Ingresar
              <ArrowRight :size="18" />
            </template>
          </AppButton>
        </div>
      </form>

      <p class="login__footer" data-anim>
        Acceso restringido a personal autorizado
      </p>
    </div>

    <div class="login__hero">
      <div class="login__hero-glow"></div>
    </div>
  </div>
</template>

<style scoped>
.login {
  min-height: 100vh;
  display: grid;
  grid-template-columns: 1fr 1fr;
  align-items: center;
  padding: 24px;
  gap: 48px;
  max-width: 1280px;
  margin: 0 auto;
}

.login__hero {
  position: relative;
  display: grid;
  place-items: center;
  min-height: 540px;
  border-radius: var(--radius-2xl);
  overflow: hidden;
  background: linear-gradient(135deg, var(--color-primary) 0%, #8b5cf6 50%, #ec4899 100%);
  box-shadow: var(--shadow-xl);
}

.login__hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image:
    radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.18) 0%, transparent 50%),
    radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.12) 0%, transparent 60%);
  pointer-events: none;
}

.login__hero::after {
  content: '';
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(45deg, transparent 48%, rgba(255, 255, 255, 0.06) 49%, rgba(255, 255, 255, 0.06) 51%, transparent 52%);
  background-size: 28px 28px;
  pointer-events: none;
  opacity: 0.5;
}

.login__card {
  position: relative;
  width: 100%;
  max-width: 460px;
  margin: 0 auto;
  padding: 40px;
  background: linear-gradient(180deg, rgba(28, 39, 66, 0.55) 0%, rgba(19, 28, 48, 0.65) 100%);
  border: 1px solid var(--color-border-default);
  border-radius: var(--radius-2xl);
  box-shadow: var(--shadow-xl);
  backdrop-filter: blur(20px) saturate(140%);
  -webkit-backdrop-filter: blur(20px) saturate(140%);
}

.login__brand {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 32px;
}

.login__logo {
  display: grid;
  place-items: center;
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, var(--color-primary) 0%, #8b5cf6 100%);
  color: white;
  border-radius: var(--radius-lg);
  box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
}

.login__title {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--color-text-primary);
}

.login__subtitle {
  margin: 2px 0 0;
  font-size: 0.78rem;
  color: var(--color-text-muted);
  font-weight: 500;
}

.login__welcome h2 {
  margin: 0 0 6px;
  font-size: 1.5rem;
  font-weight: 800;
  color: var(--color-text-primary);
  letter-spacing: -0.02em;
}

.login__welcome p {
  margin: 0;
  color: var(--color-text-secondary);
  font-size: 0.95rem;
}

.login__form {
  display: flex;
  flex-direction: column;
  gap: 18px;
  margin-top: 28px;
}

.login__password-wrap {
  position: relative;
}

.login__password-toggle {
  position: absolute;
  right: 12px;
  bottom: 11px;
  background: transparent;
  border: 0;
  color: var(--color-text-muted);
  padding: 6px;
  border-radius: var(--radius-sm);
  cursor: pointer;
  display: grid;
  place-items: center;
  transition: color var(--duration-fast) var(--ease-out);
}

.login__password-toggle:hover {
  color: var(--color-text-primary);
}

.login__alert {
  margin-top: 16px;
}

.login__footer {
  margin: 24px 0 0;
  text-align: center;
  font-size: 0.78rem;
  color: var(--color-text-muted);
}

@media (max-width: 900px) {
  .login {
    grid-template-columns: 1fr;
    gap: 24px;
    padding: 16px;
  }
  .login__hero {
    display: none;
  }
  .login__card {
    padding: 32px 24px;
  }
}
</style>
