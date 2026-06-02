<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import axios from 'axios';
import {
  Users,
  UserPlus,
  Search,
  Pencil,
  Trash2,
  ArrowLeft,
  RefreshCw,
  Mail,
  Phone,
  IdCard,
  User as UserIcon,
  Power,
  ChevronLeft,
  ChevronRight,
  ShieldAlert,
  CheckCircle2,
} from '@lucide/vue';
import AppShell from './layout/AppShell.vue';
import PageTransition from './layout/PageTransition.vue';
import AppCard from './ui/AppCard.vue';
import AppButton from './ui/AppButton.vue';
import AppInput from './ui/AppInput.vue';
import AppSelect from './ui/AppSelect.vue';
import AppAlert from './ui/AppAlert.vue';
import AppPageHeader from './ui/AppPageHeader.vue';
import AppModal from './ui/AppModal.vue';
import AppTable from './ui/AppTable.vue';
import AppAvatar from './ui/AppAvatar.vue';
import AppRoleBadge from './ui/AppRoleBadge.vue';
import AppBadge from './ui/AppBadge.vue';
import AppEmptyState from './ui/AppEmptyState.vue';
import { toast } from '../lib/toast.js';
import { useGoTo } from '../composables/useGoTo.js';

const { goTo } = useGoTo();
import { useGsap } from '../composables/useGsap.js';

const { staggerIn } = useGsap();

const user = ref(null);
const users = ref([]);
const roles = ref([]);
const carreras = ref([]);
const modalidades = ref([]);
const loading = ref(false);
const submitting = ref(false);
const searchQuery = ref('');
const roleFilter = ref('');

const showEditModal = ref(false);
const showDeleteModal = ref(false);
const userToDelete = ref(null);

const editForm = ref(emptyForm());

function emptyForm() {
  return {
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
  };
}

const roleOptions = computed(() =>
  roles.value.map((r) => ({ Id: r.IdRol, Nombre: r.Nombre || r.Nombre1 || `Rol ${r.IdRol}` }))
);
const carreraOptions = computed(() =>
  carreras.value.map((c) => ({ Id: c.IdCarrera, Nombre: c.Nombre || c.Descripcion || `Carrera ${c.IdCarrera}` }))
);
const modalidadOptions = computed(() =>
  modalidades.value.map((m) => ({ Id: m.IdModalidad, Nombre: m.Nombre || m.Descripcion || `Modalidad ${m.IdModalidad}` }))
);

const isEditStudent = computed(() => Number(editForm.value.IdRol) === 3);

const filteredUsers = computed(() => {
  const query = searchQuery.value.toLowerCase().trim();
  return users.value.filter((u) => {
    if (roleFilter.value && Number(u.IdRol) !== Number(roleFilter.value)) return false;
    if (!query) return true;
    const fullName = `${u.Nombre1} ${u.Nombre2 || ''} ${u.Apellido1} ${u.Apellido2 || ''}`.toLowerCase();
    return (
      fullName.includes(query) ||
      (u.Correo || '').toLowerCase().includes(query) ||
      (u.CI || '').toLowerCase().includes(query) ||
      (u.Telefono || '').toLowerCase().includes(query)
    );
  });
});

const stats = computed(() => {
  const total = users.value.length;
  const active = users.value.filter((u) => u.Estado).length;
  const inactive = total - active;
  const admins = users.value.filter((u) => Number(u.IdRol) === 1).length;
  const teachers = users.value.filter((u) => Number(u.IdRol) === 2).length;
  const students = users.value.filter((u) => Number(u.IdRol) === 3).length;
  return { total, active, inactive, admins, teachers, students };
});

const page = ref(1);
const pageSize = 8;
const totalPages = computed(() => Math.max(1, Math.ceil(filteredUsers.value.length / pageSize)));
const paginatedUsers = computed(() => {
  const start = (page.value - 1) * pageSize;
  return filteredUsers.value.slice(start, start + pageSize);
});
const pageRange = computed(() => {
  const from = filteredUsers.value.length === 0 ? 0 : (page.value - 1) * pageSize + 1;
  const to = Math.min(page.value * pageSize, filteredUsers.value.length);
  return { from, to };
});

const goToPage = (p) => {
  if (p < 1 || p > totalPages.value) return;
  page.value = p;
};

onMounted(async () => {
  const token = localStorage.getItem('auth_token');
  if (!token) {
    window.location.href = '/';
    return;
  }
  axios.defaults.headers.common.Authorization = `Bearer ${token}`;

  const stored = localStorage.getItem('auth_user');
  if (stored) user.value = JSON.parse(stored);

  await Promise.all([loadUsers(), loadFormData()]);

  await nextTick();
  staggerIn('.um-row', { delay: 0.05 });
});

const loadUsers = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/usuarios');
    users.value = data?.data ?? data ?? [];
    page.value = 1;
  } catch (err) {
    toast.error('No se pudieron cargar los usuarios');
  } finally {
    loading.value = false;
  }
};

const loadFormData = async () => {
  try {
    const { data } = await axios.get('/api/usuarios/form-data');
    roles.value = data?.roles ?? data?.data?.roles ?? [];
    carreras.value = data?.carreras ?? data?.data?.carreras ?? [];
    modalidades.value = data?.modalidades ?? data?.data?.modalidades ?? [];
  } catch (err) {
    console.error('Error al cargar datos del formulario:', err);
  }
};

const roleVariant = (id) => {
  const map = { 1: 'admin', 2: 'teacher', 3: 'student' };
  return map[Number(id)] || 'neutral';
};

const roleLabel = (u) => {
  if (u.Rol?.Nombre) return u.Rol.Nombre;
  const found = roles.value.find((r) => Number(r.IdRol) === Number(u.IdRol));
  return found?.Nombre || `Rol ${u.IdRol}`;
};

const carreraLabel = (id) => {
  const c = carreras.value.find((x) => Number(x.IdCarrera) === Number(id));
  return c?.Nombre || (id ? `Carrera ${id}` : '—');
};

const modalidadLabel = (id) => {
  const m = modalidades.value.find((x) => Number(x.IdModalidad) === Number(id));
  return m?.Nombre || (id ? `Modalidad ${id}` : '—');
};

const fullName = (u) => `${u.Nombre1 || ''} ${u.Apellido1 || ''}`.trim() || 'Sin nombre';

const toggleStatus = async (u) => {
  const previous = u.Estado;
  u.Estado = !previous;
  try {
    const { data } = await axios.patch(`/api/usuarios/${u.IdUsuario}/toggle-status`);
    u.Estado = data?.Estado ?? !previous;
    toast.success(data?.message || 'Estado actualizado');
  } catch (err) {
    u.Estado = previous;
    toast.error(err?.response?.data?.message || 'No se pudo actualizar el estado');
  }
};

const openEditModal = (u) => {
  editForm.value = {
    IdUsuario: u.IdUsuario,
    IdRol: u.IdRol,
    IdCarrera: u.IdCarrera || '',
    IdModalidad: u.IdModalidad || '',
    Correo: u.Correo || '',
    CI: u.CI || '',
    Telefono: u.Telefono || '',
    Nombre1: u.Nombre1 || '',
    Nombre2: u.Nombre2 || '',
    Apellido1: u.Apellido1 || '',
    Apellido2: u.Apellido2 || '',
    Contrasena: '',
    Contrasena_confirmation: '',
  };
  showEditModal.value = true;
};

const closeEditModal = () => {
  showEditModal.value = false;
  editForm.value = emptyForm();
};

const onEditRoleChange = () => {
  if (!isEditStudent.value) {
    editForm.value.IdCarrera = '';
    editForm.value.IdModalidad = '';
  }
};

const submitEdit = async () => {
  submitting.value = true;
  try {
    const payload = {
      ...editForm.value,
      IdRol: Number(editForm.value.IdRol),
      IdCarrera: isEditStudent.value && editForm.value.IdCarrera ? Number(editForm.value.IdCarrera) : null,
      IdModalidad: isEditStudent.value && editForm.value.IdModalidad ? Number(editForm.value.IdModalidad) : null,
    };
    if (!payload.Contrasena) {
      delete payload.Contrasena;
      delete payload.Contrasena_confirmation;
    }
    const { data } = await axios.put(`/api/usuarios/${editForm.value.IdUsuario}`, payload);
    toast.success(data?.message || 'Usuario actualizado');
    closeEditModal();
    await loadUsers();
    await nextTick();
    staggerIn('.um-row', { delay: 0.05 });
  } catch (err) {
    if (err?.response?.status === 422) {
      const errors = err.response?.data?.errors;
      const msg = errors ? Object.values(errors).flat().join(' | ') : err.response?.data?.message;
      toast.error(msg || 'Datos no válidos');
    } else {
      toast.error(err?.response?.data?.message || 'No se pudo actualizar el usuario');
    }
  } finally {
    submitting.value = false;
  }
};

const confirmDelete = (u) => {
  userToDelete.value = u;
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  userToDelete.value = null;
};

const submitDelete = async () => {
  if (!userToDelete.value) return;
  submitting.value = true;
  try {
    const { data } = await axios.delete(`/api/usuarios/${userToDelete.value.IdUsuario}`);
    toast.success(data?.message || 'Usuario eliminado');
    closeDeleteModal();
    await loadUsers();
    await nextTick();
    staggerIn('.um-row', { delay: 0.05 });
  } catch (err) {
    toast.error(err?.response?.data?.message || 'No se pudo eliminar el usuario');
    closeDeleteModal();
  } finally {
    submitting.value = false;
  }
};

const refresh = async () => {
  await loadUsers();
  await loadFormData();
  await nextTick();
  staggerIn('.um-row', { delay: 0.05 });
  toast.info('Lista actualizada');
};

const handleLogout = async () => {
  try {
    if (localStorage.getItem('auth_token')) await axios.post('/api/auth/logout');
  } catch {}
  localStorage.clear();
  delete axios.defaults.headers.common.Authorization;
  window.location.href = '/';
};

const columns = [
  { key: 'user', label: 'Usuario', width: '24%' },
  { key: 'contact', label: 'Contacto', width: '22%' },
  { key: 'role', label: 'Rol', width: '14%' },
  { key: 'academic', label: 'Académico', width: '20%' },
  { key: 'status', label: 'Estado', align: 'center', width: '10%' },
  { key: 'actions', label: 'Acciones', align: 'right', width: '10%' },
];
</script>

<template>
  <AppShell v-if="user" :user="user" page-title="Gestión de usuarios" @logout="handleLogout">
    <PageTransition>
      <div class="um">
        <AppPageHeader
          eyebrow="Módulo administrativo"
          title="Gestión de usuarios"
          description="Listar, editar, activar y eliminar cuentas de usuarios académicos."
        >
          <template #actions>
            <AppButton variant="secondary" :icon="RefreshCw" @click="refresh">Actualizar</AppButton>
            <AppButton variant="primary" :icon="UserPlus" @click="goTo('/usuarios/create')">
              Registrar nuevo
            </AppButton>
          </template>
        </AppPageHeader>

        <section class="um__stats">
          <AppCard padding="sm" class="um__stat">
            <div class="um__stat-icon um__stat-icon--primary"><Users :size="20" /></div>
            <div>
              <p class="um__stat-label">Total</p>
              <p class="um__stat-value">{{ stats.total }}</p>
            </div>
          </AppCard>
          <AppCard padding="sm" class="um__stat">
            <div class="um__stat-icon um__stat-icon--success"><CheckCircle2 :size="20" /></div>
            <div>
              <p class="um__stat-label">Activos</p>
              <p class="um__stat-value">{{ stats.active }}</p>
            </div>
          </AppCard>
          <AppCard padding="sm" class="um__stat">
            <div class="um__stat-icon um__stat-icon--danger"><Power :size="20" /></div>
            <div>
              <p class="um__stat-label">Inactivos</p>
              <p class="um__stat-value">{{ stats.inactive }}</p>
            </div>
          </AppCard>
          <AppCard padding="sm" class="um__stat">
            <div class="um__stat-icon um__stat-icon--warning"><ShieldAlert :size="20" /></div>
            <div>
              <p class="um__stat-label">Docentes</p>
              <p class="um__stat-value">{{ stats.teachers }}</p>
            </div>
          </AppCard>
        </section>

        <AppCard padding="md">
          <div class="um__filters">
            <AppInput
              v-model="searchQuery"
              type="text"
              placeholder="Buscar por nombre, correo, CI o teléfono..."
              :icon="Search"
              @update:modelValue="page = 1"
            />
            <AppSelect
              v-model="roleFilter"
              :options="roleOptions"
              placeholder="Todos los roles"
              @update:modelValue="page = 1"
            />
          </div>

          <AppTable
            :columns="columns"
            :rows="paginatedUsers"
            :loading="loading"
            row-key="IdUsuario"
            empty-title="Sin coincidencias"
            empty-description="No se encontraron usuarios con los filtros actuales."
          >
            <template #cell-user="{ row }">
              <div class="um__user">
                <AppAvatar :name="fullName(row)" :variant="roleVariant(row.IdRol)" size="md" />
                <div class="um__user-text">
                  <strong>{{ row.Nombre1 }} {{ row.Nombre2 || '' }}</strong>
                  <span class="um__user-sub">{{ row.Apellido1 }} {{ row.Apellido2 || '' }}</span>
                </div>
              </div>
            </template>

            <template #cell-contact="{ row }">
              <div class="um__contact">
                <span class="um__contact-row">
                  <Mail :size="13" />
                  <span>{{ row.Correo }}</span>
                </span>
                <span class="um__contact-row um__contact-row--muted">
                  <IdCard :size="13" />
                  <span>CI: {{ row.CI }}</span>
                </span>
                <span class="um__contact-row um__contact-row--muted">
                  <Phone :size="13" />
                  <span>{{ row.Telefono }}</span>
                </span>
              </div>
            </template>

            <template #cell-role="{ row }">
              <AppRoleBadge :role="roleVariant(row.IdRol)" :label="roleLabel(row)" />
            </template>

            <template #cell-academic="{ row }">
              <template v-if="Number(row.IdRol) === 3">
                <div class="um__academic">
                  <span class="um__academic-carrera">
                    <UserIcon :size="13" />
                    {{ carreraLabel(row.IdCarrera) }}
                  </span>
                  <AppBadge variant="student" size="sm">{{ modalidadLabel(row.IdModalidad) }}</AppBadge>
                </div>
              </template>
              <span v-else class="um__muted">—</span>
            </template>

            <template #cell-status="{ row }">
              <button
                type="button"
                :class="['um__status', row.Estado ? 'um__status--active' : 'um__status--inactive']"
                :title="row.Estado ? 'Desactivar usuario' : 'Activar usuario'"
                :aria-label="row.Estado ? `Desactivar a ${fullName(row)}` : `Activar a ${fullName(row)}`"
                @click="toggleStatus(row)"
              >
                <span :class="['um__status-dot', row.Estado ? 'um__status-dot--on' : 'um__status-dot--off']" aria-hidden="true"></span>
                {{ row.Estado ? 'Activo' : 'Inactivo' }}
              </button>
            </template>

            <template #cell-actions="{ row }">
              <div class="um__actions">
                <AppButton variant="ghost" size="sm" :icon="Pencil" aria-label="Editar usuario" @click="openEditModal(row)" />
                <AppButton variant="ghost" size="sm" :icon="Trash2" aria-label="Eliminar usuario" @click="confirmDelete(row)" />
              </div>
            </template>

            <template #empty>
              <AppEmptyState
                :icon="Users"
                title="Sin usuarios registrados"
                description="Crea el primer usuario del sistema para empezar a gestionar la comunidad académica."
              >
            <AppButton variant="primary" :icon="UserPlus" @click="goTo('/usuarios/create')">
              Registrar usuario
            </AppButton>
              </AppEmptyState>
            </template>
          </AppTable>

          <footer v-if="filteredUsers.length > 0" class="um__pagination">
            <p class="um__pagination-info">
              Mostrando <strong>{{ pageRange.from }}–{{ pageRange.to }}</strong> de <strong>{{ filteredUsers.length }}</strong> usuarios
            </p>
            <div class="um__pagination-controls">
              <AppButton variant="ghost" size="sm" :icon="ChevronLeft" :disabled="page === 1" @click="goToPage(page - 1)">
                Anterior
              </AppButton>
              <span class="um__pagination-page">Página {{ page }} / {{ totalPages }}</span>
              <AppButton variant="ghost" size="sm" :icon-right="ChevronRight" :disabled="page === totalPages" @click="goToPage(page + 1)">
                Siguiente
              </AppButton>
            </div>
          </footer>
        </AppCard>

        <AppButton variant="ghost" :icon="ArrowLeft" @click="goTo('/dashboard')">
          Volver al panel principal
        </AppButton>
      </div>
    </PageTransition>

    <AppModal :open="showEditModal" title="Editar usuario" size="lg" @close="closeEditModal">
      <form class="um__form" @submit.prevent="submitEdit">
        <div class="um__form-grid">
          <AppSelect
            v-model="editForm.IdRol"
            label="Rol (no editable)"
            :options="roleOptions"
            disabled
            hint="El rol no puede modificarse después del registro."
            required
          />
          <AppSelect
            v-if="isEditStudent"
            v-model="editForm.IdCarrera"
            label="Carrera"
            :options="carreraOptions"
            required
            placeholder="Seleccione una carrera"
            @update:modelValue="onEditRoleChange"
          />
          <AppSelect
            v-if="isEditStudent"
            v-model="editForm.IdModalidad"
            label="Modalidad"
            :options="modalidadOptions"
            required
            placeholder="Seleccione una modalidad"
          />

          <AppInput v-model="editForm.Correo" label="Correo" type="email" required autocomplete="email" />
          <AppInput v-model="editForm.CI" label="CI" required />
          <AppInput v-model="editForm.Telefono" label="Teléfono" required />
          <AppInput v-model="editForm.Nombre1" label="Nombre 1" required autocomplete="given-name" />
          <AppInput v-model="editForm.Nombre2" label="Nombre 2" autocomplete="additional-name" />
          <AppInput v-model="editForm.Apellido1" label="Apellido 1" required autocomplete="family-name" />
          <AppInput v-model="editForm.Apellido2" label="Apellido 2" autocomplete="family-name" />
          <AppInput v-model="editForm.Contrasena" label="Nueva contraseña" type="password" hint="Mínimo 6 caracteres. Déjalo vacío para mantener la actual." />
          <AppInput v-model="editForm.Contrasena_confirmation" label="Confirmar contraseña" type="password" />
        </div>
      </form>
      <template #footer>
        <AppButton variant="secondary" @click="closeEditModal">Cancelar</AppButton>
        <AppButton variant="primary" :icon="Pencil" :loading="submitting" @click="submitEdit">Guardar cambios</AppButton>
      </template>
    </AppModal>

    <AppModal :open="showDeleteModal" title="Confirmar eliminación" size="sm" @close="closeDeleteModal">
      <div class="um__delete">
        <AppAlert variant="warning" title="Esta acción no se puede deshacer">
          Si el usuario posee dependencias (inscripciones o notas), la eliminación física fallará por seguridad.
        </AppAlert>
        <div class="um__delete-card">
          <AppAvatar :name="fullName(userToDelete || {})" variant="primary" size="lg" />
          <div>
            <strong>{{ userToDelete?.Nombre1 }} {{ userToDelete?.Apellido1 }}</strong>
            <p class="um__delete-email">{{ userToDelete?.Correo }}</p>
          </div>
        </div>
      </div>
      <template #footer>
        <AppButton variant="secondary" @click="closeDeleteModal">Cancelar</AppButton>
        <AppButton variant="danger" :icon="Trash2" :loading="submitting" @click="submitDelete">Eliminar permanentemente</AppButton>
      </template>
    </AppModal>
  </AppShell>
</template>

<style scoped>
.um {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.um__stats {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 14px;
}

.um__stat {
  display: flex;
  align-items: center;
  gap: 12px;
}

.um__stat-icon {
  display: grid;
  place-items: center;
  width: 44px;
  height: 44px;
  border-radius: var(--radius-md);
  background: var(--color-primary-soft);
  color: var(--color-primary);
  flex-shrink: 0;
}
.um__stat-icon--success { background: var(--color-success-soft); color: var(--color-success); }
.um__stat-icon--danger { background: var(--color-danger-soft); color: var(--color-danger); }
.um__stat-icon--warning { background: var(--color-warning-soft); color: var(--color-warning); }

.um__stat-label {
  margin: 0;
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  font-weight: 600;
  color: var(--color-text-muted);
}

.um__stat-value {
  margin: 0;
  font-size: 1.4rem;
  font-weight: 800;
  color: var(--color-text-primary);
  line-height: 1;
}

.um__filters {
  display: grid;
  grid-template-columns: 1fr 240px;
  gap: 12px;
  margin-bottom: 18px;
}

.um__user {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
}

.um__user-text {
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.um__user-text strong {
  font-size: 0.92rem;
  color: var(--color-text-primary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.um__user-sub {
  font-size: 0.78rem;
  color: var(--color-text-muted);
}

.um__contact {
  display: flex;
  flex-direction: column;
  gap: 2px;
  font-size: 0.82rem;
  min-width: 0;
}

.um__contact-row {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: var(--color-text-primary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.um__contact-row--muted {
  color: var(--color-text-muted);
  font-size: 0.76rem;
}

.um__academic {
  display: flex;
  flex-direction: column;
  gap: 4px;
  align-items: flex-start;
}

.um__academic-carrera {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--color-text-primary);
}

.um__muted {
  color: var(--color-text-muted);
  font-size: 1rem;
}

.um__status {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: var(--radius-full);
  font-size: 0.78rem;
  font-weight: 700;
  border: 1px solid;
  cursor: pointer;
  transition: all var(--duration-fast) var(--ease-out);
  background: transparent;
  min-height: 32px;
}

.um__status--active {
  color: var(--color-success);
  border-color: var(--color-success-border);
  background: var(--color-success-soft);
}
.um__status--active:hover { background: var(--color-success); color: white; }

.um__status--inactive {
  color: var(--color-danger);
  border-color: var(--color-danger-border);
  background: var(--color-danger-soft);
}
.um__status--inactive:hover { background: var(--color-danger); color: white; }

.um__status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}
.um__status-dot--on { background: var(--color-success); box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.18); }
.um__status-dot--off { background: var(--color-danger); box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.18); }

.um__actions {
  display: inline-flex;
  gap: 4px;
  justify-content: flex-end;
}

.um__pagination {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  padding-top: 16px;
  margin-top: 16px;
  border-top: 1px solid var(--color-border-subtle);
}

.um__pagination-info {
  margin: 0;
  font-size: 0.82rem;
  color: var(--color-text-muted);
}

.um__pagination-controls {
  display: flex;
  align-items: center;
  gap: 10px;
}

.um__pagination-page {
  font-size: 0.82rem;
  font-weight: 600;
  color: var(--color-text-secondary);
  min-width: 96px;
  text-align: center;
}

.um__form {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.um__form-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 14px;
}

.um__delete {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.um__delete-card {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px 16px;
  background: var(--color-surface-1);
  border: 1px solid var(--color-border-subtle);
  border-radius: var(--radius-md);
}

.um__delete-card strong {
  display: block;
  font-size: 1rem;
  color: var(--color-text-primary);
  margin-bottom: 2px;
}

.um__delete-email {
  margin: 0;
  font-size: 0.85rem;
  color: var(--color-text-muted);
}

@media (max-width: 1024px) {
  .um__stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .um__filters { grid-template-columns: 1fr; }
}

@media (max-width: 640px) {
  .um__form-grid { grid-template-columns: 1fr; }
  .um__pagination { flex-direction: column; align-items: stretch; }
  .um__pagination-controls { justify-content: space-between; }
}
</style>
