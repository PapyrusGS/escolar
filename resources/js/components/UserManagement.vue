<template>
  <section class="user-management">
    <header class="user-management__header">
      <div>
        <p class="eyebrow">RF09</p>
        <h1>Gestión de usuarios</h1>
        <p>Listar, editar, suspender y eliminar cuentas de usuarios académicos.</p>
      </div>
      <div class="header-actions">
        <a class="btn-create" href="/usuarios/create">Registrar nuevo</a>
        <a class="back-link" href="/index">Volver al panel</a>
      </div>
    </header>

    <div v-if="message" class="alert" :class="`alert--${messageType}`">
      {{ message }}
      <button class="alert__close" @click="message = ''">&times;</button>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="filters">
      <input 
        v-model="searchQuery" 
        type="text" 
        placeholder="Buscar por nombre, correo, CI o teléfono..." 
        class="filters__search"
      />
      <select v-model="roleFilter" class="filters__select">
        <option value="">Todos los roles</option>
        <option v-for="rol in roles" :key="rol.IdRol" :value="rol.IdRol">
          {{ rol.Nombre }}
        </option>
      </select>
    </div>

    <!-- Lista de Usuarios -->
    <div class="table-container">
      <table class="users-table">
        <thead>
          <tr>
            <th>Nombre Completo</th>
            <th>Correo</th>
            <th>CI / Teléfono</th>
            <th>Rol</th>
            <th>Académico (Carrera / Modalidad)</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="filteredUsers.length === 0">
            <td colspan="7" class="text-center py-8 text-gray">
              {{ loading ? 'Cargando usuarios...' : 'No se encontraron usuarios.' }}
            </td>
          </tr>
          <tr v-for="usuario in filteredUsers" :key="usuario.IdUsuario">
            <td>
              <div class="user-name">
                <span class="user-name__primary">{{ usuario.Nombre1 }} {{ usuario.Nombre2 || '' }}</span>
                <span class="user-name__secondary">{{ usuario.Apellido1 }} {{ usuario.Apellido2 || '' }}</span>
              </div>
            </td>
            <td>
              <span class="user-email">{{ usuario.Correo }}</span>
            </td>
            <td>
              <div class="user-details">
                <span>CI: {{ usuario.CI }}</span>
                <span class="subtext">Tel: {{ usuario.Telefono }}</span>
              </div>
            </td>
            <td>
              <span class="badge" :class="`badge--rol-${usuario.IdRol}`">
                {{ usuario.Rol?.Nombre || `Rol ${usuario.IdRol}` }}
              </span>
            </td>
            <td>
              <div v-if="usuario.IdRol === 3" class="academic-info">
                <span class="academic-info__carrera">{{ getCarreraName(usuario.IdCarrera) }}</span>
                <span class="badge badge--modalidad">{{ getModalidadName(usuario.IdModalidad) }}</span>
              </div>
              <span v-else class="text-muted">—</span>
            </td>
            <td>
              <button 
                class="status-btn" 
                :class="usuario.Estado ? 'status-btn--active' : 'status-btn--inactive'"
                @click="toggleStatus(usuario)"
                :title="usuario.Estado ? 'Desactivar usuario' : 'Activar usuario'"
              >
                {{ usuario.Estado ? 'Activo' : 'Inactivo' }}
              </button>
            </td>
            <td>
              <div class="actions">
                <button class="btn-edit" @click="openEditModal(usuario)">Editar</button>
                <button class="btn-delete" @click="confirmDelete(usuario)">Eliminar</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal de Edición de Usuario -->
    <div v-if="showEditModal" class="modal-overlay" @click.self="closeEditModal">
      <div class="modal-card">
        <header class="modal-card__header">
          <h2>Editar Usuario</h2>
          <button class="btn-close-modal" @click="closeEditModal">&times;</button>
        </header>
        <div class="modal-card__body">
          <div v-if="message && messageType === 'error'" class="alert alert--error" style="margin-top: 0; margin-bottom: 20px;">
            {{ message }}
          </div>
          <form @submit.prevent="updateUsuario" class="modal-form">
            <div class="grid">
              <label>
                Rol <span class="optional">(No editable)</span>
                <select v-model="editForm.IdRol" required disabled>
                  <option v-for="rol in roles" :key="rol.IdRol" :value="rol.IdRol">
                    {{ rol.Nombre }}
                  </option>
                </select>
              </label>

              <label v-if="isEditStudent">
                Carrera
                <select v-model="editForm.IdCarrera" required>
                  <option value="" disabled>Seleccione una carrera</option>
                  <option v-for="carrera in carreras" :key="carrera.IdCarrera" :value="carrera.IdCarrera">
                    {{ carrera.Nombre || `Carrera ${carrera.IdCarrera}` }}
                  </option>
                </select>
              </label>

              <label v-if="isEditStudent">
                Modalidad
                <select v-model="editForm.IdModalidad" required>
                  <option value="" disabled>Seleccione una modalidad</option>
                  <option v-for="modalidad in modalidades" :key="modalidad.IdModalidad" :value="modalidad.IdModalidad">
                    {{ modalidad.Nombre || `Modalidad ${modalidad.IdModalidad}` }}
                  </option>
                </select>
              </label>

              <label>
                Correo
                <input v-model.trim="editForm.Correo" type="email" required />
              </label>

              <label>
                CI
                <input v-model.trim="editForm.CI" type="text" required />
              </label>

              <label>
                Teléfono
                <input v-model.trim="editForm.Telefono" type="text" required />
              </label>

              <label>
                Nombre 1
                <input v-model.trim="editForm.Nombre1" type="text" required />
              </label>

              <label>
                Nombre 2
                <input v-model.trim="editForm.Nombre2" type="text" />
              </label>

              <label>
                Apellido 1
                <input v-model.trim="editForm.Apellido1" type="text" required />
              </label>

              <label>
                Apellido 2
                <input v-model.trim="editForm.Apellido2" type="text" />
              </label>

              <label>
                Nueva Contraseña <span class="optional">(Opcional)</span>
                <input v-model="editForm.Contrasena" type="password" placeholder="Mínimo 6 caracteres" />
              </label>

              <label>
                Confirmar contraseña
                <input v-model="editForm.Contrasena_confirmation" type="password" />
              </label>
            </div>

            <div class="modal-actions">
              <button type="submit" class="btn-save" :disabled="modalSubmitting">
                {{ modalSubmitting ? 'Guardando...' : 'Guardar Cambios' }}
              </button>
              <button type="button" class="btn-cancel" @click="closeEditModal">Cancelar</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal de Confirmación de Eliminación -->
    <div v-if="showDeleteModal" class="modal-overlay" @click.self="closeDeleteModal">
      <div class="modal-card modal-card--danger">
        <header class="modal-card__header">
          <h2>Confirmar Eliminación</h2>
          <button class="btn-close-modal" @click="closeDeleteModal">&times;</button>
        </header>
        <div class="modal-card__body text-center">
          <p>¿Está seguro de que desea eliminar permanentemente al usuario?</p>
          <div class="user-block">
            <strong>{{ userToDelete?.Nombre1 }} {{ userToDelete?.Apellido1 }}</strong>
            <span class="subtext">{{ userToDelete?.Correo }}</span>
          </div>
          <p class="warning-text">
            <strong>Nota:</strong> Si el usuario posee dependencias (inscripciones o notas), la eliminación física fallará por seguridad, y se le ofrecerá desactivar la cuenta en su lugar.
          </p>
          <div class="modal-actions">
            <button class="btn-danger" @click="deleteUsuario" :disabled="modalSubmitting">
              {{ modalSubmitting ? 'Eliminando...' : 'Eliminar Permanentemente' }}
            </button>
            <button class="btn-cancel" @click="closeDeleteModal">Cancelar</button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import axios from 'axios';

export default {
  name: 'UserManagement',
  data() {
    return {
      users: [],
      roles: [],
      carreras: [],
      modalidades: [],
      loading: false,
      searchQuery: '',
      roleFilter: '',
      message: '',
      messageType: 'error',
      
      // Modal de edición
      showEditModal: false,
      modalSubmitting: false,
      editForm: {
        IdUsuario: null,
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

      // Modal de borrado
      showDeleteModal: false,
      userToDelete: null,
    };
  },
  computed: {
    isEditStudent() {
      return Number(this.editForm.IdRol) === 3;
    },
    filteredUsers() {
      const query = this.searchQuery.toLowerCase().trim();
      return this.users.filter(user => {
        // Filtrado por Rol
        if (this.roleFilter && Number(user.IdRol) !== Number(this.roleFilter)) {
          return false;
        }
        // Filtrado por texto
        if (!query) return true;
        const fullName = `${user.Nombre1} ${user.Nombre2 || ''} ${user.Apellido1} ${user.Apellido2 || ''}`.toLowerCase();
        const email = (user.Correo || '').toLowerCase();
        const ci = (user.CI || '').toLowerCase();
        const tel = (user.Telefono || '').toLowerCase();
        return fullName.includes(query) || email.includes(query) || ci.includes(query) || tel.includes(query);
      });
    }
  },
  mounted() {
    this.init();
  },
  methods: {
    async init() {
      this.loading = true;
      const token = localStorage.getItem('auth_token');
      if (token) {
        axios.defaults.headers.common.Authorization = `Bearer ${token}`;
      } else {
        window.location.href = '/';
        return;
      }
      await Promise.all([
        this.loadUsers(),
        this.loadFormData()
      ]);
      this.loading = false;
    },
    async loadUsers() {
      try {
        const { data } = await axios.get('/api/usuarios');
        this.users = data?.data ?? [];
      } catch (error) {
        this.setMessage('No se pudieron cargar los usuarios.', 'error');
      }
    },
    async loadFormData() {
      try {
        const { data } = await axios.get('/api/usuarios/form-data');
        this.roles = data?.roles ?? data?.data?.roles ?? [];
        this.carreras = data?.carreras ?? data?.data?.carreras ?? [];
        this.modalidades = data?.modalidades ?? data?.data?.modalidades ?? [];
      } catch (error) {
        console.error('Error al cargar datos del formulario:', error);
      }
    },
    getCarreraName(id) {
      const carrera = this.carreras.find(c => Number(c.IdCarrera) === Number(id));
      return carrera ? carrera.Nombre : `Carrera ${id}`;
    },
    getModalidadName(id) {
      const mod = this.modalidades.find(m => Number(m.IdModalidad) === Number(id));
      return mod ? mod.Nombre : `Modalidad ${id}`;
    },
    onEditRoleChange() {
      if (!this.isEditStudent) {
        this.editForm.IdCarrera = '';
        this.editForm.IdModalidad = '';
      }
    },
    async toggleStatus(usuario) {
      try {
        const { data } = await axios.patch(`/api/usuarios/${usuario.IdUsuario}/toggle-status`);
        usuario.Estado = !usuario.Estado;
        this.setMessage(data?.message || 'Estado del usuario actualizado.', 'success');
      } catch (error) {
        this.setMessage(error?.response?.data?.message || 'No se pudo actualizar el estado.', 'error');
      }
    },
    openEditModal(usuario) {
      this.editForm = {
        IdUsuario: usuario.IdUsuario,
        IdRol: usuario.IdRol,
        IdCarrera: usuario.IdCarrera || '',
        IdModalidad: usuario.IdModalidad || '',
        Correo: usuario.Correo,
        CI: usuario.CI,
        Telefono: usuario.Telefono,
        Nombre1: usuario.Nombre1,
        Nombre2: usuario.Nombre2 || '',
        Apellido1: usuario.Apellido1,
        Apellido2: usuario.Apellido2 || '',
        Contrasena: '',
        Contrasena_confirmation: '',
      };
      this.showEditModal = true;
    },
    closeEditModal() {
      this.showEditModal = false;
    },
    async updateUsuario() {
      this.modalSubmitting = true;
      this.message = '';

      try {
        const payload = {
          ...this.editForm,
          IdRol: Number(this.editForm.IdRol),
          IdCarrera: this.isEditStudent ? Number(this.editForm.IdCarrera) : null,
          IdModalidad: this.isEditStudent ? Number(this.editForm.IdModalidad) : null,
        };

        // Si no se digitó contraseña, la removemos del payload
        if (!payload.Contrasena) {
          delete payload.Contrasena;
          delete payload.Contrasena_confirmation;
        }

        const { data } = await axios.put(`/api/usuarios/${this.editForm.IdUsuario}`, payload);
        this.setMessage('Usuario actualizado correctamente.', 'success');
        this.closeEditModal();
        await this.loadUsers();
      } catch (error) {
        console.error('Error al actualizar usuario:', error);
        if (error?.response?.status === 422) {
          const errors = error.response?.data?.errors;
          if (errors && typeof errors === 'object') {
            const messages = Object.values(errors).flat().join(' | ');
            this.setMessage(messages || 'Los datos de entrada no son válidos.', 'error');
          } else {
            const responseMsg = error.response?.data?.message;
            this.setMessage(responseMsg || 'Los datos de entrada no son válidos.', 'error');
          }
        } else {
          const responseMsg = error?.response?.data?.message;
          this.setMessage(responseMsg || 'Error al actualizar el usuario.', 'error');
        }
      } finally {
        this.modalSubmitting = false;
      }
    },
    confirmDelete(usuario) {
      this.userToDelete = usuario;
      this.showDeleteModal = true;
    },
    closeDeleteModal() {
      this.showDeleteModal = false;
      this.userToDelete = null;
    },
    async deleteUsuario() {
      this.modalSubmitting = true;
      this.message = '';

      try {
        const { data } = await axios.delete(`/api/usuarios/${this.userToDelete.IdUsuario}`);
        this.setMessage(data?.message || 'Usuario eliminado correctamente.', 'success');
        this.closeDeleteModal();
        await this.loadUsers();
      } catch (error) {
        const responseMsg = error?.response?.data?.message;
        this.setMessage(responseMsg || 'No se pudo eliminar al usuario por dependencias activas.', 'error');
        this.closeDeleteModal();
      } finally {
        this.modalSubmitting = false;
      }
    },
    setMessage(message, type = 'error') {
      this.message = message;
      this.messageType = type;
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  }
};
</script>

<style scoped>
.user-management { min-height: 100vh; padding: 32px; background: linear-gradient(180deg, #07111f 0%, #101b2b 100%); color: #eef2ff; }
.user-management__header { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 32px; }
.eyebrow { margin: 0 0 8px; color: #fbbf24; text-transform: uppercase; letter-spacing: .18em; font-size: .75rem; }
h1 { margin: 0; font-size: 2rem; }
p { margin: 8px 0 0; color: #cbd5e1; }
.header-actions { display: flex; gap: 12px; }
.back-link, .btn-create, .btn-save, .btn-cancel, .btn-danger { border-radius: 999px; padding: 12px 24px; font-weight: 700; text-decoration: none; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; }
.back-link { background: transparent; border: 1px solid rgba(148, 163, 184, .22); color: #cbd5e1; }
.btn-create { background: #fbbf24; color: #0f172a; }

.alert { position: relative; margin: 20px 0; padding: 16px 40px 16px 18px; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; }
.alert--success { background: rgba(16, 185, 129, .16); color: #d1fae5; border: 1px solid rgba(16, 185, 129, .3); }
.alert--error { background: rgba(239, 68, 68, .16); color: #fecaca; border: 1px solid rgba(239, 68, 68, .3); }
.alert__close { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: transparent; border: none; font-size: 1.5rem; color: inherit; cursor: pointer; }

/* Filtros */
.filters { display: flex; gap: 16px; margin-bottom: 24px; }
.filters__search { flex: 1; border-radius: 14px; border: 1px solid rgba(148, 163, 184, .22); background: rgba(30, 41, 59, .82); color: #f8fafc; padding: 12px 16px; font-size: 1rem; outline: none; }
.filters__select { width: 220px; border-radius: 14px; border: 1px solid rgba(148, 163, 184, .22); background: rgba(30, 41, 59, .82); color: #f8fafc; padding: 12px 16px; font-size: 1rem; outline: none; }
.filters__search:focus, .filters__select:focus { border-color: #fbbf24; box-shadow: 0 0 0 3px rgba(251, 191, 36, .18); }

/* Tabla */
.table-container { background: rgba(15, 23, 42, .86); border: 1px solid rgba(148, 163, 184, .18); border-radius: 24px; overflow: hidden; box-shadow: 0 20px 60px rgba(0, 0, 0, .25); margin-bottom: 24px; }
.users-table { width: 100%; border-collapse: collapse; text-align: left; }
.users-table th { background: rgba(30, 41, 59, .5); color: #e2e8f0; font-weight: 700; padding: 16px 20px; border-bottom: 1px solid rgba(148, 163, 184, .12); }
.users-table td { padding: 16px 20px; border-bottom: 1px solid rgba(148, 163, 184, .08); vertical-align: middle; }
.users-table tbody tr:hover { background: rgba(255, 255, 255, .02); }

.user-name { display: flex; flex-direction: column; }
.user-name__primary { font-weight: 700; color: #f8fafc; font-size: 1.05rem; }
.user-name__secondary { color: #94a3b8; font-size: 0.9rem; }
.user-email { color: #fbbf24; font-weight: 600; font-family: monospace; font-size: 0.95rem; }
.user-details { display: flex; flex-direction: column; gap: 2px; }
.subtext { color: #94a3b8; font-size: 0.82rem; }

/* Badges */
.badge { display: inline-flex; align-items: center; justify-content: center; padding: 4px 10px; border-radius: 999px; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
.badge--rol-1 { background: rgba(239, 68, 68, .16); color: #fca5a5; border: 1px solid rgba(239, 68, 68, .3); }
.badge--rol-2 { background: rgba(59, 130, 246, .16); color: #93c5fd; border: 1px solid rgba(59, 130, 246, .3); }
.badge--rol-3 { background: rgba(16, 185, 129, .16); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, .3); }
.badge--modalidad { background: rgba(139, 92, 246, .16); color: #c4b5fd; border: 1px solid rgba(139, 92, 246, .3); margin-top: 4px; align-self: flex-start; }

.academic-info { display: flex; flex-direction: column; }
.academic-info__carrera { font-weight: 600; color: #e2e8f0; font-size: 0.88rem; }
.text-muted { color: #64748b; }

/* Status button */
.status-btn { padding: 6px 14px; border-radius: 999px; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s ease; font-size: 0.82rem; }
.status-btn--active { background: rgba(16, 185, 129, .16); color: #34d399; border: 1px solid rgba(16, 185, 129, .3); }
.status-btn--active:hover { background: rgba(16, 185, 129, .28); }
.status-btn--inactive { background: rgba(239, 68, 68, .16); color: #f87171; border: 1px solid rgba(239, 68, 68, .3); }
.status-btn--inactive:hover { background: rgba(239, 68, 68, .28); }

.actions { display: flex; gap: 8px; }
.actions button { border-radius: 8px; padding: 6px 12px; font-weight: 700; border: none; cursor: pointer; font-size: 0.82rem; }
.btn-edit { background: rgba(251, 191, 36, .16); color: #facc15; border: 1px solid rgba(251, 191, 36, .3); }
.btn-edit:hover { background: rgba(251, 191, 36, .26); }
.btn-delete { background: rgba(239, 68, 68, .16); color: #f87171; border: 1px solid rgba(239, 68, 68, .3); }
.btn-delete:hover { background: rgba(239, 68, 68, .26); }

/* Modal Styles */
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(7, 11, 25, 0.85); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px; backdrop-filter: blur(8px); }
.modal-card { background: #0f172a; border: 1px solid rgba(148, 163, 184, .25); border-radius: 24px; width: 100%; max-width: 750px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); animation: zoomIn 0.25s ease-out; }
.modal-card--danger { max-width: 500px; border-color: rgba(239, 68, 68, 0.4); }
.modal-card__header { padding: 20px 24px; border-bottom: 1px solid rgba(148, 163, 184, .14); display: flex; justify-content: space-between; align-items: center; background: rgba(30, 41, 59, .4); }
.modal-card__header h2 { margin: 0; font-size: 1.3rem; color: #f8fafc; }
.btn-close-modal { background: transparent; border: none; color: #94a3b8; font-size: 2rem; cursor: pointer; line-height: 1; }
.btn-close-modal:hover { color: #f8fafc; }
.modal-card__body { padding: 24px; overflow-y: auto; max-height: 80vh; }

.modal-form .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
.modal-form label { display: flex; flex-direction: column; gap: 6px; font-weight: 600; color: #e2e8f0; font-size: 0.9rem; }
.modal-form input, .modal-form select { border-radius: 12px; border: 1px solid rgba(148, 163, 184, .22); background: rgba(30, 41, 59, .82); color: #f8fafc; padding: 12px 14px; font-size: 0.95rem; outline: none; }
.modal-form input:focus, .modal-form select:focus { border-color: #fbbf24; box-shadow: 0 0 0 3px rgba(251, 191, 36, .18); }
.optional { color: #64748b; font-size: 0.78rem; font-weight: 400; }

.modal-actions { display: flex; gap: 12px; margin-top: 24px; justify-content: flex-end; }
.btn-save { background: #fbbf24; color: #0f172a; }
.btn-cancel { background: transparent; border: 1px solid rgba(148, 163, 184, .22); color: #e2e8f0; }
.btn-danger { background: #ef4444; color: #ffffff; }
.btn-save:disabled, .btn-danger:disabled { opacity: 0.7; cursor: not-allowed; }

.user-block { background: rgba(30, 41, 59, .6); border-radius: 16px; padding: 16px; margin: 16px 0; display: flex; flex-direction: column; align-items: center; border: 1px solid rgba(148, 163, 184, .12); }
.warning-text { color: #fca5a5; font-size: 0.88rem; line-height: 1.5; margin-top: 16px; }

@keyframes zoomIn {
  from { transform: scale(0.95); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

@media (max-width: 900px) {
  .user-management__header { flex-direction: column; align-items: stretch; gap: 20px; }
  .filters { flex-direction: column; }
  .filters__select { width: 100%; }
  .modal-form .grid { grid-template-columns: 1fr; }
  .users-table th:nth-child(3), .users-table td:nth-child(3),
  .users-table th:nth-child(5), .users-table td:nth-child(5) { display: none; }
}
</style>
