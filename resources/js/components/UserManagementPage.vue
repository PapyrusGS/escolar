<template>
  <section class="manager">
    <header class="manager__header">
      <div>
        <p class="eyebrow">RF01</p>
        <h1>Gestión de usuarios</h1>
        <p class="lead">Listado, edición, activación/desactivación y filtros por rol.</p>
      </div>
      <div class="actions">
        <a class="btn" href="/usuarios/create">Nuevo usuario</a>
        <a class="btn btn--ghost" href="/index">Volver al panel</a>
      </div>
    </header>

    <div v-if="message" class="alert" :class="alertClass">{{ message }}</div>

    <section class="toolbar">
      <input v-model="filters.search" class="input" type="text" placeholder="Buscar por nombre, correo o CI" @keyup.enter="loadUsers(1)" />
      <select v-model="filters.IdRol" class="input" @change="loadUsers(1)">
        <option value="">Todos los roles</option>
        <option v-for="role in roles" :key="role.IdRol" :value="role.IdRol">{{ role.Nombre }}</option>
      </select>
      <select v-model="filters.Estado" class="input" @change="loadUsers(1)">
        <option value="">Todos los estados</option>
        <option :value="1">Activos</option>
        <option :value="0">Inactivos</option>
      </select>
      <select v-model="perPage" class="input" @change="loadUsers(1)">
        <option :value="5">5 por página</option>
        <option :value="10">10 por página</option>
        <option :value="15">15 por página</option>
      </select>
      <button class="btn" type="button" @click="loadUsers(1)">Filtrar</button>
    </section>

    <section class="table-card">
      <table>
        <thead>
          <tr>
            <th>Usuario</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="usuario in usuarios" :key="usuario.IdUsuario">
            <td>{{ usuario.Nombre1 }} {{ usuario.Apellido1 }}</td>
            <td>{{ usuario.Correo }}</td>
            <td>{{ usuario.Rol?.Nombre || 'Sin rol' }}</td>
            <td>
              <span class="badge" :class="usuario.Estado ? 'badge--active' : 'badge--inactive'">
                {{ usuario.Estado ? 'Activo' : 'Inactivo' }}
              </span>
            </td>
            <td>
              <div class="row-actions">
                <button class="mini-btn" @click="openEdit(usuario)">Editar</button>
                <button class="mini-btn mini-btn--warn" @click="toggleEstado(usuario)">
                  {{ usuario.Estado ? 'Desactivar' : 'Activar' }}
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="!usuarios.length">
            <td colspan="5" class="empty">No hay usuarios para mostrar.</td>
          </tr>
        </tbody>
      </table>

      <div class="pagination">
        <span>Mostrando {{ pagination.from || 0 }} - {{ pagination.to || 0 }} de {{ pagination.total || 0 }}</span>
        <div class="pagination__actions">
          <button class="mini-btn" :disabled="pagination.current_page <= 1" @click="loadUsers(pagination.current_page - 1)">Anterior</button>
          <button class="mini-btn" :disabled="pagination.current_page >= pagination.last_page" @click="loadUsers(pagination.current_page + 1)">Siguiente</button>
        </div>
      </div>
    </section>

    <div v-if="editing" class="modal">
      <div class="modal__backdrop" @click="closeEdit"></div>
      <div class="modal__panel">
        <header class="modal__header">
          <div>
            <p class="eyebrow">Editar usuario</p>
            <h2>Actualizar registro</h2>
          </div>
          <button class="mini-btn" @click="closeEdit">Cerrar</button>
        </header>

        <form class="form" @submit.prevent="saveUser">
          <div class="grid">
            <label>Rol<select v-model="form.IdRol" required><option value="">Selecciona</option><option v-for="role in roles" :key="role.IdRol" :value="role.IdRol">{{ role.Nombre }}</option></select></label>
            <label>Estado<select v-model="form.Estado" required><option :value="true">Activo</option><option :value="false">Inactivo</option></select></label>
            <label>Primer nombre<input v-model="form.Nombre1" type="text" maxlength="50" required /></label>
            <label>Segundo nombre<input v-model="form.Nombre2" type="text" maxlength="50" /></label>
            <label>Primer apellido<input v-model="form.Apellido1" type="text" maxlength="50" required /></label>
            <label>Segundo apellido<input v-model="form.Apellido2" type="text" maxlength="50" /></label>
            <label>CI<input v-model="form.CI" type="number" required /></label>
            <label>Teléfono<input v-model="form.Telefono" type="number" /></label>
            <label>Correo<input v-model="form.Correo" type="email" required /></label>
            <label>Contraseña<input v-model="form.Contrasena" type="password" minlength="6" placeholder="Dejar vacío para no cambiar" /></label>
            <label>Confirmar contraseña<input v-model="form.Contrasena_confirmation" type="password" minlength="6" /></label>
            <label>Carrera<input v-model="form.IdCarrera" type="number" /></label>
            <label>Semestre<input v-model="form.Semestre" type="number" /></label>
          </div>

          <div class="modal__footer">
            <button class="mini-btn" type="button" @click="closeEdit">Cancelar</button>
            <button class="btn" type="submit" :disabled="saving">{{ saving ? 'Guardando...' : 'Guardar cambios' }}</button>
          </div>
        </form>
      </div>
    </div>
  </section>
</template>

<script>
import { computed, onMounted, reactive, ref } from 'vue';
import axios from 'axios';

export default {
  name: 'UserManagementPage',
  setup() {
    const usuarios = ref([]);
    const roles = ref([]);
    const pagination = ref({});
    const message = ref('');
    const messageType = ref('success');
    const loading = ref(false);
    const saving = ref(false);
    const editing = ref(false);
    const perPage = ref(10);

    const filters = reactive({ search: '', IdRol: '', Estado: '' });
    const form = reactive({
      IdUsuario: null,
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

    const alertClass = computed(() => (messageType.value === 'success' ? 'alert--success' : 'alert--error'));

    const loadProfile = async () => {
      const token = localStorage.getItem('auth_token');
      if (!token) {
        window.location.href = '/';
        return false;
      }

      axios.defaults.headers.common.Authorization = `Bearer ${token}`;
      const response = await axios.get('/api/auth/perfil');
      if (response.data.data.user?.IdRol !== 1) {
        window.location.href = '/index';
        return false;
      }
      return true;
    };

    const loadUsers = async (page = 1) => {
      try {
        loading.value = true;
        message.value = '';

        const response = await axios.get('/api/usuarios', {
          params: { page, per_page: perPage.value, ...filters },
        });

        usuarios.value = response.data.data.usuarios.data;
        roles.value = response.data.data.roles;
        pagination.value = response.data.data.usuarios;
      } catch (error) {
        console.error(error);
        messageType.value = 'error';
        message.value = error.response?.data?.message ?? 'No se pudo cargar el listado.';
      } finally {
        loading.value = false;
      }
    };

    const openEdit = (usuario) => {
      editing.value = true;
      form.IdUsuario = usuario.IdUsuario;
      form.IdRol = usuario.IdRol;
      form.Nombre1 = usuario.Nombre1;
      form.Nombre2 = usuario.Nombre2 ?? '';
      form.Apellido1 = usuario.Apellido1;
      form.Apellido2 = usuario.Apellido2 ?? '';
      form.CI = usuario.CI;
      form.Telefono = usuario.Telefono ?? '';
      form.Correo = usuario.Correo;
      form.Contrasena = '';
      form.Contrasena_confirmation = '';
      form.IdCarrera = usuario.IdCarrera ?? '';
      form.Semestre = usuario.Semestre ?? '';
      form.Estado = Boolean(usuario.Estado);
    };

    const closeEdit = () => {
      editing.value = false;
    };

    const saveUser = async () => {
      try {
        saving.value = true;
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

        if (!payload.Contrasena) {
          delete payload.Contrasena;
          delete payload.Contrasena_confirmation;
        }

        const response = await axios.put(`/api/usuarios/${form.IdUsuario}`, payload);
        messageType.value = 'success';
        message.value = response.data.message;
        editing.value = false;
        await loadUsers(pagination.value.current_page || 1);
      } catch (error) {
        console.error(error);
        messageType.value = 'error';
        message.value = error.response?.data?.message ?? 'No se pudo actualizar el usuario.';
      } finally {
        saving.value = false;
      }
    };

    const toggleEstado = async (usuario) => {
      try {
        message.value = '';
        const response = await axios.patch(`/api/usuarios/${usuario.IdUsuario}/estado`);
        messageType.value = 'success';
        message.value = response.data.message;
        await loadUsers(pagination.value.current_page || 1);
      } catch (error) {
        console.error(error);
        messageType.value = 'error';
        message.value = error.response?.data?.message ?? 'No se pudo cambiar el estado.';
      }
    };

    onMounted(async () => {
      const ok = await loadProfile();
      if (ok) {
        await loadUsers(1);
      }
    });

    return {
      usuarios,
      roles,
      pagination,
      filters,
      perPage,
      form,
      message,
      messageType,
      alertClass,
      loading,
      saving,
      editing,
      loadUsers,
      openEdit,
      closeEdit,
      saveUser,
      toggleEstado,
    };
  },
};
</script>

<style scoped>
.manager {
  display: grid;
  gap: 1rem;
  color: #e5e7eb;
}

.manager__header,
.toolbar,
.pagination,
.modal__header,
.modal__footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.eyebrow {
  margin: 0 0 0.35rem;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  font-size: 0.72rem;
  color: #fbbf24;
}

h1, h2 {
  margin: 0;
}

.lead {
  margin: 0.4rem 0 0;
  color: #cbd5e1;
}

.actions, .row-actions, .pagination__actions {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.btn, .mini-btn {
  border: 0;
  border-radius: 999px;
  padding: 0.8rem 1rem;
  font-weight: 700;
  cursor: pointer;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn {
  background: linear-gradient(135deg, #f97316, #fbbf24);
  color: #111827;
}

.btn--ghost, .mini-btn {
  background: rgba(255, 255, 255, 0.08);
  color: #e5e7eb;
}

.mini-btn--warn {
  background: rgba(251, 191, 36, 0.18);
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

.toolbar {
  flex-wrap: wrap;
}

.input {
  min-width: 180px;
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 14px;
  padding: 0.85rem 0.95rem;
  background: rgba(255, 255, 255, 0.05);
  color: #e5e7eb;
}

.table-card {
  padding: 1rem;
  border-radius: 22px;
  background: rgba(15, 23, 42, 0.72);
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 0.9rem 0.75rem;
  text-align: left;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.badge {
  display: inline-flex;
  padding: 0.35rem 0.65rem;
  border-radius: 999px;
  font-size: 0.8rem;
  font-weight: 700;
}

.badge--active {
  background: rgba(16, 185, 129, 0.15);
  color: #6ee7b7;
}

.badge--inactive {
  background: rgba(239, 68, 68, 0.15);
  color: #fca5a5;
}

.empty {
  text-align: center;
  color: #cbd5e1;
}

.pagination {
  margin-top: 1rem;
  flex-wrap: wrap;
  color: #cbd5e1;
}

.modal {
  position: fixed;
  inset: 0;
  display: grid;
  place-items: center;
  z-index: 50;
}

.modal__backdrop {
  position: absolute;
  inset: 0;
  background: rgba(15, 23, 42, 0.75);
}

.modal__panel {
  position: relative;
  z-index: 1;
  width: min(96vw, 1100px);
  max-height: 90vh;
  overflow: auto;
  padding: 1.25rem;
  border-radius: 22px;
  background: #111827;
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.form {
  margin-top: 1rem;
}

.grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
}

label {
  display: grid;
  gap: 0.4rem;
}

select, input {
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 14px;
  padding: 0.85rem 0.95rem;
  background: rgba(255, 255, 255, 0.05);
  color: #e5e7eb;
}
</style>
