<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import axios from 'axios';
import {
  Users,
  Search,
  ArrowLeft,
  GraduationCap,
  BookOpen,
  MapPin,
  Clock,
  Calendar,
  User as UserIcon,
  IdCard,
  Mail,
  Phone,
  Hash,
  CheckCircle2,
  XCircle,
  Sparkles,
  Eye,
} from '@lucide/vue';
import AppShell from './layout/AppShell.vue';
import PageTransition from './layout/PageTransition.vue';
import AppCard from './ui/AppCard.vue';
import AppButton from './ui/AppButton.vue';
import AppInput from './ui/AppInput.vue';
import AppAlert from './ui/AppAlert.vue';
import AppPageHeader from './ui/AppPageHeader.vue';
import AppModal from './ui/AppModal.vue';
import AppTable from './ui/AppTable.vue';
import AppAvatar from './ui/AppAvatar.vue';
import AppRoleBadge from './ui/AppRoleBadge.vue';
import AppBadge from './ui/AppBadge.vue';
import AppEmptyState from './ui/AppEmptyState.vue';
import AppSpinner from './ui/AppSpinner.vue';
import { toast } from '../lib/toast.js';
import { useGsap } from '../composables/useGsap.js';

const { staggerIn } = useGsap();

const user = ref(null);
const users = ref([]);
const selectedUser = ref(null);
const academicData = ref({ usuario: null, cursos: [] });
const userSearchQuery = ref('');
const activeRoleFilter = ref('');
const loadingUsers = ref(false);
const loadingDetails = ref(false);

const showEnrolledModal = ref(false);
const showDetailsModal = ref(false);
const selectedCourse = ref(null);

const roleFilters = [
  { key: '', label: 'Todos' },
  { key: '2', label: 'Docentes' },
  { key: '3', label: 'Estudiantes' },
];

const filteredUsers = computed(() => {
  const query = userSearchQuery.value.toLowerCase().trim();
  return users.value.filter((u) => {
    if (activeRoleFilter.value && String(u.IdRol) !== String(activeRoleFilter.value)) return false;
    if (!query) return true;
    const fullName = `${u.Nombre1} ${u.Apellido1}`.toLowerCase();
    const ci = String(u.CI || '').toLowerCase();
    return fullName.includes(query) || ci.includes(query);
  });
});

const isTeacher = computed(() => Number(selectedUser.value?.IdRol) === 2);
const isStudent = computed(() => Number(selectedUser.value?.IdRol) === 3);

const fullName = (u) => `${u.Nombre1} ${u.Apellido1 || ''}`.trim() || 'Sin nombre';
const initials = (u) => `${u.Nombre1?.[0] || ''}${u.Apellido1?.[0] || ''}`.toUpperCase();
const userVariant = (u) => (Number(u.IdRol) === 2 ? 'teacher' : 'student');

const formatDate = (d) => {
  if (!d) return '—';
  return new Date(d).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' });
};
const formatTime = (t) => (t ? t.substring(0, 5) : '');

const studentColumns = [
  { key: 'student', label: 'Estudiante', width: '28%' },
  { key: 'ci', label: 'CI', width: '12%' },
  { key: 'career', label: 'Carrera', width: '18%' },
  { key: 'mode', label: 'Modalidad', width: '14%' },
  { key: 'date', label: 'Inscrito el', width: '14%' },
  { key: 'status', label: 'Estado', align: 'center', width: '14%' },
];

onMounted(async () => {
  const token = localStorage.getItem('auth_token');
  if (!token) {
    window.location.href = '/';
    return;
  }
  axios.defaults.headers.common.Authorization = `Bearer ${token}`;

  const stored = localStorage.getItem('auth_user');
  if (stored) {
    user.value = JSON.parse(stored);
    if (user.value.IdRol === 2) {
      window.location.href = '/docente/cursos';
      return;
    }
  }
  await loadUsers();
});

const loadUsers = async () => {
  loadingUsers.value = true;
  try {
    const { data } = await axios.get('/api/usuarios');
    users.value = data?.data ?? data ?? [];
  } catch (err) {
    toast.error('No se pudo cargar la lista de usuarios');
  } finally {
    loadingUsers.value = false;
  }
};

const selectUser = async (u) => {
  selectedUser.value = u;
  academicData.value = { usuario: null, cursos: [] };
  loadingDetails.value = true;
  try {
    const { data } = await axios.get(`/api/cursos-materias/usuario/${u.IdUsuario}`);
    academicData.value = data?.data ?? { usuario: null, cursos: [] };
    await nextTick();
    staggerIn('.cv-course', { delay: 0.05 });
  } catch (err) {
    toast.error('No se pudo obtener la programación académica del usuario');
  } finally {
    loadingDetails.value = false;
  }
};

const openEnrolledModal = (course) => {
  selectedCourse.value = course;
  showEnrolledModal.value = true;
};
const closeEnrolledModal = () => {
  showEnrolledModal.value = false;
  selectedCourse.value = null;
};

const openDetailsModal = (course) => {
  selectedCourse.value = course;
  showDetailsModal.value = true;
};
const closeDetailsModal = () => {
  showDetailsModal.value = false;
  selectedCourse.value = null;
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
  <AppShell v-if="user" :user="user" page-title="Cursos por usuario" @logout="handleLogout">
    <PageTransition>
      <div class="cv">
        <AppPageHeader
          eyebrow="RF06 · Visualización"
          title="Cursos por usuario"
          description="Consulta el historial y programación de clases de estudiantes y docentes en el ciclo lectivo."
        >
          <template #actions>
            <AppButton variant="secondary" :icon="ArrowLeft" @click="window.location.href = '/index'">
              Volver al panel
            </AppButton>
          </template>
        </AppPageHeader>

        <AppAlert v-if="!selectedUser" variant="info" :title="`${filteredUsers.length} usuarios disponibles`">
          Selecciona un estudiante o docente en el panel lateral para ver su información académica.
        </AppAlert>

        <div class="cv__layout">
          <AppCard padding="none" class="cv__sidebar">
            <div class="cv__sidebar-head">
              <AppInput
                v-model="userSearchQuery"
                type="text"
                placeholder="Buscar por nombre o CI..."
                :icon="Search"
                size="sm"
              />
              <div class="cv__roles" role="tablist">
                <button
                  v-for="r in roleFilters"
                  :key="r.key"
                  type="button"
                  role="tab"
                  :aria-selected="activeRoleFilter === r.key"
                  :class="['cv__role', activeRoleFilter === r.key && 'cv__role--active']"
                  @click="activeRoleFilter = r.key"
                >
                  {{ r.label }}
                </button>
              </div>
            </div>

            <ul class="cv__user-list">
              <li v-if="loadingUsers" class="cv__user-state">
                <AppSpinner size="sm" /> Cargando usuarios…
              </li>
              <li v-else-if="filteredUsers.length === 0" class="cv__user-state">
                <AppEmptyState
                  :icon="Users"
                  title="Sin resultados"
                  description="No se encontraron usuarios con los filtros aplicados."
                />
              </li>
              <li
                v-for="u in filteredUsers"
                :key="u.IdUsuario"
                :class="['cv__user', selectedUser?.IdUsuario === u.IdUsuario && 'cv__user--selected']"
                role="button"
                tabindex="0"
                @click="selectUser(u)"
                @keydown.enter="selectUser(u)"
              >
                <AppAvatar :name="fullName(u)" :variant="userVariant(u)" size="sm" />
                <div class="cv__user-text">
                  <strong>{{ u.Nombre1 }} {{ u.Apellido1 }}</strong>
                  <span class="cv__user-sub">CI: {{ u.CI }}</span>
                </div>
                <AppRoleBadge
                  :role="userVariant(u)"
                  :label="Number(u.IdRol) === 2 ? 'Docente' : 'Estudiante'"
                  size="sm"
                />
              </li>
            </ul>
          </AppCard>

          <section class="cv__main">
            <div v-if="!selectedUser" class="cv__empty">
              <AppEmptyState
                :icon="Sparkles"
                title="Selecciona un usuario"
                description="Elige un estudiante o docente en el panel lateral para examinar sus cursos."
              />
            </div>

            <div v-else-if="loadingDetails" class="cv__loading">
              <AppSpinner size="lg" />
              <p>Obteniendo información académica y cursos asignados…</p>
            </div>

            <div v-else class="cv__details">
              <AppCard padding="lg" class="cv__profile">
                <div class="cv__profile-head">
                  <AppAvatar :name="fullName(selectedUser)" :variant="userVariant(selectedUser)" size="xl" />
                  <div>
                    <h2>{{ selectedUser.Nombre1 }} {{ selectedUser.Nombre2 || '' }} {{ selectedUser.Apellido1 }} {{ selectedUser.Apellido2 || '' }}</h2>
                    <div class="cv__profile-meta">
                      <AppRoleBadge
                        :role="userVariant(selectedUser)"
                        :label="isTeacher ? 'Docente asignado' : 'Estudiante regular'"
                      />
                    </div>
                  </div>
                </div>
                <div class="cv__profile-grid">
                  <div class="cv__profile-item">
                    <span class="cv__profile-label"><IdCard :size="13" /> Cédula</span>
                    <span class="cv__profile-value">{{ selectedUser.CI }}</span>
                  </div>
                  <div class="cv__profile-item">
                    <span class="cv__profile-label"><Mail :size="13" /> Correo</span>
                    <span class="cv__profile-value">{{ selectedUser.Correo }}</span>
                  </div>
                  <div v-if="selectedUser.Telefono" class="cv__profile-item">
                    <span class="cv__profile-label"><Phone :size="13" /> Teléfono</span>
                    <span class="cv__profile-value">{{ selectedUser.Telefono }}</span>
                  </div>
                  <template v-if="isStudent">
                    <div class="cv__profile-item">
                      <span class="cv__profile-label"><GraduationCap :size="13" /> Carrera</span>
                      <span class="cv__profile-value">{{ selectedUser.Carrera || 'No registrada' }}</span>
                    </div>
                    <div class="cv__profile-item">
                      <span class="cv__profile-label"><Hash :size="13" /> Modalidad</span>
                      <span class="cv__profile-value">{{ selectedUser.Modalidad || 'No registrada' }}</span>
                    </div>
                  </template>
                </div>
              </AppCard>

              <header class="cv__courses-head">
                <h3>
                  <BookOpen :size="18" />
                  Asignaturas y cursos programados
                  <span class="cv__count">({{ academicData.cursos.length }})</span>
                </h3>
              </header>

              <AppEmptyState
                v-if="academicData.cursos.length === 0"
                :icon="BookOpen"
                title="Sin cursos registrados"
                description="Este usuario no tiene materias registradas en su currícula para el ciclo actual."
              />

              <div v-else class="cv__courses">
                <AppCard
                  v-for="c in academicData.cursos"
                  :key="c.IdCursoMateria"
                  :class="['cv-course', 'cv__course', !c.Estado && 'cv__course--inactive']"
                  padding="none"
                  interactive
                >
                  <header class="cv__course-head">
                    <div>
                      <span class="cv__course-code"><Hash :size="11" /> {{ c.Materia?.CodigoMateria }}</span>
                      <h4>{{ c.Materia?.Nombre }}</h4>
                    </div>
                    <AppBadge :variant="c.Estado ? 'success' : 'danger'" size="sm">
                      <component :is="c.Estado ? CheckCircle2 : XCircle" :size="11" />
                      {{ c.Estado ? 'Activo' : 'Desactivado' }}
                    </AppBadge>
                  </header>

                  <div class="cv__course-body">
                    <div class="cv__info">
                      <MapPin :size="14" />
                      <span>
                        <strong>Aula:</strong>
                        {{ c.Curso?.Nombre || `Aula ${c.Curso?.Aula}` }} (Piso {{ c.Curso?.Piso }})
                      </span>
                    </div>
                    <div class="cv__info">
                      <Clock :size="14" />
                      <span>
                        <strong>Turno:</strong>
                        {{ c.Turno?.Nombre }} ({{ formatTime(c.Turno?.HoraInicio) }} - {{ formatTime(c.Turno?.HoraFin) }})
                      </span>
                    </div>
                    <div class="cv__info">
                      <Calendar :size="14" />
                      <span>
                        <strong>Vigencia:</strong>
                        {{ formatDate(c.FechaInicio) }} al {{ formatDate(c.FechaFin) }}
                      </span>
                    </div>

                    <template v-if="isStudent">
                      <hr class="cv__divider" />
                      <div class="cv__info"><UserIcon :size="14" /><span><strong>Docente:</strong> {{ c.Docente?.Nombre }}</span></div>
                      <div class="cv__info"><Calendar :size="14" /><span><strong>Inscripción:</strong> {{ formatDate(c.Inscripcion?.Fecha) }}</span></div>
                      <div class="cv__info">
                        <CheckCircle2 :size="14" />
                        <span>
                          <strong>Estado:</strong>
                          <AppBadge :variant="c.Inscripcion?.Aprobado ? 'success' : 'warning'" size="sm">
                            {{ c.Inscripcion?.Aprobado ? 'Aprobada' : 'En curso' }}
                          </AppBadge>
                        </span>
                      </div>
                    </template>

                    <template v-if="isTeacher">
                      <hr class="cv__divider" />
                      <div class="cv__slots">
                        <div class="cv__slots-head">
                          <strong>Cupos:</strong>
                          <span>{{ c.Inscritos }} / {{ c.MaxInscritos }} alumnos</span>
                        </div>
                        <div class="cv__progress">
                          <div
                            :class="['cv__progress-fill', c.Inscritos >= c.MaxInscritos && 'cv__progress-fill--full']"
                            :style="{ width: `${Math.min((c.Inscritos / c.MaxInscritos) * 100, 100)}%` }"
                          ></div>
                        </div>
                      </div>
                    </template>
                  </div>

                  <footer class="cv__course-foot">
                    <AppButton
                      v-if="isTeacher"
                      variant="primary"
                      :icon="Users"
                      block
                      @click="openEnrolledModal(c)"
                    >
                      Ver alumnos inscritos
                    </AppButton>
                    <AppButton
                      v-else
                      variant="secondary"
                      :icon="Eye"
                      block
                      @click="openDetailsModal(c)"
                    >
                      Ver detalles completos
                    </AppButton>
                  </footer>
                </AppCard>
              </div>
            </div>
          </section>
        </div>
      </div>
    </PageTransition>

    <AppModal :open="showEnrolledModal" size="lg" @close="closeEnrolledModal">
      <template #header>
        <div>
          <h2 class="cv__modal-title">Alumnos inscritos</h2>
          <p class="cv__modal-sub">{{ selectedCourse?.Materia?.Nombre }} · {{ selectedCourse?.Materia?.CodigoMateria }}</p>
        </div>
      </template>
      <AppEmptyState
        v-if="!selectedCourse?.Alumnos || selectedCourse.Alumnos.length === 0"
        :icon="Users"
        title="Sin alumnos inscritos"
        description="Aún no hay estudiantes registrados en este curso."
      />
      <AppTable
        v-else
        :columns="studentColumns"
        :rows="selectedCourse.Alumnos"
        row-key="IdUsuario"
      >
        <template #cell-student="{ row }">
          <div class="cv__student">
            <AppAvatar :name="row.Nombre" variant="student" size="sm" />
            <div>
              <strong>{{ row.Nombre }}</strong>
              <p class="cv__student-mail">{{ row.Correo }}</p>
            </div>
          </div>
        </template>
        <template #cell-career="{ row }">
          <span class="cv__career">{{ row.Carrera }}</span>
        </template>
        <template #cell-mode="{ row }">
          <AppBadge variant="student" size="sm">{{ row.Modalidad }}</AppBadge>
        </template>
        <template #cell-date="{ row }">{{ formatDate(row.FechaInscripcion) }}</template>
        <template #cell-status="{ row }">
          <AppBadge :variant="row.Aprobado ? 'success' : 'warning'" size="sm">
            {{ row.Aprobado ? 'Aprobado' : 'Cursando' }}
          </AppBadge>
        </template>
      </AppTable>
    </AppModal>

    <AppModal :open="showDetailsModal" size="md" @close="closeDetailsModal">
      <template #header>
        <div>
          <h2 class="cv__modal-title">Detalles del curso</h2>
          <p class="cv__modal-sub">{{ selectedCourse?.Materia?.Nombre }}</p>
        </div>
      </template>
      <div class="cv__details-grid">
        <div class="cv__detail-item">
          <span class="cv__detail-label">Asignatura</span>
          <span class="cv__detail-value">{{ selectedCourse?.Materia?.Nombre }} ({{ selectedCourse?.Materia?.CodigoMateria }})</span>
        </div>
        <div class="cv__detail-item">
          <span class="cv__detail-label">Cátedra a cargo</span>
          <span class="cv__detail-value">{{ selectedCourse?.Docente?.Nombre }} ({{ selectedCourse?.Docente?.Correo }})</span>
        </div>
        <div class="cv__detail-item">
          <span class="cv__detail-label">Aula y ubicación</span>
          <span class="cv__detail-value">{{ selectedCourse?.Curso?.Nombre || `Aula ${selectedCourse?.Curso?.Aula}` }} (Piso {{ selectedCourse?.Curso?.Piso }})</span>
        </div>
        <div class="cv__detail-item">
          <span class="cv__detail-label">Turno y horario</span>
          <span class="cv__detail-value">{{ selectedCourse?.Turno?.Nombre }} ({{ formatTime(selectedCourse?.Turno?.HoraInicio) }} - {{ formatTime(selectedCourse?.Turno?.HoraFin) }})</span>
        </div>
        <div class="cv__detail-item">
          <span class="cv__detail-label">Vigencia</span>
          <span class="cv__detail-value">{{ formatDate(selectedCourse?.FechaInicio) }} al {{ formatDate(selectedCourse?.FechaFin) }}</span>
        </div>
        <div class="cv__detail-item">
          <span class="cv__detail-label">Fecha de inscripción</span>
          <span class="cv__detail-value">{{ formatDate(selectedCourse?.Inscripcion?.Fecha) }}</span>
        </div>
        <div class="cv__detail-item">
          <span class="cv__detail-label">Estado de aprobación</span>
          <span class="cv__detail-value">
            <AppBadge :variant="selectedCourse?.Inscripcion?.Aprobado ? 'success' : 'warning'" size="sm">
              {{ selectedCourse?.Inscripcion?.Aprobado ? 'Aprobada' : 'En curso' }}
            </AppBadge>
          </span>
        </div>
      </div>
    </AppModal>
  </AppShell>
</template>

<style scoped>
.cv {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.cv__layout {
  display: grid;
  grid-template-columns: 340px 1fr;
  gap: 20px;
  min-height: 70vh;
}

.cv__sidebar {
  display: flex;
  flex-direction: column;
  max-height: 75vh;
  overflow: hidden;
}

.cv__sidebar-head {
  padding: 16px;
  border-bottom: 1px solid var(--color-border-subtle);
  display: flex;
  flex-direction: column;
  gap: 12px;
  background: rgba(28, 39, 66, 0.3);
}

.cv__roles {
  display: flex;
  gap: 4px;
  padding: 4px;
  background: var(--color-surface-3);
  border-radius: var(--radius-md);
}

.cv__role {
  flex: 1;
  padding: 6px 10px;
  background: transparent;
  border: 0;
  color: var(--color-text-muted);
  font-size: 0.78rem;
  font-weight: 700;
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: all var(--duration-fast) var(--ease-out);
  min-height: 32px;
}

.cv__role:hover { color: var(--color-text-primary); }
.cv__role--active {
  background: var(--color-primary);
  color: white;
  box-shadow: var(--shadow-sm);
}

.cv__user-list {
  list-style: none;
  margin: 0;
  padding: 8px;
  overflow-y: auto;
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.cv__user-state {
  padding: 24px 16px;
  text-align: center;
  color: var(--color-text-muted);
  font-size: 0.88rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.cv__user {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: var(--radius-md);
  cursor: pointer;
  border: 1px solid transparent;
  transition: all var(--duration-fast) var(--ease-out);
}

.cv__user:hover {
  background: var(--color-surface-3);
}

.cv__user--selected {
  background: var(--color-primary-soft);
  border-color: var(--color-primary-border);
}

.cv__user-text {
  flex: 1;
  min-width: 0;
}

.cv__user-text strong {
  display: block;
  font-size: 0.88rem;
  color: var(--color-text-primary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.cv__user-sub {
  font-size: 0.74rem;
  color: var(--color-text-muted);
}

.cv__main {
  min-width: 0;
}

.cv__empty,
.cv__loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 14px;
  padding: 60px 24px;
  min-height: 50vh;
  text-align: center;
}

.cv__loading p {
  margin: 0;
  color: var(--color-text-muted);
}

.cv__details {
  display: flex;
  flex-direction: column;
  gap: 22px;
  animation: fadeIn 0.3s var(--ease-out);
}

.cv__profile-head {
  display: flex;
  align-items: center;
  gap: 16px;
  padding-bottom: 18px;
  border-bottom: 1px solid var(--color-border-subtle);
  margin-bottom: 18px;
}

.cv__profile-head h2 {
  margin: 0 0 8px;
  font-size: 1.3rem;
  font-weight: 800;
  color: var(--color-text-primary);
  letter-spacing: -0.01em;
}

.cv__profile-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 14px;
}

.cv__profile-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.cv__profile-label {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  font-weight: 600;
  color: var(--color-text-muted);
}

.cv__profile-value {
  font-size: 0.95rem;
  color: var(--color-text-primary);
  font-weight: 600;
}

.cv__courses-head h3 {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin: 0;
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--color-text-primary);
}

.cv__count {
  font-size: 0.85rem;
  color: var(--color-text-muted);
  font-weight: 500;
  margin-left: 4px;
}

.cv__courses {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 18px;
}

.cv__course {
  display: flex;
  flex-direction: column;
}

.cv__course--inactive {
  opacity: 0.6;
}

.cv__course-head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 10px;
  padding: 16px 20px;
  background: rgba(28, 39, 66, 0.3);
  border-bottom: 1px solid var(--color-border-subtle);
}

.cv__course-code {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 0.74rem;
  font-weight: 700;
  color: var(--color-primary);
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
  text-transform: uppercase;
}

.cv__course-head h4 {
  margin: 4px 0 0;
  font-size: 1.02rem;
  color: var(--color-text-primary);
  line-height: 1.3;
  font-weight: 700;
}

.cv__course-body {
  padding: 18px 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  flex: 1;
}

.cv__info {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  font-size: 0.85rem;
  color: var(--color-text-secondary);
  line-height: 1.5;
}

.cv__info strong {
  color: var(--color-text-primary);
  font-weight: 700;
  margin-right: 4px;
}

.cv__divider {
  border: 0;
  height: 1px;
  background: var(--color-border-subtle);
  margin: 6px 0;
}

.cv__slots {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.cv__slots-head {
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  font-size: 0.88rem;
  color: var(--color-text-secondary);
}

.cv__slots-head strong {
  color: var(--color-text-primary);
  font-weight: 700;
}

.cv__progress {
  height: 8px;
  background: var(--color-surface-3);
  border-radius: var(--radius-full);
  overflow: hidden;
}

.cv__progress-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--color-success) 0%, #34d399 100%);
  border-radius: var(--radius-full);
  transition: width var(--duration-base) var(--ease-out);
}

.cv__progress-fill--full {
  background: linear-gradient(90deg, var(--color-danger) 0%, #f87171 100%);
}

.cv__course-foot {
  padding: 12px 20px 16px;
}

.cv__modal-title {
  margin: 0 0 4px;
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--color-text-primary);
}

.cv__modal-sub {
  margin: 0;
  font-size: 0.82rem;
  color: var(--color-text-muted);
}

.cv__student {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 0;
}

.cv__student strong {
  display: block;
  font-size: 0.88rem;
  color: var(--color-text-primary);
}

.cv__student-mail {
  margin: 0;
  font-size: 0.74rem;
  color: var(--color-text-muted);
}

.cv__career {
  font-size: 0.85rem;
  color: var(--color-text-primary);
  font-weight: 600;
}

.cv__details-grid {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.cv__detail-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding-bottom: 12px;
  border-bottom: 1px solid var(--color-border-subtle);
}

.cv__detail-item:last-child { border-bottom: 0; }

.cv__detail-label {
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--color-text-muted);
}

.cv__detail-value {
  font-size: 0.95rem;
  color: var(--color-text-primary);
  font-weight: 600;
  line-height: 1.5;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 1024px) {
  .cv__layout { grid-template-columns: 1fr; }
  .cv__sidebar { max-height: 50vh; }
}
</style>
