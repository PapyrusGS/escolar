<template>
  <section class="page">
    <header class="page__header">
      <div>
        <p class="eyebrow">RF01</p>
        <h1>Registro de usuarios</h1>
        <p class="lead">Alta de nuevos usuarios con selección de rol y validación de datos.</p>
      </div>
      <a class="back-link" href="/index">Volver al panel</a>
    </header>

    <div v-if="message" :class="['alert', messageType === 'success' ? 'alert--success' : 'alert--error']">
      {{ message }}
    </div>

    <form class="panel" @submit.prevent="submit">
      <div class="grid">
        <label class="field">
          <span>Rol</span>
          <select v-model="form.IdRol" class="input">
            <option value="">Seleccione un rol</option>
            <option v-for="role in roles" :key="role.IdRol" :value="role.IdRol">
              {{ role.Nombre }}
            </option>
          </select>
          <small v-if="errors.IdRol" class="error">{{ errors.IdRol[0] }}</small>
        </label>

        <label class="field">
          <span>Correo</span>
          <input v-model="form.Correo" class="input" type="email">
          <small v-if="errors.Correo" class="error">{{ errors.Correo[0] }}</small>
        </label>

        <label class="field">
          <span>CI</span>
          <input v-model="form.CI" class="input" type="number">
          <small v-if="errors.CI" class="error">{{ errors.CI[0] }}</small>
        </label>

        <label class="field">
          <span>Teléfono</span>
          <input v-model="form.Telefono" class="input" type="number">
          <small v-if="errors.Telefono" class="error">{{ errors.Telefono[0] }}</small>
        </label>

        <label class="field">
          <span>Nombre 1</span>
          <input v-model="form.Nombre1" class="input" type="text">
          <small v-if="errors.Nombre1" class="error">{{ errors.Nombre1[0] }}</small>
        </label>

        <label class="field">
          <span>Nombre 2</span>
          <input v-model="form.Nombre2" class="input" type="text">
        </label>

        <label class="field">
          <span>Apellido 1</span>
          <input v-model="form.Apellido1" class="input" type="text">
          <small v-if="errors.Apellido1" class="error">{{ errors.Apellido1[0] }}</small>
        </label>

        <label class="field">
          <span>Apellido 2</span>
          <input v-model="form.Apellido2" class="input" type="text">
        </label>

        <label class="field">
          <span>Contraseña</span>
          <input v-model="form.Contrasena" class="input" type="password">
          <small v-if="errors.Contrasena" class="error">{{ errors.Contrasena[0] }}</small>
        </label>

        <label class="field">
          <span>Confirmar contraseña</span>
          <input v-model="form.Contrasena_confirmation" class="input" type="password">
        </label>

        <label class="field field--full">
          <span class="checkbox">
            <input v-model="form.Estado" type="checkbox">
            Usuario activo
          </span>
        </label>
      </div>

      <div class="actions">
        <button class="button" type="submit" :disabled="loading">
          {{ loading ? 'Registrando...' : 'Registrar usuario' }}
        </button>
        <button class="button button--ghost" type="button" @click="resetForm" :disabled="loading">Limpiar</button>
      </div>
    </form>
  </section>
</template>

<script>
import axios from 'axios';

export default {
  name: 'UserCreateForm',
  data() {
    return {
      loading: false,
      roles: [],
      errors: {},
      message: '',
      messageType: 'success',
      form: {
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
        Estado: true,
      },
    };
  },
  mounted() {
    const token = localStorage.getItem('auth_token');

    if (!token) {
      window.location.href = '/';
      return;
    }

    axios.defaults.headers.common.Authorization = `Bearer ${token}`;
    this.loadFormData();
  },
  methods: {
    async loadFormData() {
      try {
        const response = await axios.get('/api/usuarios/form-data');
        this.roles = response.data.data.roles || [];
      } catch (error) {
        console.error(error);
        this.message = error.response?.data?.message || 'No se pudo cargar el formulario.';
        this.messageType = 'error';
      }
    },
    async submit() {
      try {
        this.loading = true;
        this.errors = {};
        this.message = '';

        const payload = {
          ...this.form,
          IdRol: Number(this.form.IdRol),
          CI: this.form.CI ? Number(this.form.CI) : '',
          Telefono: this.form.Telefono ? Number(this.form.Telefono) : null,
        };

        const response = await axios.post('/api/usuarios', payload);
        this.message = response.data.message || 'Usuario registrado correctamente.';
        this.messageType = 'success';
        this.resetForm(false);
      } catch (error) {
        console.error(error);
        this.message = error.response?.data?.message || 'No se pudo registrar el usuario.';
        this.messageType = 'error';
        this.errors = error.response?.data?.data || {};
      } finally {
        this.loading = false;
      }
    },
    resetForm(clearMessage = true) {
      this.form = {
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
        Estado: true,
      };

      if (clearMessage) {
        this.message = '';
        this.errors = {};
      }
    },
  },
};
</script>

<style scoped>
.page {
  display: grid;
  gap: 1.25rem;
  color: #e5e7eb;
}

.page__header {
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

h1, p {
  margin: 0;
}

h1 {
  font-size: 2rem;
}

.lead {
  margin-top: 0.5rem;
  color: #cbd5e1;
}

.back-link {
  align-self: center;
  text-decoration: none;
  color: #111827;
  background: linear-gradient(135deg, #fbbf24, #f97316);
  font-weight: 700;
  padding: 0.8rem 1rem;
  border-radius: 999px;
}

.alert {
  border-radius: 16px;
  padding: 0.9rem 1rem;
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

.panel {
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 20px;
  padding: 1rem;
}

.grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.9rem;
}

.field {
  display: grid;
  gap: 0.35rem;
}

.field--full {
  grid-column: 1 / -1;
}

.checkbox {
  display: flex;
  gap: 0.55rem;
  align-items: center;
}

.input,
.button {
  border-radius: 14px;
  border: 1px solid rgba(255, 255, 255, 0.08);
  background: rgba(255, 255, 255, 0.06);
  color: #e5e7eb;
  padding: 0.85rem 1rem;
}

.button {
  cursor: pointer;
  font-weight: 700;
}

.button--ghost {
  background: transparent;
}

.actions {
  display: flex;
  gap: 0.75rem;
  margin-top: 1rem;
}

.error {
  color: #fca5a5;
}

@media (max-width: 900px) {
  .page__header,
  .grid {
    grid-template-columns: 1fr;
    display: grid;
  }
}
</style>
