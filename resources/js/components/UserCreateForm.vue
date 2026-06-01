<template>
  <section class="user-create">
    <header class="user-create__header">
      <div>
        <p class="eyebrow">RF01</p>
        <h1>Registro de usuarios</h1>
        <p>Alta de nuevos usuarios con selección de rol y validación de datos.</p>
      </div>
      <a class="back-link" href="/index">Volver al panel</a>
    </header>

    <div v-if="message" class="alert" :class="`alert--${messageType}`">
      {{ message }}
    </div>

    <form class="user-form" @submit.prevent="submit">
      <div class="grid">
        <label>
          Rol
          <select v-model="form.IdRol" required @change="onRoleChange">
            <option value="" disabled>Seleccione un rol</option>
            <option v-for="rol in roles" :key="rol.IdRol" :value="rol.IdRol">
              {{ rol.Nombre || rol.Nombre1 || `Rol ${rol.IdRol}` }}
            </option>
          </select>
        </label>

        <label v-if="isStudent">
          Carrera
          <select v-model="form.IdCarrera" required>
            <option value="" disabled>Seleccione una carrera</option>
            <option v-for="carrera in carreras" :key="carrera.IdCarrera" :value="carrera.IdCarrera">
              {{ carrera.Nombre || carrera.Descripcion || `Carrera ${carrera.IdCarrera}` }}
            </option>
          </select>
        </label>

        <label v-if="isStudent">
          Modalidad
          <select v-model="form.IdModalidad" required>
            <option value="" disabled>Seleccione una modalidad</option>
            <option v-for="modalidad in modalidades" :key="modalidad.IdModalidad" :value="modalidad.IdModalidad">
              {{ modalidad.Nombre || modalidad.Descripcion || `Modalidad ${modalidad.IdModalidad}` }}
            </option>
          </select>
        </label>

        <label>
          Correo
          <input v-model.trim="form.Correo" type="email" autocomplete="email" required />
        </label>

        <label>
          CI
          <input v-model.trim="form.CI" type="text" autocomplete="off" required />
        </label>

        <label>
          Teléfono
          <input v-model.trim="form.Telefono" type="text" autocomplete="off" required />
        </label>

        <label>
          Nombre 1
          <input v-model.trim="form.Nombre1" type="text" autocomplete="given-name" required />
        </label>

        <label>
          Nombre 2
          <input v-model.trim="form.Nombre2" type="text" autocomplete="additional-name" />
        </label>

        <label>
          Apellido 1
          <input v-model.trim="form.Apellido1" type="text" autocomplete="family-name" required />
        </label>

        <label>
          Apellido 2
          <input v-model.trim="form.Apellido2" type="text" autocomplete="family-name" />
        </label>

        <label>
          Contraseña
          <input v-model="form.Contrasena" type="password" autocomplete="new-password" required />
        </label>

        <label>
          Confirmar contraseña
          <input v-model="form.Contrasena_confirmation" type="password" autocomplete="new-password" required />
        </label>
      </div>

      <div class="actions">
        <button type="submit" :disabled="submitting">
          {{ submitting ? 'Registrando...' : 'Registrar usuario' }}
        </button>
        <button type="button" class="secondary" @click="reset">Limpiar</button>
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
      roles: [],
      carreras: [],
      modalidades: [],
      submitting: false,
      message: '',
      messageType: 'error',
      form: {
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
      },
    };
  },
  computed: {
    isStudent() {
      return Number(this.form.IdRol) === 3;
    },
  },
  mounted() {
    this.loadFormData();
  },
  methods: {
    async loadFormData() {
      try {
        const token = localStorage.getItem('auth_token');
        const { data } = await axios.get('/api/usuarios/form-data', {
          headers: token ? { Authorization: `Bearer ${token}` } : {},
        });

        this.roles = data?.roles ?? data?.data?.roles ?? [];
        this.carreras = data?.carreras ?? data?.data?.carreras ?? [];
        this.modalidades = data?.modalidades ?? data?.data?.modalidades ?? [];
      } catch (error) {
        this.setMessage(error?.response?.data?.message || 'No se pudieron cargar los datos del formulario.', 'error');
      }
    },
    onRoleChange() {
      if (!this.isStudent) {
        this.form.IdCarrera = '';
        this.form.IdModalidad = '';
      }
    },
    async submit() {
      this.submitting = true;
      this.message = '';

      try {
        const token = localStorage.getItem('auth_token');
        const payload = {
          ...this.form,
          IdRol: Number(this.form.IdRol),
          IdCarrera: this.isStudent ? Number(this.form.IdCarrera) : null,
          IdModalidad: this.isStudent ? Number(this.form.IdModalidad) : null,
        };

        const { data } = await axios.post('/api/usuarios', payload, {
          headers: token ? { Authorization: `Bearer ${token}` } : {},
        });

        this.setMessage(data?.message || 'Usuario registrado correctamente.', 'success');
        this.reset(false);
      } catch (error) {
        const responseMessage = error?.response?.data?.message;
        this.setMessage(responseMessage || 'No se pudo registrar el usuario.', 'error');
      } finally {
        this.submitting = false;
      }
    },
    reset(clearMessage = true) {
      this.form = {
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
      };
      if (clearMessage) {
        this.message = '';
      }
    },
    setMessage(message, type = 'error') {
      this.message = message;
      this.messageType = type;
    },
  },
};
</script>

<style scoped>
.user-create { min-height: 100vh; padding: 32px; background: linear-gradient(180deg, #07111f 0%, #101b2b 100%); color: #eef2ff; }
.user-create__header { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; margin-bottom:24px; }
.eyebrow { margin:0 0 8px; color:#fbbf24; text-transform:uppercase; letter-spacing:.18em; font-size:.75rem; }
h1 { margin:0; font-size:2rem; }
p { margin:8px 0 0; color:#cbd5e1; }
.back-link, button { border-radius:999px; padding:12px 18px; font-weight:700; text-decoration:none; border:none; cursor:pointer; }
.back-link { background:#f59e0b; color:#0f172a; }
.alert { margin:20px 0; padding:16px 18px; border-radius:16px; }
.alert--success { background:rgba(16,185,129,.16); color:#d1fae5; }
.alert--error { background:rgba(239,68,68,.16); color:#fecaca; }
.user-form { background:rgba(15,23,42,.86); border:1px solid rgba(148,163,184,.18); border-radius:24px; padding:24px; box-shadow:0 20px 60px rgba(0,0,0,.25); }
.grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; }
label { display:flex; flex-direction:column; gap:8px; font-weight:600; color:#e2e8f0; }
input, select { border-radius:14px; border:1px solid rgba(148,163,184,.22); background:rgba(30,41,59,.82); color:#f8fafc; padding:14px 16px; font-size:1rem; outline:none; }
input:focus, select:focus { border-color:#f59e0b; box-shadow:0 0 0 3px rgba(245,158,11,.18); }
.actions { display:flex; gap:12px; margin-top:22px; }
button { background:#f59e0b; color:#0f172a; }
button.secondary { background:transparent; border:1px solid rgba(148,163,184,.22); color:#e2e8f0; }
button:disabled { opacity:.7; cursor:not-allowed; }
@media (max-width: 900px) { .grid { grid-template-columns:1fr; } .user-create__header { flex-direction:column; } }
</style>
