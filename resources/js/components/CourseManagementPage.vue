<template>
  <section class="manager">
    <header class="manager__header">
      <div>
        <p class="eyebrow">RF04</p>
        <h1>Gestión de cursos</h1>
        <p class="lead">Crear, editar y eliminar cursos desde un solo panel.</p>
      </div>
      <div class="actions">
        <button class="btn" type="button" @click="openCreate">Nuevo curso</button>
        <a class="btn btn--ghost" href="/index">Volver al panel</a>
      </div>
    </header>

    <div v-if="message" class="alert" :class="alertClass">{{ message }}</div>

    <section class="toolbar">
      <input v-model="filters.search" class="input" type="text" placeholder="Buscar por nombre o descripción" @keyup.enter="loadCourses(1)" />
      <select v-model="filters.Estado" class="input" @change="loadCourses(1)">
        <option value="">Todos los estados</option>
        <option :value="1">Activos</option>
        <option :value="0">Inactivos</option>
      </select>
      <select v-model="perPage" class="input" @change="loadCourses(1)">
        <option :value="5">5 por página</option>
        <option :value="10">10 por página</option>
        <option :value="15">15 por página</option>
      </select>
      <button class="btn" type="button" @click="loadCourses(1)">Filtrar</button>
    </section>

    <section class="table-card">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Estado</th>
            <th>Registro</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="curso in cursos" :key="curso.IdCurso">
            <td>#{{ curso.IdCurso }}</td>
            <td>{{ curso.Nombre }}</td>
            <td>{{ curso.Descripcion || 'Sin descripción' }}</td>
            <td>
              <span class="badge" :class="curso.Estado ? 'badge--active' : 'badge--inactive'">
                {{ curso.Estado ? 'Activo' : 'Inactivo' }}
              </span>
            </td>
            <td>{{ courseDate(curso.FechaRegistro) }}</td>
            <td>
              <div class="row-actions">
                <button class="mini-btn" @click="openEdit(curso)">Editar</button>
                <button class="mini-btn mini-btn--warn" @click="removeCourse(curso)">Eliminar</button>
              </div>
            </td>
          </tr>
          <tr v-if="!cursos.length">
            <td colspan="6" class="empty">No hay cursos para mostrar.</td>
          </tr>
        </tbody>
      </table>

      <div class="pagination">
        <span>Mostrando {{ pagination.from || 0 }} - {{ pagination.to || 0 }} de {{ pagination.total || 0 }}</span>
        <div class="pagination__actions">
          <button class="mini-btn" :disabled="pagination.current_page <= 1" @click="loadCourses(pagination.current_page - 1)">Anterior</button>
          <button class="mini-btn" :disabled="pagination.current_page >= pagination.last_page" @click="loadCourses(pagination.current_page + 1)">Siguiente</button>
        </div>
      </div>
    </section>

    <div v-if="editing" class="modal">
      <div class="modal__backdrop" @click="closeModal"></div>
      <div class="modal__panel">
        <header class="modal__header">
          <div>
            <p class="eyebrow">Curso</p>
            <h2>{{ form.IdCurso ? 'Editar curso' : 'Nuevo curso' }}</h2>
          </div>
          <button class="mini-btn" @click="closeModal">Cerrar</button>
        </header>

        <form class="form" @submit.prevent="saveCourse">
          <div class="grid">
            <label>Nombre<input v-model="form.Nombre" type="text" maxlength="100" required /></label>
            <label>Descripción<textarea v-model="form.Descripcion" rows="4" maxlength="255"></textarea></label>
            <label>Estado
              <select v-model="form.Estado" required>
                <option :value="true">Activo</option>
                <option :value="false">Inactivo</option>
              </select>
            </label>
          </div>

          <div class="modal__footer">
            <button class="mini-btn" type="button" @click="closeModal">Cancelar</button>
            <button class="btn" type="submit" :disabled="saving">{{ saving ? 'Guardando...' : 'Guardar' }}</button>
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
  name: 'CourseManagementPage',
  setup() {
    const cursos = ref([]);
    const pagination = ref({});
    const message = ref('');
    const messageType = ref('success');
    const loading = ref(false);
    const saving = ref(false);
    const editing = ref(false);
    const perPage = ref(10);

    const filters = reactive({
      search: '',
      Estado: '',
    });

    const form = reactive({
      IdCurso: null,
      Nombre: '',
      Descripcion: '',
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

    const loadCourses = async (page = 1) => {
      try {
        loading.value = true;
        message.value = '';

        const response = await axios.get('/api/cursos', {
          params: {
            page,
            per_page: perPage.value,
            ...filters,
          },
        });

        cursos.value = response.data.data.cursos.data;
        pagination.value = response.data.data.cursos;
      } catch (error) {
        console.error(error);
        messageType.value = 'error';
        message.value = error.response?.data?.message ?? 'No se pudo cargar el listado de cursos.';
      } finally {
        loading.value = false;
      }
    };

    const openCreate = () => {
      editing.value = true;
      form.IdCurso = null;
      form.Nombre = '';
      form.Descripcion = '';
      form.Estado = true;
    };

    const openEdit = (curso) => {
      editing.value = true;
      form.IdCurso = curso.IdCurso;
      form.Nombre = curso.Nombre;
      form.Descripcion = curso.Descripcion || '';
      form.Estado = Boolean(curso.Estado);
    };

    const closeModal = () => {
      editing.value = false;
    };

    const saveCourse = async () => {
      try {
        saving.value = true;
        message.value = '';

        const payload = {
          Nombre: form.Nombre,
          Descripcion: form.Descripcion,
          Estado: Boolean(form.Estado),
        };

        let response;

        if (form.IdCurso) {
          response = await axios.put(`/api/cursos/${form.IdCurso}`, payload);
        } else {
          response = await axios.post('/api/cursos', payload);
        }

        messageType.value = 'success';
        message.value = response.data.message;
        editing.value = false;
        await loadCourses(pagination.value.current_page || 1);
      } catch (error) {
        console.error(error);
        messageType.value = 'error';
        message.value = error.response?.data?.message ?? 'No se pudo guardar el curso.';
      } finally {
        saving.value = false;
      }
    };

    const removeCourse = async (curso) => {
      const confirmed = window.confirm(`¿Eliminar el curso "${curso.Nombre}"?`);
      if (!confirmed) return;

      try {
        message.value = '';
        const response = await axios.delete(`/api/cursos/${curso.IdCurso}`);
        messageType.value = response.data.status ? 'success' : 'error';
        message.value = response.data.message;
        await loadCourses(pagination.value.current_page || 1);
      } catch (error) {
        console.error(error);
        messageType.value = 'error';
        message.value = error.response?.data?.message ?? 'No se pudo eliminar el curso.';
      }
    };

    const courseDate = (value) => {
      if (!value) return 'N/A';
      return new Date(value).toLocaleString('es-BO');
    };

    onMounted(async () => {
      const ok = await loadProfile();
      if (ok) {
        await loadCourses(1);
      }
    });

    return {
      cursos,
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
      openCreate,
      openEdit,
      closeModal,
      saveCourse,
      removeCourse,
      loadCourses,
      courseDate,
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
  width: min(96vw, 900px);
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
}

label {
  display: grid;
  gap: 0.4rem;
}

select, input, textarea {
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 14px;
  padding: 0.85rem 0.95rem;
  background: rgba(255, 255, 255, 0.05);
  color: #e5e7eb;
}
</style>
