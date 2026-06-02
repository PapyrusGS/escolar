<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import axios from 'axios';
import {
  BookOpen,
  ClipboardList,
  Users,
  MapPin,
  Clock,
  Calendar,
  Hash,
  ArrowLeft,
  GraduationCap,
  Mail,
  IdCard,
  CheckCircle2,
  XCircle,
  Sparkles,
} from '@lucide/vue';
import AppShell from './layout/AppShell.vue';
import PageTransition from './layout/PageTransition.vue';
import AppCard from './ui/AppCard.vue';
import AppButton from './ui/AppButton.vue';
import AppPageHeader from './ui/AppPageHeader.vue';
import AppModal from './ui/AppModal.vue';
import AppTable from './ui/AppTable.vue';
import AppBadge from './ui/AppBadge.vue';
import AppAvatar from './ui/AppAvatar.vue';
import AppEmptyState from './ui/AppEmptyState.vue';
import AppSpinner from './ui/AppSpinner.vue';
import { toast } from '../lib/toast.js';
import { useGsap } from '../composables/useGsap.js';

const { staggerIn } = useGsap();

const user = ref(null);
const cursos = ref([]);
const loading = ref(false);

const showAlumnosModal = ref(false);
const selectedCurso = ref(null);
const alumnos = ref([]);
const loadingAlumnos = ref(false);

const stats = computed(() => {
  const totalAlumnos = cursos.value.reduce((sum, c) => sum + Number(c.Inscritos || 0), 0);
  const cupos = cursos.value.reduce((sum, c) => sum + Number(c.MaxInscritos || 0), 0);
  return {
    cursos: cursos.value.length,
    alumnos: totalAlumnos,
    cupos,
    full: cursos.value.filter((c) => Number(c.Inscritos) >= Number(c.MaxInscritos)).length,
  };
});

const formatDate = (d) => {
  if (!d) return '—';
  return new Date(d).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' });
};
const formatTime = (t) => (t ? t.substring(0, 5) : '');

const studentColumns = [
  { key: 'student', label: 'Estudiante', width: '24%' },
  { key: 'ci', label: 'CI', width: '14%' },
  { key: 'mail', label: 'Correo', width: '24%' },
  { key: 'date', label: 'Inscripto el', width: '16%' },
  { key: 'nota', label: 'Nota', align: 'center', width: '10%' },
  { key: 'status', label: 'Estado', align: 'center', width: '12%' },
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
  staggerIn('.dc-course', { delay: 0.05 });
});

const loadCursos = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/docente/cursos');
    cursos.value = data?.data ?? data ?? [];
  } catch (err) {
    toast.error('No se pudieron cargar los cursos asignados');
  } finally {
    loading.value = false;
  }
};

const openAlumnos = async (curso) => {
  selectedCurso.value = curso;
  alumnos.value = [];
  showAlumnosModal.value = true;
  loadingAlumnos.value = true;
  try {
    const { data } = await axios.get(`/api/docente/cursos/${curso.IdCursoMateria}/alumnos`);
    alumnos.value = data?.data ?? data ?? [];
  } catch (err) {
    toast.error('No se pudieron cargar los alumnos inscritos');
  } finally {
    loadingAlumnos.value = false;
  }
};

const closeAlumnosModal = () => {
  showAlumnosModal.value = false;
  selectedCurso.value = null;
  alumnos.value = [];
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
  <AppShell v-if="user" :user="user" page-title="Mis cursos" @logout="handleLogout">
    <PageTransition>
      <div class="dc">
        <AppPageHeader
          eyebrow="Panel del docente"
          title="Materias y cursos asignados"
          description="Consulta las materias que impartes, sus horarios y alumnos inscritos."
        >
          <template #actions>
            <AppButton variant="secondary" :icon="ArrowLeft" @click="window.location.href = '/dashboard'">Volver al Dashboard</AppButton>
            <AppButton variant="primary" :icon="ClipboardList" @click="window.location.href = '/docente/notas'">Gestionar notas</AppButton>
          </template>
        </AppPageHeader>

        <section v-if="!loading && cursos.length" class="dc__stats">
          <AppCard padding="sm" class="dc__stat">
            <div class="dc__stat-icon dc__stat-icon--primary"><BookOpen :size="20" /></div>
            <div>
              <p class="dc__stat-label">Cursos asignados</p>
              <p class="dc__stat-value">{{ stats.cursos }}</p>
            </div>
          </AppCard>
          <AppCard padding="sm" class="dc__stat">
            <div class="dc__stat-icon dc__stat-icon--info"><Users :size="20" /></div>
            <div>
              <p class="dc__stat-label">Alumnos totales</p>
              <p class="dc__stat-value">{{ stats.alumnos }}</p>
            </div>
          </AppCard>
          <AppCard padding="sm" class="dc__stat">
            <div class="dc__stat-icon dc__stat-icon--success"><GraduationCap :size="20" /></div>
            <div>
              <p class="dc__stat-label">Cupo total</p>
              <p class="dc__stat-value">{{ stats.cupos }}</p>
            </div>
          </AppCard>
          <AppCard padding="sm" class="dc__stat">
            <div class="dc__stat-icon dc__stat-icon--warning"><XCircle :size="20" /></div>
            <div>
              <p class="dc__stat-label">Cursos llenos</p>
              <p class="dc__stat-value">{{ stats.full }}</p>
            </div>
          </AppCard>
        </section>

        <div v-if="loading" class="dc__loading">
          <AppSpinner size="lg" />
          <p>Cargando tus cursos asignados…</p>
        </div>

        <AppEmptyState
          v-else-if="cursos.length === 0"
          :icon="Sparkles"
          title="No tienes cursos asignados"
          description="Contacta al administrador para que te asigne materias en el ciclo lectivo actual."
        />

        <div v-else class="dc__grid">
          <AppCard
            v-for="c in cursos"
            :key="c.IdCursoMateria"
            class="dc-course dc__course"
            padding="none"
            interactive
          >
            <header class="dc__course-head">
              <div class="dc__course-title">
                <span class="dc__course-code"><Hash :size="11" /> {{ c.Materia.CodigoMateria }}</span>
                <h3>{{ c.Materia.Nombre }}</h3>
              </div>
              <AppBadge :variant="c.Inscritos >= c.MaxInscritos ? 'danger' : 'success'" size="sm">
                {{ c.Inscritos }}/{{ c.MaxInscritos }}
              </AppBadge>
            </header>

            <div class="dc__course-body">
              <div class="dc__info"><MapPin :size="14" /><span><strong>Aula:</strong> {{ c.Curso.Nombre }} (Piso {{ c.Curso.Piso }})</span></div>
              <div class="dc__info">
                <Clock :size="14" />
                <span>
                  <strong>Turno:</strong>
                  <AppBadge variant="primary" size="sm">{{ c.Turno.Nombre }}</AppBadge>
                  {{ formatTime(c.Turno.HoraInicio) }} - {{ formatTime(c.Turno.HoraFin) }}
                </span>
              </div>
              <div class="dc__info"><Calendar :size="14" /><span><strong>Vigencia:</strong> {{ formatDate(c.FechaInicio) }} al {{ formatDate(c.FechaFin) }}</span></div>
            </div>

            <footer class="dc__course-foot">
              <AppButton variant="primary" :icon="Users" block @click="openAlumnos(c)">
                Ver alumnos ({{ c.Inscritos }})
              </AppButton>
              <AppButton variant="secondary" :icon="ClipboardList" block @click="window.location.href = `/docente/notas?curso=${c.IdCursoMateria}`">
                Gestionar notas
              </AppButton>
            </footer>
          </AppCard>
        </div>
      </div>
    </PageTransition>

    <AppModal :open="showAlumnosModal" size="lg" @close="closeAlumnosModal">
      <template #header>
        <div>
          <h2 class="dc__modal-title">Alumnos inscritos</h2>
          <p class="dc__modal-sub">{{ selectedCurso?.Materia?.Nombre }} · {{ selectedCurso?.Materia?.CodigoMateria }}</p>
        </div>
      </template>
      <div v-if="loadingAlumnos" class="dc__modal-loading">
        <AppSpinner size="md" />
        <p>Cargando alumnos inscritos…</p>
      </div>
      <AppEmptyState
        v-else-if="alumnos.length === 0"
        :icon="Users"
        title="Sin alumnos inscritos"
        description="Aún no hay estudiantes registrados en este curso."
      />
      <AppTable
        v-else
        :columns="studentColumns"
        :rows="alumnos"
        row-key="IdUsuario"
      >
        <template #cell-student="{ row }">
          <div class="dc__student">
            <AppAvatar :name="row.Nombre" variant="student" size="sm" />
            <strong>{{ row.Nombre }}</strong>
          </div>
        </template>
        <template #cell-mail="{ row }">
          <span class="dc__mail"><Mail :size="12" /> {{ row.Correo }}</span>
        </template>
        <template #cell-nota="{ row }">
          <AppBadge v-if="row.EstadoNota" :variant="row.Aprobado ? 'success' : 'danger'" size="sm">
            {{ row.Nota }}
          </AppBadge>
          <AppBadge v-else variant="neutral" size="sm">Sin nota</AppBadge>
        </template>
        <template #cell-status="{ row }">
          <AppBadge :variant="row.Aprobado ? 'success' : 'warning'" size="sm">
            <component :is="row.Aprobado ? CheckCircle2 : Clock" :size="11" />
            {{ row.Aprobado ? 'Aprobado' : 'Cursando' }}
          </AppBadge>
        </template>
      </AppTable>
    </AppModal>
  </AppShell>
</template>

<style scoped>
.dc {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.dc__stats {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 14px;
}

.dc__stat {
  display: flex;
  align-items: center;
  gap: 12px;
}

.dc__stat-icon {
  display: grid;
  place-items: center;
  width: 44px;
  height: 44px;
  border-radius: var(--radius-md);
  background: var(--color-primary-soft);
  color: var(--color-primary);
  flex-shrink: 0;
}
.dc__stat-icon--info { background: var(--color-info-soft); color: var(--color-info); }
.dc__stat-icon--success { background: var(--color-success-soft); color: var(--color-success); }
.dc__stat-icon--warning { background: var(--color-warning-soft); color: var(--color-warning); }

.dc__stat-label {
  margin: 0;
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  font-weight: 600;
  color: var(--color-text-muted);
}

.dc__stat-value {
  margin: 0;
  font-size: 1.4rem;
  font-weight: 800;
  color: var(--color-text-primary);
  line-height: 1;
}

.dc__loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 14px;
  padding: 60px 24px;
  text-align: center;
}

.dc__loading p {
  margin: 0;
  color: var(--color-text-muted);
}

.dc__grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
  gap: 18px;
}

.dc__course {
  display: flex;
  flex-direction: column;
}

.dc__course-head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 10px;
  padding: 18px 20px;
  background: rgba(28, 39, 66, 0.3);
  border-bottom: 1px solid var(--color-border-subtle);
}

.dc__course-code {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 0.74rem;
  font-weight: 700;
  color: var(--color-info);
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
  text-transform: uppercase;
}

.dc__course-head h3 {
  margin: 4px 0 0;
  font-size: 1.05rem;
  color: var(--color-text-primary);
  line-height: 1.3;
  font-weight: 700;
}

.dc__course-body {
  padding: 18px 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  flex: 1;
}

.dc__info {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  font-size: 0.85rem;
  color: var(--color-text-secondary);
  line-height: 1.5;
}

.dc__info strong {
  color: var(--color-text-primary);
  font-weight: 700;
  margin-right: 4px;
}

.dc__course-foot {
  padding: 14px 20px 18px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  border-top: 1px solid var(--color-border-subtle);
}

.dc__modal-title {
  margin: 0 0 4px;
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--color-text-primary);
}

.dc__modal-sub {
  margin: 0;
  font-size: 0.82rem;
  color: var(--color-text-muted);
}

.dc__modal-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 40px;
  text-align: center;
  color: var(--color-text-muted);
}

.dc__modal-loading p { margin: 0; }

.dc__student {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 0;
}

.dc__student strong {
  font-size: 0.9rem;
  color: var(--color-text-primary);
}

.dc__mail {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 0.82rem;
  color: var(--color-text-muted);
}

@media (max-width: 1024px) {
  .dc__stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .dc__grid { grid-template-columns: 1fr; }
}
</style>
