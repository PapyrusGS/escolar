<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import {
  User as UserIcon,
  ShieldCheck,
  Save,
  KeyRound,
  Mail,
  Phone,
  IdCard,
  ArrowLeft,
  Lock,
  GraduationCap,
  Layers,
  Eye,
  EyeOff,
  AtSign,
  Info,
} from '@lucide/vue';
import AppShell from './layout/AppShell.vue';
import PageTransition from './layout/PageTransition.vue';
import AppCard from './ui/AppCard.vue';
import AppButton from './ui/AppButton.vue';
import AppInput from './ui/AppInput.vue';
import AppAlert from './ui/AppAlert.vue';
import AppPageHeader from './ui/AppPageHeader.vue';
import AppRoleBadge from './ui/AppRoleBadge.vue';
import AppAvatar from './ui/AppAvatar.vue';
import AppBadge from './ui/AppBadge.vue';
import { toast } from '../lib/toast.js';
import { useGoTo } from '../composables/useGoTo.js';

const { goTo } = useGoTo();

const user = ref(null);
const activeTab = ref('datos');
const submitting = ref(false);
const errorMsg = ref('');
const successMsg = ref('');
const showCurrent = ref(false);
const showNew = ref(false);
const showConfirm = ref(false);

const form = ref({
  IdUsuario: null,
  IdRol: null,
  Rol: null,
  Nombre1: '',
  Nombre2: '',
  Apellido1: '',
  Apellido2: '',
  CI: '',
  Telefono: '',
  Correo: '',
  CorreoPersonal: '',
  CarreraNombre: '',
  ModalidadNombre: '',
});

const passwordForm = ref({
  password_actual: '',
  contrasena: '',
  contrasena_confirmation: '',
});

const roleVariant = computed(() => {
  const map = { 1: 'admin', 2: 'teacher', 3: 'student' };
  return map[Number(form.value.IdRol)] || 'default';
});

const roleLabel = computed(() => form.value.Rol?.Nombre || 'Cargando…');
const fullName = computed(() => `${form.value.Nombre1} ${form.value.Apellido1}`.trim() || 'Usuario');
const isStudent = computed(() => Number(form.value.IdRol) === 3);
const tabs = [
  { key: 'datos', label: 'Datos Personales', icon: UserIcon },
  { key: 'seguridad', label: 'Seguridad', icon: ShieldCheck },
];

onMounted(async () => {
  const token = localStorage.getItem('auth_token');
  if (!token) {
    window.location.href = '/';
    return;
  }
  axios.defaults.headers.common.Authorization = `Bearer ${token}`;

  const stored = localStorage.getItem('auth_user');
  if (stored) user.value = JSON.parse(stored);

  await loadProfile();
});

const loadProfile = async () => {
  try {
    const { data } = await axios.get('/api/auth/perfil');
    const u = data?.data?.user;
    if (u) form.value = { ...form.value, ...u };
  } catch (err) {
    errorMsg.value = 'No se pudo recuperar la información del perfil.';
  }
};

const clearAlerts = () => {
  errorMsg.value = '';
  successMsg.value = '';
};

const updateProfile = async () => {
  clearAlerts();
  submitting.value = true;
  try {
    const payload = {
      Telefono: form.value.Telefono,
      CorreoPersonal: form.value.CorreoPersonal || null,
    };
    const { data } = await axios.put('/api/auth/perfil', payload);
    const u = data?.data?.user;
    if (u) {
      form.value = { ...form.value, ...u };
      localStorage.setItem('auth_user', JSON.stringify(u));
    }
    successMsg.value = data?.message || 'Datos de contacto actualizados correctamente.';
    toast.success(successMsg.value);
  } catch (err) {
    if (err?.response?.status === 422) {
      const errors = err.response?.data?.errors;
      errorMsg.value = errors
        ? Object.values(errors).flat().join(' | ')
        : err.response?.data?.message || 'Datos no válidos.';
    } else {
      errorMsg.value = err?.response?.data?.message || 'No se pudo actualizar el perfil.';
    }
    toast.error(errorMsg.value);
  } finally {
    submitting.value = false;
  }
};

const changePassword = async () => {
  clearAlerts();
  if (passwordForm.value.contrasena !== passwordForm.value.contrasena_confirmation) {
    errorMsg.value = 'Las nuevas contraseñas no coinciden.';
    toast.error(errorMsg.value);
    return;
  }
  submitting.value = true;
  try {
    const { data } = await axios.put('/api/auth/contrasena', { ...passwordForm.value });
    successMsg.value = data?.message || 'Contraseña cambiada correctamente.';
    passwordForm.value = { password_actual: '', contrasena: '', contrasena_confirmation: '' };
    toast.success(successMsg.value);
  } catch (err) {
    errorMsg.value = err?.response?.data?.message || 'No se pudo cambiar la contraseña. Verifica tu clave actual.';
    toast.error(errorMsg.value);
  } finally {
    submitting.value = false;
  }
};

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
  <AppShell v-if="user" :user="user" page-title="Mi perfil" @logout="handleLogout">
    <PageTransition>
      <div class="up">
        <AppPageHeader
          eyebrow="Perfil de usuario"
          title="Mi perfil"
          description="Visualiza tu información académica, personal y administra la seguridad de tu cuenta."
        >
          <template #actions>
            <AppButton variant="secondary" :icon="ArrowLeft" @click="goTo('/dashboard')">
              Volver al Dashboard
            </AppButton>
          </template>
        </AppPageHeader>

        <AppAlert v-if="successMsg" variant="success" :title="successMsg" dismissible @dismiss="successMsg = ''" />
        <AppAlert v-if="errorMsg" variant="danger" :title="errorMsg" dismissible @dismiss="errorMsg = ''" />

        <AppCard padding="none" class="up__card">
          <div class="up__hero">
            <AppAvatar :name="fullName" :variant="roleVariant" size="xl" />
            <div class="up__hero-text">
              <h2>{{ fullName }}</h2>
              <div class="up__hero-meta">
                <AppRoleBadge :role="roleVariant" :label="roleLabel" />
                <span class="up__hero-mail"><Mail :size="14" /> {{ form.Correo }}</span>
              </div>
            </div>
          </div>

          <nav class="up__tabs" role="tablist">
            <button
              v-for="tab in tabs"
              :key="tab.key"
              type="button"
              role="tab"
              :aria-selected="activeTab === tab.key"
              :class="['up__tab', activeTab === tab.key && 'up__tab--active']"
              @click="activeTab = tab.key"
            >
              <component :is="tab.icon" :size="16" />
              {{ tab.label }}
            </button>
          </nav>

          <div v-if="activeTab === 'datos'" class="up__panel">
            <AppCard padding="lg" class="up__ficha">
              <header class="up__ficha-head">
                <div class="up__ficha-title">
                  <GraduationCap :size="20" />
                  <h3>Ficha académica</h3>
                </div>
                <AppBadge variant="info" size="sm">
                  <Lock :size="11" /> Solo lectura
                </AppBadge>
              </header>

              <div class="up__ficha-grid">
                <div class="up__ficha-item">
                  <span class="up__ficha-label"><UserIcon :size="13" /> Nombre completo</span>
                  <span class="up__ficha-value">{{ form.Nombre1 }} {{ form.Nombre2 || '' }} {{ form.Apellido1 }} {{ form.Apellido2 || '' }}</span>
                </div>
                <div class="up__ficha-item">
                  <span class="up__ficha-label"><IdCard :size="13" /> CI</span>
                  <span class="up__ficha-value">{{ form.CI || '—' }}</span>
                </div>
                <div class="up__ficha-item">
                  <span class="up__ficha-label"><AtSign :size="13" /> Correo académico</span>
                  <span class="up__ficha-value up__ficha-value--mono">{{ form.Correo || '—' }}</span>
                </div>
                <div class="up__ficha-item">
                  <span class="up__ficha-label"><ShieldCheck :size="13" /> Rol de acceso</span>
                  <span class="up__ficha-value">{{ roleLabel }}</span>
                </div>
                <template v-if="isStudent">
                  <div class="up__ficha-item">
                    <span class="up__ficha-label"><Layers :size="13" /> Carrera</span>
                    <span class="up__ficha-value">{{ form.CarreraNombre || 'No asignada' }}</span>
                  </div>
                  <div class="up__ficha-item">
                    <span class="up__ficha-label"><KeyRound :size="13" /> Modalidad</span>
                    <span class="up__ficha-value">{{ form.ModalidadNombre || 'No asignada' }}</span>
                  </div>
                </template>
              </div>

              <footer class="up__ficha-foot">
                <Info :size="14" />
                <span>Estos datos son administrados por el sistema. Para solicitar un cambio contacta al administrador.</span>
              </footer>
            </AppCard>

            <AppCard padding="lg" class="up__contact">
              <header class="up__contact-head">
                <div class="up__contact-title">
                  <Phone :size="20" />
                  <h3>Datos de contacto</h3>
                </div>
                <p class="up__contact-hint">Edita tu información personal. Estos campos son privados y solo tú puedes modificarlos.</p>
              </header>

              <form class="up__form" @submit.prevent="updateProfile">
                <div class="up__contact-grid">
                  <AppInput
                    v-model="form.CorreoPersonal"
                    label="Correo personal"
                    type="email"
                    :icon="AtSign"
                    autocomplete="email"
                    placeholder="tu-correo-personal@ejemplo.com"
                    hint="Opcional — se usará como canal de respaldo para notificaciones importantes."
                  />
                  <AppInput
                    v-model="form.Telefono"
                    label="Teléfono"
                    :icon="Phone"
                    required
                    autocomplete="tel"
                    placeholder="Ej. 70123456"
                  />
                </div>

                <div class="up__actions">
                  <AppButton type="submit" variant="primary" :icon="Save" :loading="submitting">
                    Guardar cambios
                  </AppButton>
                </div>
              </form>
            </AppCard>
          </div>

          <div v-else class="up__panel">
            <form class="up__form" @submit.prevent="changePassword">
              <AppAlert variant="info" title="Recomendación">
                Usa una contraseña de al menos 8 caracteres, combinando letras, números y símbolos.
              </AppAlert>

              <div class="up__security">
                <div class="up__field">
                  <AppInput
                    v-model="passwordForm.password_actual"
                    label="Contraseña actual"
                    :type="showCurrent ? 'text' : 'password'"
                    :icon="Lock"
                    required
                    placeholder="Escribe tu contraseña actual"
                    autocomplete="current-password"
                  />
                  <button
                    type="button"
                    class="up__toggle"
                    :aria-label="showCurrent ? 'Ocultar contraseña actual' : 'Mostrar contraseña actual'"
                    @click="showCurrent = !showCurrent"
                  >
                    <component :is="showCurrent ? EyeOff : Eye" :size="16" />
                  </button>
                </div>

                <div class="up__field">
                  <AppInput
                    v-model="passwordForm.contrasena"
                    label="Nueva contraseña"
                    :type="showNew ? 'text' : 'password'"
                    :icon="KeyRound"
                    required
                    minlength="6"
                    hint="Mínimo 6 caracteres"
                    placeholder="Define tu nueva contraseña"
                    autocomplete="new-password"
                  />
                  <button
                    type="button"
                    class="up__toggle"
                    :aria-label="showNew ? 'Ocultar nueva contraseña' : 'Mostrar nueva contraseña'"
                    @click="showNew = !showNew"
                  >
                    <component :is="showNew ? EyeOff : Eye" :size="16" />
                  </button>
                </div>

                <div class="up__field">
                  <AppInput
                    v-model="passwordForm.contrasena_confirmation"
                    label="Confirmar nueva contraseña"
                    :type="showConfirm ? 'text' : 'password'"
                    :icon="KeyRound"
                    required
                    placeholder="Repite la nueva contraseña"
                    autocomplete="new-password"
                  />
                  <button
                    type="button"
                    class="up__toggle"
                    :aria-label="showConfirm ? 'Ocultar confirmación' : 'Mostrar confirmación'"
                    @click="showConfirm = !showConfirm"
                  >
                    <component :is="showConfirm ? EyeOff : Eye" :size="16" />
                  </button>
                </div>
              </div>

              <div class="up__actions">
                <AppButton type="submit" variant="primary" :icon="KeyRound" :loading="submitting">
                  Cambiar contraseña
                </AppButton>
              </div>
            </form>
          </div>
        </AppCard>
      </div>
    </PageTransition>
  </AppShell>
</template>

<style scoped>
.up {
  display: flex;
  flex-direction: column;
  gap: 24px;
  max-width: 960px;
  margin: 0 auto;
}

.up__card {
  overflow: hidden;
}

.up__hero {
  display: flex;
  align-items: center;
  gap: 18px;
  padding: 26px 28px;
  background:
    radial-gradient(circle at top right, rgba(99, 102, 241, 0.18) 0%, transparent 60%),
    linear-gradient(180deg, rgba(28, 39, 66, 0.5) 0%, rgba(19, 28, 48, 0.6) 100%);
  border-bottom: 1px solid var(--color-border-subtle);
}

.up__hero-text h2 {
  margin: 0 0 6px;
  font-size: 1.4rem;
  font-weight: 800;
  color: var(--color-text-primary);
  letter-spacing: -0.01em;
}

.up__hero-meta {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.up__hero-mail {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.85rem;
  color: var(--color-text-muted);
}

.up__tabs {
  display: flex;
  border-bottom: 1px solid var(--color-border-subtle);
  background: rgba(28, 39, 66, 0.25);
  padding: 0 8px;
  gap: 4px;
  overflow-x: auto;
}

.up__tab {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 14px 20px;
  background: transparent;
  border: 0;
  border-bottom: 2px solid transparent;
  color: var(--color-text-muted);
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: all var(--duration-fast) var(--ease-out);
  white-space: nowrap;
  min-height: 48px;
}

.up__tab:hover {
  color: var(--color-text-primary);
  background: rgba(99, 102, 241, 0.05);
}

.up__tab--active {
  color: var(--color-primary);
  border-bottom-color: var(--color-primary);
}

.up__panel {
  padding: 24px 28px 28px;
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.up__form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.up__security {
  display: flex;
  flex-direction: column;
  gap: 14px;
  max-width: 520px;
}

.up__field {
  position: relative;
}

.up__toggle {
  position: absolute;
  right: 10px;
  bottom: 10px;
  display: grid;
  place-items: center;
  width: 36px;
  height: 36px;
  background: transparent;
  border: 0;
  color: var(--color-text-muted);
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: all var(--duration-fast) var(--ease-out);
}

.up__toggle:hover {
  color: var(--color-primary);
  background: var(--color-surface-3);
}

.up__ficha {
  background:
    linear-gradient(180deg, rgba(28, 39, 66, 0.55) 0%, rgba(19, 28, 48, 0.6) 100%);
  border: 1px solid var(--color-border-subtle);
}

.up__ficha-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 16px;
  padding-bottom: 14px;
  border-bottom: 1px solid var(--color-border-subtle);
}

.up__ficha-title {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  color: var(--color-primary);
}

.up__ficha-title h3 {
  margin: 0;
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--color-text-primary);
}

.up__ficha-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 16px 22px;
}

.up__ficha-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
  min-width: 0;
}

.up__ficha-label {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--color-text-muted);
  font-weight: 600;
}

.up__ficha-value {
  font-size: 0.98rem;
  font-weight: 600;
  color: var(--color-text-primary);
  line-height: 1.4;
  word-break: break-word;
}

.up__ficha-value--mono {
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
  font-size: 0.9rem;
}

.up__ficha-foot {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 18px;
  padding-top: 14px;
  border-top: 1px dashed var(--color-border-subtle);
  font-size: 0.82rem;
  color: var(--color-text-muted);
  line-height: 1.4;
}

.up__contact {
  background: linear-gradient(180deg, rgba(28, 39, 66, 0.45) 0%, rgba(19, 28, 48, 0.55) 100%);
  border: 1px solid var(--color-border-subtle);
}

.up__contact-head {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 16px;
  padding-bottom: 14px;
  border-bottom: 1px solid var(--color-border-subtle);
}

.up__contact-title {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  color: var(--color-success);
}

.up__contact-title h3 {
  margin: 0;
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--color-text-primary);
}

.up__contact-hint {
  margin: 0;
  font-size: 0.85rem;
  color: var(--color-text-muted);
  line-height: 1.4;
}

.up__contact-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 14px;
}

.up__actions {
  display: flex;
  justify-content: flex-end;
  padding-top: 12px;
  border-top: 1px solid var(--color-border-subtle);
}

@media (max-width: 768px) {
  .up__hero { flex-direction: column; align-items: flex-start; text-align: left; padding: 22px; }
  .up__ficha-grid { grid-template-columns: 1fr; }
  .up__contact-grid { grid-template-columns: 1fr; }
  .up__panel { padding: 18px; }
  .up__security { max-width: 100%; }
}
</style>
