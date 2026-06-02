<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import axios from 'axios';
import {
  CalendarClock,
  Plus,
  Search,
  ArrowLeft,
  RefreshCw,
  Pencil,
  Trash2,
  BookOpen,
  MapPin,
  User as UserIcon,
  Clock,
  Calendar,
  Users,
  Power,
  ChevronLeft,
  ChevronRight,
  AlertTriangle,
  Hash,
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
import AppBadge from './ui/AppBadge.vue';
import AppEmptyState from './ui/AppEmptyState.vue';
import { toast } from '../lib/toast.js';
import { useGsap } from '../composables/useGsap.js';

const { staggerIn } = useGsap();

const user = ref(null);
const cursosMaterias = ref([]);
const cursos = ref([]);
const materias = ref([]);
const docentes = ref([]);
const turnos = ref([]);
const loading = ref(false);
const submitting = ref(false);
const searchQuery = ref('');
const turnoFilter = ref('');

const showFormModal = ref(false);
const showDeleteModal = ref(false);
const isEditing = ref(false);
const cmToDelete = ref(null);
const formErrors = ref([]);

function emptyForm() {
  return {
    IdCursoMateria: null,
    IdCurso: '',
    IdMateria: '',
    IdDocente: '',
    IdTurno: '',
    FechaInicio: '',
    FechaFin: '',
    MaxInscritos: 40,
  };
}

const form = ref(emptyForm());

const turnoOptions = computed(() =>
  turnos.value.map((t) => ({
    Id: t.IdTurno,
    Nombre: `${t.Nombre} (${formatTime(t.HoraInicio)} - ${formatTime(t.HoraFin)})`,
  }))
);
const materiaOptions = computed(() =>
  materias.value.map((m) => ({ Id: m.IdMateria, Nombre: `[${m.CodigoMateria}] ${m.Nombre}` }))
);
const cursoOptions = computed(() =>
  cursos.value.map((c) => ({ Id: c.IdCurso, Nombre: `${c.Nombre} (Piso ${c.Piso})` }))
);
const docenteOptions = computed(() =>
  docentes.value.map((d) => ({ Id: d.IdUsuario, Nombre: d.Nombre }))
);

const filteredCursos = computed(() => {
  const query = searchQuery.value.toLowerCase().trim();
  return cursosMaterias.value.filter((cm) => {
    if (turnoFilter.value && Number(cm.IdTurno) !== Number(turnoFilter.value)) return false;
    if (!query) return true;
    const materiaName = (cm.Materia?.Nombre || '').toLowerCase();
    const materiaCode = (cm.Materia?.CodigoMateria || '').toLowerCase();
    const teacher = (cm.Docente?.Nombre || '').toLowerCase();
    const aula = (cm.Curso?.Aula || '').toLowerCase();
    return materiaName.includes(query) || materiaCode.includes(query) || teacher.includes(query) || aula.includes(query);
  });
});

const stats = computed(() => {
  const total = cursosMaterias.value.length;
  const active = cursosMaterias.value.filter((c) => c.Estado).length;
  const inactive = total - active;
  const full = cursosMaterias.value.filter((c) => Number(c.Inscritos) >= Number(c.MaxInscritos)).length;
  return { total, active, inactive, full };
});

const page = ref(1);
const pageSize = 8;
const totalPages = computed(() => Math.max(1, Math.ceil(filteredCursos.value.length / pageSize)));
const paginatedCursos = computed(() => {
  const start = (page.value - 1) * pageSize;
  return filteredCursos.value.slice(start, start + pageSize);
});
const pageRange = computed(() => {
  const from = filteredCursos.value.length === 0 ? 0 : (page.value - 1) * pageSize + 1;
  const to = Math.min(page.value * pageSize, filteredCursos.value.length);
  return { from, to };
});

const goToPage = (p) => {
  if (p < 1 || p > totalPages.value) return;
  page.value = p;
};

const columns = [
  { key: 'materia', label: 'Materia', width: '20%' },
  { key: 'aula', label: 'Aula', width: '12%' },
  { key: 'docente', label: 'Docente', width: '16%' },
  { key: 'turno', label: 'Turno', width: '14%' },
  { key: 'vigencia', label: 'Vigencia', width: '14%' },
  { key: 'cupos', label: 'Cupos', align: 'center', width: '10%' },
  { key: 'status', label: 'Estado', align: 'center', width: '8%' },
  { key: 'actions', label: 'Acciones', align: 'right', width: '6%' },
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

  await init();
  await nextTick();
  staggerIn('.cm-row', { delay: 0.05 });
});

const init = async () => {
  loading.value = true;
  try {
    await Promise.all([loadCursosMaterias(), loadFormData()]);
  } finally {
    loading.value = false;
  }
};

const loadCursosMaterias = async () => {
  try {
    const { data } = await axios.get('/api/cursos-materias');
    cursosMaterias.value = data?.data ?? data ?? [];
    page.value = 1;
  } catch (err) {
    toast.error('No se pudo cargar la programación de los cursos');
  }
};

const loadFormData = async () => {
  try {
    const { data } = await axios.get('/api/cursos-materias/form-data');
    cursos.value = data?.cursos ?? [];
    materias.value = data?.materias ?? [];
    docentes.value = data?.docentes ?? [];
    turnos.value = data?.turnos ?? [];
  } catch (err) {
    console.error('Error al cargar datos auxiliares:', err);
  }
};

const formatDate = (d) => {
  if (!d) return '—';
  return new Date(d).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' });
};
const formatTime = (t) => (t ? t.substring(0, 5) : '');

const openCreate = () => {
  isEditing.value = false;
  formErrors.value = [];
  form.value = emptyForm();
  showFormModal.value = true;
};

const openEdit = (cm) => {
  isEditing.value = true;
  formErrors.value = [];
  form.value = {
    IdCursoMateria: cm.IdCursoMateria,
    IdCurso: cm.IdCurso,
    IdMateria: cm.IdMateria,
    IdDocente: cm.IdDocente,
    IdTurno: cm.IdTurno,
    FechaInicio: cm.FechaInicio ? cm.FechaInicio.substring(0, 10) : '',
    FechaFin: cm.FechaFin ? cm.FechaFin.substring(0, 10) : '',
    MaxInscritos: cm.MaxInscritos,
  };
  showFormModal.value = true;
};

const closeFormModal = () => {
  showFormModal.value = false;
  formErrors.value = [];
};

const validateForm = () => {
  const errors = [];
  if (!form.value.IdMateria) errors.push('Seleccione una materia/asignatura.');
  if (!form.value.IdCurso) errors.push('Seleccione un aula/ubicación.');
  if (!form.value.IdDocente) errors.push('Seleccione un docente.');
  if (!form.value.IdTurno) errors.push('Seleccione un turno/horario.');
  if (!form.value.FechaInicio) errors.push('La fecha de inicio es obligatoria.');
  if (!form.value.FechaFin) errors.push('La fecha de finalización es obligatoria.');
  if (form.value.FechaInicio && form.value.FechaFin) {
    const start = new Date(form.value.FechaInicio);
    const end = new Date(form.value.FechaFin);
    if (isNaN(start.getTime())) errors.push('La fecha de inicio no es válida.');
    if (isNaN(end.getTime())) errors.push('La fecha de finalización no es válida.');
    if (!isNaN(start.getTime()) && !isNaN(end.getTime()) && end < start) {
      errors.push('La fecha de fin no puede ser anterior a la fecha de inicio.');
    }
  }
  if (!form.value.MaxInscritos || form.value.MaxInscritos < 1) {
    errors.push('El cupo máximo debe ser mayor o igual a 1.');
  }
  return errors;
};

const submit = async () => {
  formErrors.value = validateForm();
  if (formErrors.value.length) {
    toast.error('Revisa los campos del formulario');
    return;
  }

  submitting.value = true;
  try {
    const payload = { ...form.value };
    let response;
    if (isEditing.value) {
      response = await axios.put(`/api/cursos-materias/${form.value.IdCursoMateria}`, payload);
    } else {
      response = await axios.post('/api/cursos-materias', payload);
    }
    toast.success(response?.data?.message || 'Programación guardada');
    closeFormModal();
    await loadCursosMaterias();
    await nextTick();
    staggerIn('.cm-row', { delay: 0.05 });
  } catch (err) {
    if (err?.response?.status === 422) {
      const apiErrors = err.response?.data?.errors;
      formErrors.value = apiErrors ? Object.values(apiErrors).flat() : [err.response?.data?.message || 'Datos inválidos.'];
    } else {
      formErrors.value = [err?.response?.data?.message || 'No se pudo guardar la programación.'];
    }
  } finally {
    submitting.value = false;
  }
};

const toggleStatus = async (cm) => {
  const previous = cm.Estado;
  cm.Estado = !previous;
  try {
    const { data } = await axios.patch(`/api/cursos-materias/${cm.IdCursoMateria}/toggle-status`);
    cm.Estado = data?.Estado ?? !previous;
    toast.success(data?.message || 'Estado actualizado');
  } catch (err) {
    cm.Estado = previous;
    toast.error(err?.response?.data?.message || 'No se pudo actualizar el estado');
  }
};

const confirmDelete = (cm) => {
  cmToDelete.value = cm;
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  cmToDelete.value = null;
};

const submitDelete = async () => {
  if (!cmToDelete.value) return;
  submitting.value = true;
  try {
    const { data } = await axios.delete(`/api/cursos-materias/${cmToDelete.value.IdCursoMateria}`);
    const variant = data?.action === 'deactivated' ? 'warning' : 'success';
    toast.show(data?.message || 'Programación procesada', variant);
    closeDeleteModal();
    await loadCursosMaterias();
    await nextTick();
    staggerIn('.cm-row', { delay: 0.05 });
  } catch (err) {
    toast.error(err?.response?.data?.message || 'No se pudo procesar la eliminación');
    closeDeleteModal();
  } finally {
    submitting.value = false;
  }
};

const refresh = async () => {
  await init();
  await nextTick();
  staggerIn('.cm-row', { delay: 0.05 });
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
</script>

<template>
  <AppShell v-if="user" :user="user" page-title="Gestión de cursos" @logout="handleLogout">
    <PageTransition>
      <div class="cm">
        <AppPageHeader
          eyebrow="RF04 · Módulo académico"
          title="Gestión de cursos programados"
          description="Programa materias, asigna docentes, aulas y turnos para el ciclo lectivo."
        >
          <template #actions>
            <AppButton variant="secondary" :icon="RefreshCw" @click="refresh">Actualizar</AppButton>
            <AppButton variant="primary" :icon="Plus" @click="openCreate">Programar nuevo</AppButton>
          </template>
        </AppPageHeader>

        <section class="cm__stats">
          <AppCard padding="sm" class="cm__stat">
            <div class="cm__stat-icon cm__stat-icon--primary"><CalendarClock :size="20" /></div>
            <div>
              <p class="cm__stat-label">Total cursos</p>
              <p class="cm__stat-value">{{ stats.total }}</p>
            </div>
          </AppCard>
          <AppCard padding="sm" class="cm__stat">
            <div class="cm__stat-icon cm__stat-icon--success"><Power :size="20" /></div>
            <div>
              <p class="cm__stat-label">Activos</p>
              <p class="cm__stat-value">{{ stats.active }}</p>
            </div>
          </AppCard>
          <AppCard padding="sm" class="cm__stat">
            <div class="cm__stat-icon cm__stat-icon--danger"><AlertTriangle :size="20" /></div>
            <div>
              <p class="cm__stat-label">Inactivos</p>
              <p class="cm__stat-value">{{ stats.inactive }}</p>
            </div>
          </AppCard>
          <AppCard padding="sm" class="cm__stat">
            <div class="cm__stat-icon cm__stat-icon--warning"><Users :size="20" /></div>
            <div>
              <p class="cm__stat-label">Cupo lleno</p>
              <p class="cm__stat-value">{{ stats.full }}</p>
            </div>
          </AppCard>
        </section>

        <AppCard padding="md">
          <div class="cm__filters">
            <AppInput
              v-model="searchQuery"
              type="text"
              placeholder="Buscar por materia, docente o aula..."
              :icon="Search"
              @update:modelValue="page = 1"
            />
            <AppSelect
              v-model="turnoFilter"
              :options="turnoOptions"
              placeholder="Todos los turnos"
              @update:modelValue="page = 1"
            />
          </div>

          <AppTable
            :columns="columns"
            :rows="paginatedCursos"
            :loading="loading"
            row-key="IdCursoMateria"
            empty-title="Sin cursos programados"
            empty-description="Aún no se ha programado ningún curso para este ciclo lectivo."
          >
            <template #cell-materia="{ row }">
              <div class="cm__materia">
                <div class="cm__materia-icon"><BookOpen :size="18" /></div>
                <div>
                  <strong>{{ row.Materia?.Nombre || '—' }}</strong>
                  <span class="cm__materia-code"><Hash :size="11" /> {{ row.Materia?.CodigoMateria }}</span>
                </div>
              </div>
            </template>

            <template #cell-aula="{ row }">
              <template v-if="row.Curso">
                <div class="cm__aula">
                  <span class="cm__aula-name"><MapPin :size="13" /> {{ row.Curso.Aula }}</span>
                  <span class="cm__aula-piso">Piso {{ row.Curso.Piso }}</span>
                </div>
              </template>
              <span v-else class="cm__muted">—</span>
            </template>

            <template #cell-docente="{ row }">
              <div class="cm__docente">
                <UserIcon :size="14" />
                <span>{{ row.Docente?.Nombre || 'Sin asignar' }}</span>
              </div>
            </template>

            <template #cell-turno="{ row }">
              <template v-if="row.Turno">
                <AppBadge variant="primary" size="sm"><Clock :size="11" /> {{ row.Turno.Nombre }}</AppBadge>
                <p class="cm__turno-time">{{ formatTime(row.Turno.HoraInicio) }} - {{ formatTime(row.Turno.HoraFin) }}</p>
              </template>
              <span v-else class="cm__muted">—</span>
            </template>

            <template #cell-vigencia="{ row }">
              <div class="cm__vigencia">
                <span class="cm__vigencia-date"><Calendar :size="12" /> {{ formatDate(row.FechaInicio) }}</span>
                <span class="cm__vigencia-sub">al {{ formatDate(row.FechaFin) }}</span>
              </div>
            </template>

            <template #cell-cupos="{ row }">
              <div class="cm__cupos">
                <strong :class="Number(row.Inscritos) >= Number(row.MaxInscritos) ? 'cm__cupos-full' : 'cm__cupos-ok'">
                  {{ row.Inscritos }}
                </strong>
                <span class="cm__cupos-max">/ {{ row.MaxInscritos }}</span>
              </div>
            </template>

            <template #cell-status="{ row }">
              <button
                type="button"
                :class="['cm__status', row.Estado ? 'cm__status--active' : 'cm__status--inactive']"
                :title="row.Estado ? 'Desactivar curso' : 'Activar curso'"
                @click="toggleStatus(row)"
              >
                <span :class="['cm__status-dot', row.Estado ? 'cm__status-dot--on' : 'cm__status-dot--off']" aria-hidden="true"></span>
                {{ row.Estado ? 'Activo' : 'Inactivo' }}
              </button>
            </template>

            <template #cell-actions="{ row }">
              <div class="cm__actions">
                <AppButton variant="ghost" size="sm" :icon="Pencil" aria-label="Editar" @click="openEdit(row)" />
                <AppButton variant="ghost" size="sm" :icon="Trash2" aria-label="Eliminar" @click="confirmDelete(row)" />
              </div>
            </template>

            <template #empty>
              <AppEmptyState
                :icon="CalendarClock"
                title="Sin cursos programados"
                description="Crea la primera programación de curso para iniciar el ciclo."
              >
                <AppButton variant="primary" :icon="Plus" @click="openCreate">Programar curso</AppButton>
              </AppEmptyState>
            </template>
          </AppTable>

          <footer v-if="filteredCursos.length > 0" class="cm__pagination">
            <p class="cm__pagination-info">
              Mostrando <strong>{{ pageRange.from }}–{{ pageRange.to }}</strong> de <strong>{{ filteredCursos.length }}</strong> programaciones
            </p>
            <div class="cm__pagination-controls">
              <AppButton variant="ghost" size="sm" :icon="ChevronLeft" :disabled="page === 1" @click="goToPage(page - 1)">Anterior</AppButton>
              <span class="cm__pagination-page">Página {{ page }} / {{ totalPages }}</span>
              <AppButton variant="ghost" size="sm" :icon-right="ChevronRight" :disabled="page === totalPages" @click="goToPage(page + 1)">Siguiente</AppButton>
            </div>
          </footer>
        </AppCard>

        <AppButton variant="ghost" :icon="ArrowLeft" @click="window.location.href = '/index'">
          Volver al panel principal
        </AppButton>
      </div>
    </PageTransition>

    <AppModal :open="showFormModal" :title="isEditing ? 'Editar programación' : 'Programar nuevo curso'" size="lg" @close="closeFormModal">
      <form class="cm__form" @submit.prevent="submit">
        <AppAlert v-if="formErrors.length" variant="danger" title="Revisa la información">
          <ul class="cm__errors">
            <li v-for="(err, i) in formErrors" :key="i">{{ err }}</li>
          </ul>
        </AppAlert>
        <div class="cm__form-grid">
          <AppSelect v-model="form.IdMateria" label="Materia / Asignatura" :options="materiaOptions" required placeholder="Seleccione una materia" />
          <AppSelect v-model="form.IdCurso" label="Aula / Ubicación física" :options="cursoOptions" required placeholder="Seleccione un aula" />
          <AppSelect v-model="form.IdDocente" label="Docente" :options="docenteOptions" required placeholder="Seleccione un docente" />
          <AppSelect v-model="form.IdTurno" label="Turno / Horario" :options="turnoOptions" required placeholder="Seleccione un turno" />
          <AppInput v-model="form.FechaInicio" label="Fecha de inicio" type="date" required />
          <AppInput v-model="form.FechaFin" label="Fecha de finalización" type="date" required />
          <AppInput v-model.number="form.MaxInscritos" label="Cupo máximo" type="number" :min="1" required hint="Número máximo de estudiantes inscritos." />
        </div>
      </form>
      <template #footer>
        <AppButton variant="secondary" @click="closeFormModal">Cancelar</AppButton>
        <AppButton variant="primary" :icon="CalendarClock" :loading="submitting" @click="submit">
          {{ isEditing ? 'Guardar cambios' : 'Programar curso' }}
        </AppButton>
      </template>
    </AppModal>

    <AppModal :open="showDeleteModal" title="Confirmar eliminación" size="sm" @close="closeDeleteModal">
      <div class="cm__delete">
        <AppAlert variant="warning" title="Acción irreversible">
          Si hay alumnos inscritos, la eliminación física fallará y el curso será desactivado por seguridad.
        </AppAlert>
        <div class="cm__delete-card">
          <div class="cm__delete-icon"><BookOpen :size="20" /></div>
          <div>
            <strong>{{ cmToDelete?.Materia?.Nombre }}</strong>
            <p class="cm__delete-meta">Docente: {{ cmToDelete?.Docente?.Nombre || '—' }}</p>
            <p class="cm__delete-meta">Aula: {{ cmToDelete?.Curso?.Aula }} (Piso {{ cmToDelete?.Curso?.Piso }})</p>
          </div>
        </div>
      </div>
      <template #footer>
        <AppButton variant="secondary" @click="closeDeleteModal">Cancelar</AppButton>
        <AppButton variant="danger" :icon="Trash2" :loading="submitting" @click="submitDelete">Eliminar programación</AppButton>
      </template>
    </AppModal>
  </AppShell>
</template>

<style scoped>
.cm {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.cm__stats {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 14px;
}

.cm__stat {
  display: flex;
  align-items: center;
  gap: 12px;
}

.cm__stat-icon {
  display: grid;
  place-items: center;
  width: 44px;
  height: 44px;
  border-radius: var(--radius-md);
  background: var(--color-primary-soft);
  color: var(--color-primary);
  flex-shrink: 0;
}
.cm__stat-icon--success { background: var(--color-success-soft); color: var(--color-success); }
.cm__stat-icon--danger { background: var(--color-danger-soft); color: var(--color-danger); }
.cm__stat-icon--warning { background: var(--color-warning-soft); color: var(--color-warning); }

.cm__stat-label {
  margin: 0;
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  font-weight: 600;
  color: var(--color-text-muted);
}

.cm__stat-value {
  margin: 0;
  font-size: 1.4rem;
  font-weight: 800;
  color: var(--color-text-primary);
  line-height: 1;
}

.cm__filters {
  display: grid;
  grid-template-columns: 1fr 240px;
  gap: 12px;
  margin-bottom: 18px;
}

.cm__materia {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 0;
}

.cm__materia-icon {
  display: grid;
  place-items: center;
  width: 36px;
  height: 36px;
  border-radius: var(--radius-sm);
  background: var(--color-primary-soft);
  color: var(--color-primary);
  flex-shrink: 0;
}

.cm__materia strong {
  display: block;
  font-size: 0.92rem;
  color: var(--color-text-primary);
}

.cm__materia-code {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 0.76rem;
  color: var(--color-text-muted);
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
}

.cm__aula {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.cm__aula-name {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-weight: 700;
  color: var(--color-text-primary);
  font-size: 0.9rem;
}

.cm__aula-piso {
  font-size: 0.76rem;
  color: var(--color-text-muted);
}

.cm__docente {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.88rem;
  color: var(--color-text-primary);
}

.cm__turno-time {
  margin: 4px 0 0;
  font-size: 0.76rem;
  color: var(--color-text-muted);
}

.cm__vigencia {
  display: flex;
  flex-direction: column;
  gap: 2px;
  font-size: 0.85rem;
}

.cm__vigencia-date {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  color: var(--color-text-primary);
  font-weight: 600;
}

.cm__vigencia-sub {
  font-size: 0.76rem;
  color: var(--color-text-muted);
}

.cm__cupos {
  display: inline-flex;
  align-items: baseline;
  gap: 4px;
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
}

.cm__cupos-ok { color: var(--color-success); font-size: 1.05rem; font-weight: 800; }
.cm__cupos-full { color: var(--color-danger); font-size: 1.05rem; font-weight: 800; }
.cm__cupos-max { color: var(--color-text-muted); font-size: 0.82rem; }

.cm__muted {
  color: var(--color-text-muted);
}

.cm__status {
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

.cm__status--active {
  color: var(--color-success);
  border-color: var(--color-success-border);
  background: var(--color-success-soft);
}
.cm__status--active:hover { background: var(--color-success); color: white; }

.cm__status--inactive {
  color: var(--color-danger);
  border-color: var(--color-danger-border);
  background: var(--color-danger-soft);
}
.cm__status--inactive:hover { background: var(--color-danger); color: white; }

.cm__status-dot { width: 8px; height: 8px; border-radius: 50%; }
.cm__status-dot--on { background: var(--color-success); box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.18); }
.cm__status-dot--off { background: var(--color-danger); box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.18); }

.cm__actions {
  display: inline-flex;
  gap: 4px;
  justify-content: flex-end;
}

.cm__pagination {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  padding-top: 16px;
  margin-top: 16px;
  border-top: 1px solid var(--color-border-subtle);
}

.cm__pagination-info {
  margin: 0;
  font-size: 0.82rem;
  color: var(--color-text-muted);
}

.cm__pagination-controls {
  display: flex;
  align-items: center;
  gap: 10px;
}

.cm__pagination-page {
  font-size: 0.82rem;
  font-weight: 600;
  color: var(--color-text-secondary);
  min-width: 96px;
  text-align: center;
}

.cm__form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.cm__form-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 14px;
}

.cm__errors {
  margin: 0;
  padding-left: 20px;
  font-size: 0.85rem;
  line-height: 1.5;
}

.cm__delete {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.cm__delete-card {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px 16px;
  background: var(--color-surface-1);
  border: 1px solid var(--color-border-subtle);
  border-radius: var(--radius-md);
}

.cm__delete-icon {
  display: grid;
  place-items: center;
  width: 44px;
  height: 44px;
  border-radius: var(--radius-md);
  background: var(--color-primary-soft);
  color: var(--color-primary);
  flex-shrink: 0;
}

.cm__delete-card strong {
  display: block;
  font-size: 1rem;
  color: var(--color-text-primary);
  margin-bottom: 2px;
}

.cm__delete-meta {
  margin: 0;
  font-size: 0.85rem;
  color: var(--color-text-muted);
}

@media (max-width: 1024px) {
  .cm__stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .cm__filters { grid-template-columns: 1fr; }
}

@media (max-width: 640px) {
  .cm__form-grid { grid-template-columns: 1fr; }
  .cm__pagination { flex-direction: column; align-items: stretch; }
  .cm__pagination-controls { justify-content: space-between; }
}
</style>
