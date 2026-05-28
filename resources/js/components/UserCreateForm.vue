<template>
  <section class="create-form">
    <header class="topbar">
      <div>
        <p class="eyebrow">RF01</p>
        <h1>Registro de usuarios</h1>
        <p class="lead">{{ roleName || 'Solo administradores pueden registrar usuarios.' }}</p>
      </div>
      <button class="back" type="button" @click="goBack">Volver al panel</button>
    </header>

    <div v-if="message" class="alert" :class="alertClass">{{ message }}</div>

    <form class="panel" @submit.prevent="submit">
      <div class="grid">
        <label>
          Rol
          <select v-model="form.IdRol" required>
            <option disabled value="">Selecciona un rol</option>
            <option v-for="role in roles" :key="role.IdRol" :value="role.IdRol">
              {{ role.Nombre }}
            </option>
          </select>
        </label>

        <label>
          Estado
          <select v-model="form.Estado" required>
            <option :value="true">Activo</option>
            <option :value="false">Inactivo</option>
          </select>
        </label>

        <label>
          Primer nombre
          <input v-model="form.Nombre1" type="text" maxlength="50" required />
        </label>

        <label>
          Segundo nombre
          <input v-model="form.Nombre2" type="text" maxlength="50" />
        </label>

        <label>
          Primer apellido
          <input v-model="form.Apellido1" type="text" maxlength="50" required />
        </label>

        <label>
          Segundo apellido
          <input v-model="form.Apellido2" type="text" maxlength="50" />
        </label>

        <label>
          CI
          <input v-model="form.CI" type="number" required />
        </label>

        <label>
          Teléfono
          <input v-model="form.Telefono" type="number" />
        </label>

        <label>
          Correo
          <input v-model="form.Correo" type="email" required />
        </label>

        <label>
          Contraseña
          <input v-model="form.Contrasena" type="password" minlength="6" required />
        </label>

        <label>
          Confirmar contraseña
          <input v-model="form.Contrasena_confirmation" type="password" minlength="6" required />
        </label>

        <label>
          Carrera
          <input v-model="form.IdCarrera" type="number" placeholder="Opcional según el rol" />
        </label>

        <label>
          Semestre
          <input v-model="form.Semestre" type="number" placeholder="Opcional según el rol" />
        </label>
      </div>

      <button class="submit" type="submit" :disabled="loading">
        {{ loading ? 'Registrando...' : 'Registrar usuario' }}
      </button>
    </form>
  </section>
</template>

<script>
import { computed, onMounted, reactive, ref } from 'vue';
import axios from 'axios';

export default {
  name: 'UserCreateForm',
  setup() {
    const loading = ref(false);
    const message = ref('');
    const messageType = ref('success');
    const roles = ref([]);
    const roleName = ref('');

    const form = reactive({
      IdRol: '',
      Nombre1: '',
      Nombre2: '',
      Apellido1: '',
      Apellido2: '',
      CI: '',
      Telefono: '',
      Correo: '',
      Contrasena: '',
      Contrasena_confirmation: '',
      IdCarrera: '',
      Semestre: '',
      Estado: true,
    });

    const alertClass = computed(() => messageType.value === 'success' ? 'alert--success' : 'alert--error');

    const goBack = () => {
      window.location.href = '/index';
    };

    const loadProfile = async () => {
      const token = localStorage.getItem('auth_token');

      if (!token) {
        window.location.href = '/';
        return false;
      }

      axios.defaults.headers.common.Authorization = `Bearer ${token}`;

      const response = await axios.get('/api/auth/perfil');
      const user = response.data.data.user;

      if (user?.IdRol !== 1) {
        messageType.value = 'error';
        message.value = 'No tienes permisos para registrar usuarios.';
        setTimeout(() => {
          window.location.href = '/index';
        }, 1500);
        return false;
      }

      roleName.value = user?.Rol?.Nombre || 'Administrador';
      return true;
    };

    const loadRoles = async () => {
      const response = await axios.get('/api/usuarios/form-data');
      roles.value = response.data.data.roles;
    };

    onMounted(async () => {
      try {
        const ok = await loadProfile();
        if (!ok) return;
        await loadRoles();
      } catch (error) {
        console.error(error);
        messageType.value = 'error';
        message.value = error.response?.data?.message ?? 'No se pudieron cargar los datos del formulario.';
      }
    });

    const submit = async () => {
      try {
        loading.value = true;
        message.value = '';

        const payload = {
          ...form,
          IdRol: Number(form.IdRol),
          CI: Number(form.CI),
          Telefono: form.Telefono ? Number(form.Telefono) : null,
          IdCarrera: form.IdCarrera ? Number(form.IdCarrera) : null,
          Semestre: form.Semestre ? Number(form.Semestre) : null,
          Estado: Boolean(form.Estado),
        };

        const response = await axios.post('/api/usuarios', payload);

        messageType.value = 'success';
        message.value = response.data.message;

        form.IdRol = '';
        form.Nombre1 = '';
        form.Nombre2 = '';
        form.Apellido1 = '';
        form.Apellido2 = '';
        form.CI = '';
        form.Telefono = '';
        form.Correo = '';
        form.Contrasena = '';
        form.Contrasena_confirmation = '';
        form.IdCarrera = '';
        form.Semestre = '';
        form.Estado = true;
      } catch (error) {
        console.error(error);
        messageType.value = 'error';
        message.value = error.response?.data?.message ?? 'No se pudo registrar el usuario.';
      } finally {
        loading.value = false;
      }
    };

    return {
      form,
      roles,
      roleName,
      loading,
      message,
      alertClass,
      submit,
      goBack,
    };
  },
};
</script>

<style scoped>
.create-form {
  display: grid;
  gap: 1rem;
  color: #e5e7eb;
}

.topbar {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  align-items: flex-start;
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
  font-size: 1.9rem;
}

.lead {
  margin: 0.5rem 0 0;
  color: #cbd5e1;
}

.back,
.submit {
  border: 0;
  border-radius: 999px;
  padding: 0.9rem 1.2rem;
  font-weight: 700;
  cursor: pointer;
}

.back {
  background: rgba(255, 255, 255, 0.08);
  color: #e5e7eb;
}

.submit {
  background: linear-gradient(135deg, #f97316, #fbbf24);
  color: #111827;
}

.panel {
  padding: 1.25rem;
  border-radius: 22px;
  background: rgba(15, 23, 42, 0.72);
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
}

.grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  margin-bottom: 1rem;
}

label {
  display: grid;
  gap: 0.4rem;
  font-size: 0.95rem;
}

input,
select {
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 14px;
  padding: 0.85rem 0.95rem;
  background: rgba(255, 255, 255, 0.05);
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
</style>
