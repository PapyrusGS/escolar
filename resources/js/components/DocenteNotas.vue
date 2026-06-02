<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import axios from 'axios';
import {
  ClipboardList,
  ArrowLeft,
  BookOpen,
  Hash,
  CheckCircle2,
  XCircle,
  Pencil,
  Plus,
  Save,
  X,
  TrendingUp,
  TrendingDown,
  Target,
  Users,
  Award,
  Sparkles,
  MapPin,
  Info,
} from '@lucide/vue';
import AppShell from './layout/AppShell.vue';
import PageTransition from './layout/PageTransition.vue';
import AppCard from './ui/AppCard.vue';
import AppButton from './ui/AppButton.vue';
import AppSelect from './ui/AppSelect.vue';
import AppAlert from './ui/AppAlert.vue';
import AppPageHeader from './ui/AppPageHeader.vue';
import AppTable from './ui/AppTable.vue';
import AppAvatar from './ui/AppAvatar.vue';
import AppBadge from './ui/AppBadge.vue';
import AppEmptyState from './ui/AppEmptyState.vue';
import AppSpinner from './ui/AppSpinner.vue';
import AppInput from './ui/AppInput.vue';
import { toast } from '../lib/toast.js';
import { useGsap } from '../composables/useGsap.js';

const { staggerIn } = useGsap();

const user = ref(null);
const cursos = ref([]);
const selectedCursoId = ref('');
const loadingCursos = ref(false);
const loadingNotas = ref(false);
const alumnos = ref([]);
const rendimiento = ref(null);

const editingId = ref(null);
const editingValue = ref(null);
const savingNota = ref(false);
const notaInput = ref(null);

const selectedCurso = computed(() => cursos.value.find((c) => Number(c.IdCursoMateria) === Number(selectedCursoId.value)));

const cursoOptions = computed(() =>
  cursos.value.map((c) => ({
    Id: c.IdCursoMateria,
    Nombre: `[${c.CodigoMateria}] ${c.MateriaNombre} — Aula ${c.Aula} (${c.Inscritos} inscritos)`,
  }))
);

const columns = [
  { key: 'index', label: '#', width: '5%', align: 'center' },
  { key: 'student', label: 'Estudiante', width: '34%' },
  { key: 'ci', label: 'CI', width: '14%' },
  { key: 'nota', label: 'Nota actual', align: 'center', width: '14%' },
  { key: 'actions', label: 'Acciones', align: 'center', width: '18%' },
  { key: 'status', label: 'Estado', align: 'center', width: '15%' },
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

  await loadCursos();
  await nextTick();
  staggerIn('.dn-rend-card', { delay: 0.05 });

  const params = new URLSearchParams(window.location.search);
  const cursoParam = params.get('curso');
  if (cursoParam && cursos.value.some((c) => String(c.IdCursoMateria) === String(cursoParam))) {
    selectedCursoId.value = Number(cursoParam);
    await onCursoChange();
  }
});

const loadCursos = async () => {
  loadingCursos.value = true;
  try {
    const { data } = await axios.get('/api/docente/notas/cursos');
    cursos.value = data?.data ?? data ?? [];
  } catch (err) {
    toast.error('No se pudieron cargar los cursos');
  } finally {
    loadingCursos.value = false;
  }
};

const onCursoChange = async () => {
  if (!selectedCursoId.value) return;
  loadingNotas.value = true;
  alumnos.value = [];
  rendimiento.value = null;
  cancelEdit();
  try {
    await Promise.all([loadNotas(), loadRendimiento()]);
    await nextTick();
    staggerIn('.dn-row', { delay: 0.04 });
  } finally {
    loadingNotas.value = false;
  }
};

const loadNotas = async () => {
  try {
    const { data } = await axios.get(`/api/docente/cursos/${selectedCursoId.value}/notas`);
    alumnos.value = data?.data ?? data ?? [];
  } catch (err) {
    toast.error('No se pudieron cargar las notas');
  }
};

const loadRendimiento = async () => {
  try {
    const { data } = await axios.get(`/api/docente/cursos/${selectedCursoId.value}/rendimiento`);
    rendimiento.value = data?.data ?? data ?? null;
  } catch (err) {
    console.error('Error al cargar rendimiento:', err);
  }
};

const startEdit = async (alumno) => {
  editingId.value = alumno.IdInscripcion;
  editingValue.value = alumno.Nota ?? '';
  await nextTick();
  if (notaInput.value) {
    const el = Array.isArray(notaInput.value) ? notaInput.value[0] : notaInput.value;
    if (el && el.focus) el.focus();
  }
};

const cancelEdit = () => {
  editingId.value = null;
  editingValue.value = null;
};

const saveNota = async (alumno) => {
  if (editingValue.value === null || editingValue.value === '' || isNaN(Number(editingValue.value))) {
    toast.error('Ingresa un valor numérico válido');
    return;
  }
  const nota = parseFloat(editingValue.value);
  if (nota < 0 || nota > 100) {
    toast.error('La nota debe estar entre 0 y 100');
    return;
  }
  savingNota.value = true;
  try {
    if (alumno.EstadoNota) {
      await axios.put(`/api/docente/notas/${alumno.IdNota}`, { Nota: nota });
      toast.success('Nota actualizada');
    } else {
      await axios.post('/api/docente/notas', { IdInscripcion: alumno.IdInscripcion, Nota: nota });
      toast.success('Nota asignada');
    }
    cancelEdit();
    await Promise.all([loadNotas(), loadRendimiento()]);
  } catch (err) {
    toast.error(err?.response?.data?.message || 'No se pudo guardar la nota');
  } finally {
    savingNota.value = false;
  }
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
  <AppShell v-if="user" :user="user" page-title="Gestión de notas" @logout="handleLogout">
    <PageTransition>
      <div class="dn">
        <AppPageHeader
          eyebrow="Panel del docente"
          title="Gestión de notas"
          description="Asigna, edita y visualiza el rendimiento de tus alumnos por curso."
        >
          <template #actions>
            <AppButton variant="secondary" :icon="ArrowLeft" @click="window.location.href = '/docente/cursos'">Mis cursos</AppButton>
            <AppButton variant="secondary" :icon="ArrowLeft" @click="window.location.href = '/dashboard'">Volver al Dashboard</AppButton>
          </template>
        </AppPageHeader>

        <AppCard padding="lg" class="dn__selector">
          <AppSelect
            v-model="selectedCursoId"
            :options="cursoOptions"
            label="Curso a gestionar"
            placeholder="Selecciona un curso"
            :icon="BookOpen"
            @update:modelValue="onCursoChange"
          />
          <div v-if="selectedCurso" class="dn__selector-meta">
            <span><MapPin :size="13" /> Aula {{ selectedCurso.Aula }}</span>
            <span><Users :size="13" /> {{ selectedCurso.Inscritos }} inscritos</span>
            <span><Hash :size="13" /> {{ selectedCurso.CodigoMateria }}</span>
          </div>
        </AppCard>

        <div v-if="loadingCursos" class="dn__loading">
          <AppSpinner size="lg" />
          <p>Cargando tus cursos…</p>
        </div>

        <AppEmptyState
          v-else-if="cursos.length === 0"
          :icon="Sparkles"
          title="No tienes cursos asignados"
          description="Contacta al administrador para que te asigne materias."
        />

        <template v-else-if="selectedCursoId">
          <div v-if="loadingNotas" class="dn__loading">
            <AppSpinner size="lg" />
            <p>Cargando notas del curso…</p>
          </div>

          <template v-else>
            <section v-if="rendimiento" class="dn__rend">
              <header class="dn__rend-head">
                <h3>
                  <Award :size="18" />
                  Rendimiento del curso
                </h3>
                <p class="dn__rend-hint">Regla: nota ≥ 51 = Aprobado.</p>
              </header>
              <div class="dn__rend-grid">
                <AppCard padding="md" class="dn-rend-card dn__rend-card">
                  <div class="dn__rend-icon dn__rend-icon--primary"><Target :size="18" /></div>
                  <span class="dn__rend-label">Promedio</span>
                  <strong class="dn__rend-value">{{ rendimiento.promedio }}</strong>
                </AppCard>
                <AppCard padding="md" class="dn-rend-card dn__rend-card">
                  <div class="dn__rend-icon dn__rend-icon--success"><CheckCircle2 :size="18" /></div>
                  <span class="dn__rend-label">Aprobados</span>
                  <strong class="dn__rend-value dn__rend-value--ok">{{ rendimiento.aprobados }}</strong>
                </AppCard>
                <AppCard padding="md" class="dn-rend-card dn__rend-card">
                  <div class="dn__rend-icon dn__rend-icon--danger"><XCircle :size="18" /></div>
                  <span class="dn__rend-label">Reprobados</span>
                  <strong class="dn__rend-value dn__rend-value--bad">{{ rendimiento.reprobados }}</strong>
                </AppCard>
                <AppCard padding="md" class="dn-rend-card dn__rend-card">
                  <div class="dn__rend-icon dn__rend-icon--info"><TrendingUp :size="18" /></div>
                  <span class="dn__rend-label">% Aprobación</span>
                  <strong class="dn__rend-value">{{ rendimiento.porcentaje_aprobacion }}%</strong>
                </AppCard>
                <AppCard padding="md" class="dn-rend-card dn__rend-card">
                  <div class="dn__rend-icon dn__rend-icon--success"><TrendingUp :size="18" /></div>
                  <span class="dn__rend-label">Nota máx</span>
                  <strong class="dn__rend-value">{{ rendimiento.nota_maxima ?? '—' }}</strong>
                </AppCard>
                <AppCard padding="md" class="dn-rend-card dn__rend-card">
                  <div class="dn__rend-icon dn__rend-icon--danger"><TrendingDown :size="18" /></div>
                  <span class="dn__rend-label">Nota mín</span>
                  <strong class="dn__rend-value">{{ rendimiento.nota_minima ?? '—' }}</strong>
                </AppCard>
              </div>
            </section>

            <AppEmptyState
              v-if="alumnos.length === 0"
              :icon="Users"
              title="Sin alumnos inscritos"
              description="Este curso aún no tiene estudiantes registrados."
              class="dn__empty"
            />

            <AppCard v-else padding="none">
              <header class="dn__table-head">
                <h3>
                  <ClipboardList :size="18" />
                  Alumnos ({{ alumnos.length }})
                </h3>
                <p class="dn__table-hint">
                  <Info :size="13" />
                  Las notas se guardan individualmente al confirmar con ✓ o la tecla Enter.
                </p>
              </header>
              <AppTable :columns="columns" :rows="alumnos" row-key="IdInscripcion">
                <template #cell-index="{ index }">{{ index + 1 }}</template>
                <template #cell-student="{ row }">
                  <div class="dn__student">
                    <AppAvatar :name="row.Estudiante" variant="student" size="sm" />
                    <strong>{{ row.Estudiante }}</strong>
                  </div>
                </template>
                <template #cell-nota="{ row }">
                  <AppBadge v-if="row.EstadoNota" :variant="row.Aprobado ? 'success' : 'danger'" size="md">
                    {{ row.Nota }}
                  </AppBadge>
                  <AppBadge v-else variant="neutral" size="sm">Sin nota</AppBadge>
                </template>
                <template #cell-actions="{ row }">
                  <div v-if="editingId === row.IdInscripcion" class="dn__edit">
                    <AppInput
                      ref="notaInput"
                      v-model="editingValue"
                      type="number"
                      :min="0"
                      :max="100"
                      :step="0.01"
                      size="sm"
                      class="dn__edit-input"
                      @keyup.enter="saveNota(row)"
                      @keyup.escape="cancelEdit"
                    />
                    <AppButton variant="success" size="sm" :icon="Save" :loading="savingNota" :disabled="savingNota" @click="saveNota(row)" />
                    <AppButton variant="ghost" size="sm" :icon="X" @click="cancelEdit" />
                  </div>
                  <AppButton
                    v-else
                    :variant="row.EstadoNota ? 'outline' : 'primary'"
                    size="sm"
                    :icon="row.EstadoNota ? Pencil : Plus"
                    @click="startEdit(row)"
                  >
                    {{ row.EstadoNota ? 'Editar' : 'Asignar' }}
                  </AppButton>
                </template>
                <template #cell-status="{ row }">
                  <AppBadge
                    :variant="!row.EstadoNota ? 'neutral' : row.Aprobado ? 'success' : 'danger'"
                    size="sm"
                  >
                    {{ row.EstadoNota ? (row.Aprobado ? 'Aprobado' : 'Reprobado') : 'Sin nota' }}
                  </AppBadge>
                </template>
              </AppTable>
            </AppCard>
          </template>
        </template>

        <AppEmptyState
          v-else
          :icon="BookOpen"
          title="Selecciona un curso"
          description="Elige un curso en el selector superior para gestionar las calificaciones de tus alumnos."
        />
      </div>
    </PageTransition>
  </AppShell>
</template>

<style scoped>
.dn {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.dn__selector {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.dn__selector-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
  font-size: 0.85rem;
  color: var(--color-text-muted);
  padding-top: 10px;
  border-top: 1px solid var(--color-border-subtle);
}

.dn__selector-meta span {
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.dn__loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
  padding: 60px 24px;
  text-align: center;
}

.dn__loading p {
  margin: 0;
  color: var(--color-text-muted);
}

.dn__rend {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.dn__rend-head {
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
}

.dn__rend-head h3 {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin: 0;
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--color-text-primary);
}

.dn__rend-hint {
  margin: 0;
  font-size: 0.82rem;
  color: var(--color-text-muted);
}

.dn__rend-grid {
  display: grid;
  grid-template-columns: repeat(6, minmax(0, 1fr));
  gap: 12px;
}

.dn__rend-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 6px;
}

.dn__rend-icon {
  display: grid;
  place-items: center;
  width: 40px;
  height: 40px;
  border-radius: var(--radius-md);
  background: var(--color-primary-soft);
  color: var(--color-primary);
}
.dn__rend-icon--success { background: var(--color-success-soft); color: var(--color-success); }
.dn__rend-icon--danger { background: var(--color-danger-soft); color: var(--color-danger); }
.dn__rend-icon--info { background: var(--color-info-soft); color: var(--color-info); }

.dn__rend-label {
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  font-weight: 600;
  color: var(--color-text-muted);
}

.dn__rend-value {
  font-size: 1.5rem;
  font-weight: 800;
  color: var(--color-text-primary);
  line-height: 1;
}

.dn__rend-value--ok { color: var(--color-success); }
.dn__rend-value--bad { color: var(--color-danger); }

.dn__empty {
  margin-top: 8px;
}

.dn__table-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  padding: 18px 22px;
  border-bottom: 1px solid var(--color-border-subtle);
  background: rgba(28, 39, 66, 0.3);
}

.dn__table-head h3 {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin: 0;
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--color-text-primary);
}

.dn__table-hint {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  margin: 0;
  font-size: 0.78rem;
  color: var(--color-text-muted);
}

.dn__student {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 0;
}

.dn__student strong {
  font-size: 0.92rem;
  color: var(--color-text-primary);
}

.dn__edit {
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.dn__edit-input {
  width: 80px;
}

.dn__edit-input :deep(.app-input__field) {
  text-align: center;
  padding: 6px 10px;
  font-weight: 700;
}

@media (max-width: 1024px) {
  .dn__rend-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}

@media (max-width: 640px) {
  .dn__rend-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .dn__table-head { flex-direction: column; align-items: flex-start; }
}
</style>
