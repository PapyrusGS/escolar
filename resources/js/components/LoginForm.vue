<script setup>
import { ref, onMounted } from 'vue';
import { Mail, Lock, GraduationCap, ArrowRight, Eye, EyeOff, Sparkles, ShieldCheck, BookOpen, Award } from '@lucide/vue';
import axios from 'axios';
import AppInput from './ui/AppInput.vue';
import AppButton from './ui/AppButton.vue';
import AppAlert from './ui/AppAlert.vue';

const form = ref({ Correo: '', Contrasena: '' });
const showPassword = ref(false);
const loading = ref(false);
const errorMsg = ref('');

onMounted(() => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    axios.defaults.headers.common.Authorization = `Bearer ${token}`;
    window.location.href = '/dashboard';
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
    window.location.href = '/dashboard';
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Credenciales inválidas o usuario inactivo.';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="login">
    <div class="login__card">
      <div class="login__brand">
        <div class="login__logo">
          <GraduationCap :size="28" :stroke-width="2.2" />
        </div>
        <div>
          <h1 class="login__title">Sistema Universitario</h1>
          <p class="login__subtitle">Plataforma académica integral</p>
        </div>
      </div>

      <div class="login__welcome">
        <h2>Bienvenido de vuelta</h2>
        <p>Ingresa con tus credenciales para acceder al sistema</p>
      </div>

      <AppAlert
        v-if="errorMsg"
        variant="danger"
        :title="errorMsg"
        class="login__alert"
      />

      <form class="login__form" @submit.prevent="submit" novalidate>
        <div>
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

        <div class="login__password-wrap">
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

        <div>
          <AppButton type="submit" variant="primary" size="lg" :loading="loading" block>
            <template v-if="!loading">
              Ingresar al sistema
              <ArrowRight :size="18" />
            </template>
          </AppButton>
        </div>
      </form>

      <div class="login__features">
        <div class="login__feature">
          <ShieldCheck :size="18" />
          <span>Acceso seguro</span>
        </div>
        <div class="login__feature">
          <BookOpen :size="18" />
          <span>Gestión académica</span>
        </div>
        <div class="login__feature">
          <Award :size="18" />
          <span>Calificaciones</span>
        </div>
      </div>

      <p class="login__footer">
        <Sparkles :size="12" />
        Acceso restringido a personal autorizado
      </p>
    </div>
  </div>
</template>

<style scoped>
.login {
  min-height: 100vh;
  display: grid;
  place-items: center;
  padding: 24px;
  background:
    radial-gradient(ellipse 80% 60% at 50% 0%, rgba(99, 102, 241, 0.18), transparent 60%),
    radial-gradient(ellipse 70% 50% at 50% 100%, rgba(139, 92, 246, 0.10), transparent 60%);
}

.login__card {
  position: relative;
  width: 100%;
  max-width: 460px;
  padding: 40px;
  background: linear-gradient(180deg, rgba(28, 39, 66, 0.65) 0%, rgba(19, 28, 48, 0.75) 100%);
  border: 1px solid var(--color-border-default);
  border-radius: var(--radius-2xl);
  box-shadow: var(--shadow-xl);
  backdrop-filter: blur(20px) saturate(140%);
  -webkit-backdrop-filter: blur(20px) saturate(140%);
  animation: fadeUp 0.4s var(--ease-out) both;
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
  flex-shrink: 0;
}

.login__title {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--color-text-primary);
  letter-spacing: -0.01em;
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

.login__features {
  display: flex;
  justify-content: space-between;
  gap: 10px;
  margin-top: 28px;
  padding-top: 20px;
  border-top: 1px solid var(--color-border-subtle);
  flex-wrap: wrap;
}

.login__feature {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.78rem;
  font-weight: 600;
  color: var(--color-text-muted);
}

.login__feature :deep(svg) {
  color: var(--color-primary);
  flex-shrink: 0;
}

.login__footer {
  margin: 18px 0 0;
  text-align: center;
  font-size: 0.78rem;
  color: var(--color-text-muted);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  width: 100%;
}

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(12px); }
  to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 520px) {
  .login { padding: 16px; }
  .login__card { padding: 28px 22px; }
  .login__welcome h2 { font-size: 1.3rem; }
  .login__features { justify-content: center; }
}
</style>
