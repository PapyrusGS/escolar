<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { UserPlus, RotateCcw, Save, ArrowLeft } from '@lucide/vue';
import AppShell from './layout/AppShell.vue';
import PageTransition from './layout/PageTransition.vue';
import AppCard from './ui/AppCard.vue';
import AppButton from './ui/AppButton.vue';
import AppInput from './ui/AppInput.vue';
import AppSelect from './ui/AppSelect.vue';
import AppAlert from './ui/AppAlert.vue';
import AppPageHeader from './ui/AppPageHeader.vue';
import { toast } from '../lib/toast.js';

const user = ref(null);
const roles = ref([]);
const carreras = ref([]);
const modalidades = ref([]);
const submitting = ref(false);
const successMsg = ref('');

const form = ref({
  IdRol: '',
  IdCarrera: '',
  IdModalidad: '',
  Correo: '',
  CI: '',
  Telefono: '',
  Nombre1: '',
  Nombre2: '',
  Apellido1: '',
  Apellido2: '',
  Contrasena: '',
  Contrasena_confirmation: '',
});

const isStudent = computed(() => Number(form.value.IdRol) === 3);

onMounted(async () => {
  const stored = localStorage.getItem('auth_user');
  if (stored) user.value = JSON.parse(stored);
  await loadFormData();
});

const loadFormData = async () => {
  try {
    const token = localStorage.getItem('auth_token');
    const { data } = await axios.get('/api/usuarios/form-data', {
      headers: token ? { Authorization: `Bearer ${token}` } : {},
    });
    roles.value = (data?.roles ?? data?.data?.roles ?? []).map((r) => ({ Id: r.IdRol, Nombre: r.Nombre || r.Nombre1 || `Rol ${r.IdRol}` }));
    carreras.value = (data?.carreras ?? data?.data?.carreras ?? []).map((c) => ({ Id: c.IdCarrera, Nombre: c.Nombre || c.Descripcion || `Carrera ${c.IdCarrera}` }));
    modalidades.value = (data?.modalidades ?? data?.data?.modalidades ?? []).map((m) => ({ Id: m.IdModalidad, Nombre: m.Nombre || m.Descripcion || `Modalidad ${m.IdModalidad}` }));
  } catch (err) {
    toast.error('No se pudieron cargar los datos del formulario');
  }
};

const onRoleChange = () => {
  if (!isStudent.value) {
    form.value.IdCarrera = '';
    form.value.IdModalidad = '';
  }
};

const submit = async () => {
  submitting.value = true;
  successMsg.value = '';
  try {
    const token = localStorage.getItem('auth_token');
    const payload = {
      ...form.value,
      IdRol: Number(form.value.IdRol),
      IdCarrera: isStudent.value ? Number(form.value.IdCarrera) : null,
      IdModalidad: isStudent.value ? Number(form.value.IdModalidad) : null,
    };
    const { data } = await axios.post('/api/usuarios', payload, {
      headers: token ? { Authorization: `Bearer ${token}` } : {},
    });
    toast.success(data?.message || 'Usuario registrado correctamente');
    successMsg.value = data?.message || 'Usuario registrado correctamente';
    reset(false);
  } catch (err) {
    toast.error(err.response?.data?.message || 'No se pudo registrar el usuario');
  } finally {
    submitting.value = false;
  }
};

const reset = (clearMessage = true) => {
  form.value = {
    IdRol: '', IdCarrera: '', IdModalidad: '',
    Correo: '', CI: '', Telefono: '',
    Nombre1: '', Nombre2: '', Apellido1: '', Apellido2: '',
    Contrasena: '', Contrasena_confirmation: '',
  };
  if (clearMessage) successMsg.value = '';
};

const handleLogout = async () => {
  try { if (localStorage.getItem('auth_token')) await axios.post('/api/auth/logout'); } catch {}
  localStorage.clear();
  delete axios.defaults.headers.common.Authorization;
  window.location.href = '/';
};
</script>

<template>
  <AppShell v-if="user" :user="user" page-title="Registrar usuario" @logout="handleLogout">
    <PageTransition>
      <div class="ucf">
        <AppPageHeader
          eyebrow="Módulo administrativo"
          title="Registro de usuarios"
          description="Alta de nuevos usuarios con selección de rol y validación de datos."
        >
          <template #actions>
            <AppButton variant="secondary" :icon="ArrowLeft" @click="window.location.href = '/dashboard'">
              Volver al panel
            </AppButton>
          </template>
        </AppPageHeader>

        <AppAlert v-if="successMsg" variant="success" :title="successMsg" class="ucf__alert" />

        <AppCard padding="lg">
          <form class="ucf__form" @submit.prevent="submit">
            <div class="ucf__grid">
              <AppSelect v-model="form.IdRol" label="Rol" :options="roles" required placeholder="Seleccione un rol" @update:modelValue="onRoleChange" />

              <AppSelect v-if="isStudent" v-model="form.IdCarrera" label="Carrera" :options="carreras" required placeholder="Seleccione una carrera" />
              <AppSelect v-if="isStudent" v-model="form.IdModalidad" label="Modalidad" :options="modalidades" required placeholder="Seleccione una modalidad" />

              <AppInput v-model="form.Correo" label="Correo" type="email" required autocomplete="email" />
              <AppInput v-model="form.CI" label="CI" type="text" required />
              <AppInput v-model="form.Telefono" label="Teléfono" type="text" required />
              <AppInput v-model="form.Nombre1" label="Nombre 1" type="text" required autocomplete="given-name" />
              <AppInput v-model="form.Nombre2" label="Nombre 2" type="text" autocomplete="additional-name" />
              <AppInput v-model="form.Apellido1" label="Apellido 1" type="text" required autocomplete="family-name" />
              <AppInput v-model="form.Apellido2" label="Apellido 2" type="text" autocomplete="family-name" />
              <AppInput v-model="form.Contrasena" label="Contraseña" type="password" required autocomplete="new-password" />
              <AppInput v-model="form.Contrasena_confirmation" label="Confirmar contraseña" type="password" required autocomplete="new-password" />
            </div>

            <div class="ucf__actions">
              <AppButton type="submit" variant="primary" :icon="UserPlus" :loading="submitting">
                Registrar usuario
              </AppButton>
              <AppButton type="button" variant="secondary" :icon="RotateCcw" @click="reset">
                Limpiar
              </AppButton>
            </div>
          </form>
        </AppCard>
      </div>
    </PageTransition>
  </AppShell>
</template>

<style scoped>
.ucf {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.ucf__form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.ucf__grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 16px;
}

.ucf__actions {
  display: flex;
  gap: 10px;
  padding-top: 8px;
  border-top: 1px solid var(--color-border-subtle);
}

@media (max-width: 768px) {
  .ucf__grid {
    grid-template-columns: 1fr;
  }
}
</style>
