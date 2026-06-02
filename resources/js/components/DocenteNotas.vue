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
import AppStatCard from './ui/AppStatCard.vue';
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

const distribucionPct = (n) => {
  if (!rendimiento.value || !rendimiento.value.alumnos_con_nota) return 0;
  return Math.round((n / rendimiento.value.alumnos_con_nota) * 1000) / 10;
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
            <AppButton variant="secondary" :icon="ArrowLeft" @click="goTo('/docente/cursos')">Mis cursos</AppButton>
            <AppButton variant="secondary" :icon="ArrowLeft" @click="goTo('/dashboard')">Volver al Dashboard</AppButton>
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

              <AppCard padding="none" class="dn__hero">
                <div class="dn__hero-body">
                  <div class="dn__hero-main">
                    <span class="dn__hero-label">Promedio del curso</span>
                    <strong class="dn__hero-value">{{ rendimiento.promedio }}</strong>
                    <span class="dn__hero-scale">/ 100</span>
                  </div>
                  <div class="dn__hero-side">
                    <div class="dn__hero-stat">
                      <Users :size="16" />
                      <div>
                        <span class="dn__hero-stat-label">Alumnos totales</span>
                        <strong class="dn__hero-stat-value">{{ rendimiento.total_alumnos }}</strong>
                      </div>
                    </div>
                    <div class="dn__hero-stat">
                      <ClipboardList :size="16" />
                      <div>
                        <span class="dn__hero-stat-label">Con nota asignada</span>
                        <strong class="dn__hero-stat-value">{{ rendimiento.alumnos_con_nota }}</strong>
                      </div>
                    </div>
                    <div class="dn__hero-stat">
                      <Target :size="16" />
                      <div>
                        <span class="dn__hero-stat-label">% Aprobación</span>
                        <strong class="dn__hero-stat-value">{{ rendimiento.porcentaje_aprobacion }}%</strong>
                      </div>
                    </div>
                  </div>
                </div>
              </AppCard>

              <div class="dn__kpis">
                <AppStatCard
                  class="dn-rend-card"
                  label="Aprobados"
                  :value="rendimiento.aprobados"
                  :icon="CheckCircle2"
                  variant="success"
                />
                <AppStatCard
                  class="dn-rend-card"
                  label="Reprobados"
                  :value="rendimiento.reprobados"
                  :icon="XCircle"
                  variant="danger"
                />
                <AppStatCard
                  class="dn-rend-card"
                  label="Nota máxima"
                  :value="rendimiento.nota_maxima ?? '—'"
                  :icon="TrendingUp"
                  variant="info"
                />
                <AppStatCard
                  class="dn-rend-card"
                  label="Nota mínima"
                  :value="rendimiento.nota_minima ?? '—'"
                  :icon="TrendingDown"
                  variant="warning"
                />
              </div>

              <AppCard padding="md" class="dn__dist">
                <header class="dn__dist-head">
                  <h4>
                    <Target :size="16" />
                    Distribución por rango de notas
                  </h4>
                  <span class="dn__dist-hint">Basado en {{ rendimiento.alumnos_con_nota }} alumnos con nota</span>
                </header>
                <div v-if="rendimiento.alumnos_con_nota > 0" class="dn__dist-bar" :aria-label="`Distribución: ${rendimiento.distribucion['0_50']} reprobados, ${rendimiento.distribucion['51_70'] + rendimiento.distribucion['71_90'] + rendimiento.distribucion['91_100']} aprobados`">
                  <div
                    v-if="distribucionPct(rendimiento.distribucion['0_50']) > 0"
                    class="dn__dist-seg dn__dist-seg--bad"
                    :style="{ width: distribucionPct(rendimiento.distribucion['0_50']) + '%' }"
                    :title="`0–50: ${rendimiento.distribucion['0_50']} alumnos`"
                  >
                    <span v-if="distribucionPct(rendimiento.distribucion['0_50']) >= 10">{{ rendimiento.distribucion['0_50'] }}</span>
                  </div>
                  <div
                    v-if="distribucionPct(rendimiento.distribucion['51_70']) > 0"
                    class="dn__dist-seg dn__dist-seg--warn"
                    :style="{ width: distribucionPct(rendimiento.distribucion['51_70']) + '%' }"
                    :title="`51–70: ${rendimiento.distribucion['51_70']} alumnos`"
                  >
                    <span v-if="distribucionPct(rendimiento.distribucion['51_70']) >= 10">{{ rendimiento.distribucion['51_70'] }}</span>
                  </div>
                  <div
                    v-if="distribucionPct(rendimiento.distribucion['71_90']) > 0"
                    class="dn__dist-seg dn__dist-seg--info"
                    :style="{ width: distribucionPct(rendimiento.distribucion['71_90']) + '%' }"
                    :title="`71–90: ${rendimiento.distribucion['71_90']} alumnos`"
                  >
                    <span v-if="distribucionPct(rendimiento.distribucion['71_90']) >= 10">{{ rendimiento.distribucion['71_90'] }}</span>
                  </div>
                  <div
                    v-if="distribucionPct(rendimiento.distribucion['91_100']) > 0"
                    class="dn__dist-seg dn__dist-seg--ok"
                    :style="{ width: distribucionPct(rendimiento.distribucion['91_100']) + '%' }"
                    :title="`91–100: ${rendimiento.distribucion['91_100']} alumnos`"
                  >
                    <span v-if="distribucionPct(rendimiento.distribucion['91_100']) >= 10">{{ rendimiento.distribucion['91_100'] }}</span>
                  </div>
                </div>
                <div v-else class="dn__dist-empty">
                  Aún no hay notas asignadas en este curso.
                </div>
                <ul v-if="rendimiento.alumnos_con_nota > 0" class="dn__dist-legend">
                  <li class="dn__dist-legend-item">
                    <span class="dn__dist-swatch dn__dist-swatch--bad"></span>
                    <span class="dn__dist-legend-label">0–50 (Reprobado)</span>
                    <span class="dn__dist-legend-value">{{ rendimiento.distribucion['0_50'] }}</span>
                  </li>
                  <li class="dn__dist-legend-item">
                    <span class="dn__dist-swatch dn__dist-swatch--warn"></span>
                    <span class="dn__dist-legend-label">51–70 (Básico)</span>
                    <span class="dn__dist-legend-value">{{ rendimiento.distribucion['51_70'] }}</span>
                  </li>
                  <li class="dn__dist-legend-item">
                    <span class="dn__dist-swatch dn__dist-swatch--info"></span>
                    <span class="dn__dist-legend-label">71–90 (Bueno)</span>
                    <span class="dn__dist-legend-value">{{ rendimiento.distribucion['71_90'] }}</span>
                  </li>
                  <li class="dn__dist-legend-item">
                    <span class="dn__dist-swatch dn__dist-swatch--ok"></span>
                    <span class="dn__dist-legend-label">91–100 (Excelente)</span>
                    <span class="dn__dist-legend-value">{{ rendimiento.distribucion['91_100'] }}</span>
                  </li>
                </ul>
              </AppCard>
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
  gap: 16px;
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

.dn__hero {
  background:
    radial-gradient(circle at top right, rgba(99, 102, 241, 0.22) 0%, transparent 65%),
    linear-gradient(135deg, rgba(28, 39, 66, 0.7) 0%, rgba(19, 28, 48, 0.8) 100%);
  border: 1px solid var(--color-primary-border);
}

.dn__hero-body {
  padding: 26px 28px;
  display: grid;
  grid-template-columns: minmax(220px, 1.2fr) minmax(260px, 2fr);
  gap: 28px;
  align-items: center;
}

.dn__hero-main {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 4px;
}

.dn__hero-label {
  font-size: 0.74rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  font-weight: 700;
  color: var(--color-text-muted);
}

.dn__hero-value {
  font-size: 3.25rem;
  font-weight: 800;
  line-height: 1;
  color: var(--color-text-primary);
  font-variant-numeric: tabular-nums;
  background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.dn__hero-scale {
  font-size: 1rem;
  font-weight: 600;
  color: var(--color-text-muted);
}

.dn__hero-side {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px;
}

.dn__hero-stat {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 14px;
  background: rgba(6, 9, 18, 0.35);
  border: 1px solid var(--color-border-subtle);
  border-radius: var(--radius-md);
  color: var(--color-primary);
}

.dn__hero-stat-label {
  display: block;
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--color-text-muted);
  font-weight: 600;
}

.dn__hero-stat-value {
  display: block;
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--color-text-primary);
  line-height: 1.1;
  font-variant-numeric: tabular-nums;
}

.dn__kpis {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 14px;
}

.dn__dist-head {
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 14px;
  flex-wrap: wrap;
}

.dn__dist-head h4 {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin: 0;
  font-size: 0.95rem;
  font-weight: 700;
  color: var(--color-text-primary);
}

.dn__dist-hint {
  font-size: 0.78rem;
  color: var(--color-text-muted);
}

.dn__dist-bar {
  display: flex;
  width: 100%;
  height: 38px;
  border-radius: var(--radius-md);
  overflow: hidden;
  background: var(--color-surface-3);
  border: 1px solid var(--color-border-subtle);
}

.dn__dist-seg {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.78rem;
  font-weight: 800;
  color: white;
  min-width: 0;
  transition: width var(--duration-base) var(--ease-out);
}

.dn__dist-seg--bad  { background: linear-gradient(180deg, #ef4444 0%, #b91c1c 100%); }
.dn__dist-seg--warn { background: linear-gradient(180deg, #f59e0b 0%, #b45309 100%); }
.dn__dist-seg--info { background: linear-gradient(180deg, #0ea5e9 0%, #0369a1 100%); }
.dn__dist-seg--ok   { background: linear-gradient(180deg, #10b981 0%, #047857 100%); }

.dn__dist-empty {
  padding: 22px 16px;
  text-align: center;
  font-size: 0.88rem;
  color: var(--color-text-muted);
  background: var(--color-surface-3);
  border-radius: var(--radius-md);
  border: 1px dashed var(--color-border-subtle);
}

.dn__dist-legend {
  list-style: none;
  margin: 14px 0 0;
  padding: 0;
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 10px;
}

.dn__dist-legend-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.82rem;
  color: var(--color-text-secondary);
  padding: 6px 10px;
  background: rgba(28, 39, 66, 0.35);
  border-radius: var(--radius-sm);
  border: 1px solid var(--color-border-subtle);
}

.dn__dist-legend-label {
  flex: 1;
  min-width: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.dn__dist-legend-value {
  font-weight: 800;
  color: var(--color-text-primary);
  font-variant-numeric: tabular-nums;
}

.dn__dist-swatch {
  width: 12px;
  height: 12px;
  border-radius: 3px;
  flex-shrink: 0;
}
.dn__dist-swatch--bad  { background: #ef4444; }
.dn__dist-swatch--warn { background: #f59e0b; }
.dn__dist-swatch--info { background: #0ea5e9; }
.dn__dist-swatch--ok   { background: #10b981; }

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
  .dn__hero-body { grid-template-columns: 1fr; gap: 18px; }
  .dn__hero-side { grid-template-columns: repeat(3, minmax(0, 1fr)); }
  .dn__kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .dn__dist-legend { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}

@media (max-width: 640px) {
  .dn__hero-side { grid-template-columns: 1fr; }
  .dn__hero-value { font-size: 2.5rem; }
  .dn__kpis { grid-template-columns: 1fr; }
  .dn__dist-legend { grid-template-columns: 1fr; }
  .dn__table-head { flex-direction: column; align-items: flex-start; }
}
</style>
