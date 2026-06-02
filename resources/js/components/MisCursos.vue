<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import {
  ArrowLeft,
  BookOpen,
  MapPin,
  Clock,
  Calendar,
  Hash,
  User as UserIcon,
  GraduationCap,
  CheckCircle2,
  Sparkles,
  TrendingUp,
  Award,
} from '@lucide/vue';
import AppShell from './layout/AppShell.vue';
import PageTransition from './layout/PageTransition.vue';
import AppCard from './ui/AppCard.vue';
import AppButton from './ui/AppButton.vue';
import AppBadge from './ui/AppBadge.vue';
import AppPageHeader from './ui/AppPageHeader.vue';
import AppSpinner from './ui/AppSpinner.vue';
import AppEmptyState from './ui/AppEmptyState.vue';
import { toast } from '../lib/toast.js';

const user = ref(null);
const loading = ref(false);
const materias = ref([]);
const activeFilter = ref('En curso');

const filters = [
  { key: 'En curso', label: 'En curso' },
  { key: 'Próxima', label: 'Próximas' },
  { key: 'Finalizada', label: 'Finalizadas' },
  { key: 'Aprobada', label: 'Aprobadas' },
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
    if (Number(user.value.IdRol) !== 3) {
      window.location.href = '/dashboard';
      return;
    }
  }
  await loadMaterias();
});

const loadMaterias = async () => {
  loading.value = true;
  try {
    const { data: res } = await axios.get('/api/auth/dashboard/stats');
    if (res?.status && res.data) {
      const all = res.data.materiasInscritas || [];
      const aprobadasIds = new Set(
        all.filter((m) => m.Aprobado).map((m) => m.IdCursoMateria)
      );
      materias.value = all.map((m) => ({
        ...m,
        Estado: aprobadasIds.has(m.IdCursoMateria) ? 'Aprobada' : m.Estado,
      }));
    } else {
      toast.error(res?.message || 'No se pudieron cargar tus cursos.');
    }
  } catch (err) {
    toast.error('No se pudieron cargar tus cursos.');
  } finally {
    loading.value = false;
  }
};

const filteredMaterias = computed(() => {
  if (activeFilter.value === 'Aprobada') {
    return materias.value.filter((m) => m.Estado === 'Aprobada');
  }
  return materias.value.filter((m) => m.Estado === activeFilter.value);
});

const counts = computed(() => ({
  'En curso': materias.value.filter((m) => m.Estado === 'En curso').length,
  'Próxima': materias.value.filter((m) => m.Estado === 'Próxima').length,
  'Finalizada': materias.value.filter((m) => m.Estado === 'Finalizada').length,
  'Aprobada': materias.value.filter((m) => m.Estado === 'Aprobada').length,
}));

const stateVariant = (estado) => {
  if (estado === 'En curso') return 'info';
  if (estado === 'Aprobada' || estado === 'Finalizada') return 'success';
  if (estado === 'Próxima') return 'warning';
  return 'neutral';
};

const formatDate = (d) => {
  if (!d) return '—';
  return new Date(d).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' });
};

const handleLogout = async () => {
  try {
    if (localStorage.getItem('auth_token')) await axios.post('/api/auth/logout');
  } catch {}
  localStorage.clear();
  delete axios.defaults.headers.common.Authorization;
  window.location.href = '/';
};

const goToDashboard = () => { window.location.href = '/dashboard'; };
</script>

<template>
  <AppShell v-if="user" :user="user" page-title="Mis cursos" @logout="handleLogout">
    <PageTransition>
      <div class="mc">
        <AppPageHeader
          eyebrow="Panel del estudiante"
          title="Mis cursos"
          description="Consulta las materias en las que estás inscrito durante el periodo académico actual."
        >
          <template #actions>
            <AppButton variant="secondary" :icon="ArrowLeft" @click="goToDashboard">
              Volver al Dashboard
            </AppButton>
          </template>
        </AppPageHeader>

        <section class="mc__stats">
          <AppCard padding="md" class="mc__stat">
            <div class="mc__stat-icon mc__stat-icon--info"><BookOpen :size="20" /></div>
            <div>
              <p class="mc__stat-label">En curso</p>
              <p class="mc__stat-value">{{ counts['En curso'] }}</p>
            </div>
          </AppCard>
          <AppCard padding="md" class="mc__stat">
            <div class="mc__stat-icon mc__stat-icon--warning"><Clock :size="20" /></div>
            <div>
              <p class="mc__stat-label">Próximas</p>
              <p class="mc__stat-value">{{ counts['Próxima'] }}</p>
            </div>
          </AppCard>
          <AppCard padding="md" class="mc__stat">
            <div class="mc__stat-icon mc__stat-icon--success"><CheckCircle2 :size="20" /></div>
            <div>
              <p class="mc__stat-label">Aprobadas</p>
              <p class="mc__stat-value">{{ counts['Aprobada'] }}</p>
            </div>
          </AppCard>
          <AppCard padding="md" class="mc__stat">
            <div class="mc__stat-icon mc__stat-icon--neutral"><TrendingUp :size="20" /></div>
            <div>
              <p class="mc__stat-label">Total</p>
              <p class="mc__stat-value">{{ materias.length }}</p>
            </div>
          </AppCard>
        </section>

        <AppCard padding="md">
          <div class="mc__filters" role="tablist">
            <button
              v-for="f in filters"
              :key="f.key"
              type="button"
              role="tab"
              :aria-selected="activeFilter === f.key"
              :class="['mc__filter', activeFilter === f.key && 'mc__filter--active']"
              @click="activeFilter = f.key"
            >
              {{ f.label }}
              <span v-if="counts[f.key] > 0" class="mc__filter-count">{{ counts[f.key] }}</span>
            </button>
          </div>
        </AppCard>

        <AppSpinner v-if="loading" :fullscreen="true" label="Cargando tus cursos..." />

        <AppEmptyState
          v-else-if="filteredMaterias.length === 0"
          :icon="Sparkles"
          :title="activeFilter === 'En curso' ? 'No tienes materias en curso' : 'Sin materias en esta categoría'"
          :description="activeFilter === 'En curso' ? 'Cuando inicien tus clases del periodo actual aparecerán aquí.' : 'Cambia el filtro para ver otras materias inscritas.'"
        />

        <div v-else class="mc__grid">
          <AppCard
            v-for="m in filteredMaterias"
            :key="m.IdCursoMateria"
            padding="none"
            class="mc__card"
          >
            <header class="mc__card-head">
              <div>
                <span class="mc__card-code"><Hash :size="11" /> {{ m.CodigoMateria }}</span>
                <h3>{{ m.Materia }}</h3>
              </div>
              <AppBadge :variant="stateVariant(m.Estado)" size="sm">
                <component :is="m.Estado === 'Aprobada' ? Award : CheckCircle2" :size="11" />
                {{ m.Estado }}
              </AppBadge>
            </header>
            <div class="mc__card-body">
              <div class="mc__info">
                <UserIcon :size="14" />
                <span><strong>Docente:</strong> {{ m.Docente || 'No asignado' }}</span>
              </div>
              <div class="mc__info">
                <MapPin :size="14" />
                <span><strong>Aula:</strong> {{ m.Aula || '—' }}<template v-if="m.Piso"> (Piso {{ m.Piso }})</template></span>
              </div>
              <div class="mc__info">
                <Clock :size="14" />
                <span><strong>Turno:</strong> {{ m.Turno || '—' }} {{ m.HoraInicio ? `(${m.HoraInicio?.substring(0,5)}–${m.HoraFin?.substring(0,5)})` : '' }}</span>
              </div>
              <div class="mc__info">
                <Calendar :size="14" />
                <span><strong>Vigencia:</strong> {{ formatDate(m.FechaInicio) }} al {{ formatDate(m.FechaFin) }}</span>
              </div>
              <div v-if="m.Nota !== null && m.Nota !== undefined" class="mc__info">
                <GraduationCap :size="14" />
                <span><strong>Nota:</strong> <strong :class="['mc__nota', Number(m.Nota) >= 51 ? 'mc__nota--ok' : 'mc__nota--bad']">{{ m.Nota }}</strong> / 100</span>
              </div>
            </div>
            <footer class="mc__card-foot">
              <span class="mc__inscrito">Inscrito el {{ formatDate(m.FechaInscripcion) }}</span>
            </footer>
          </AppCard>
        </div>
      </div>
    </PageTransition>
  </AppShell>
</template>

<style scoped>
.mc {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.mc__stats {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 14px;
}

.mc__stat {
  display: flex;
  align-items: center;
  gap: 12px;
}

.mc__stat-icon {
  display: grid;
  place-items: center;
  width: 44px;
  height: 44px;
  border-radius: var(--radius-md);
  background: var(--color-primary-soft);
  color: var(--color-primary);
  flex-shrink: 0;
}
.mc__stat-icon--success { background: var(--color-success-soft); color: var(--color-success); }
.mc__stat-icon--warning { background: var(--color-warning-soft); color: var(--color-warning); }
.mc__stat-icon--info    { background: var(--color-info-soft);    color: var(--color-info); }
.mc__stat-icon--neutral { background: var(--color-surface-3);    color: var(--color-text-secondary); }

.mc__stat-label {
  margin: 0;
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  font-weight: 700;
  color: var(--color-text-muted);
}

.mc__stat-value {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 800;
  color: var(--color-text-primary);
  line-height: 1;
}

.mc__filters {
  display: flex;
  gap: 6px;
  padding: 4px;
  background: var(--color-surface-3);
  border-radius: var(--radius-md);
  flex-wrap: wrap;
}

.mc__filter {
  flex: 1;
  min-width: 100px;
  padding: 8px 12px;
  background: transparent;
  border: 0;
  color: var(--color-text-muted);
  font-size: 0.82rem;
  font-weight: 700;
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: all var(--duration-fast) var(--ease-out);
  min-height: 36px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.mc__filter:hover { color: var(--color-text-primary); }
.mc__filter--active {
  background: var(--color-primary);
  color: white;
  box-shadow: var(--shadow-sm);
}

.mc__filter-count {
  font-size: 0.7rem;
  padding: 1px 7px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: var(--radius-full);
  font-weight: 800;
}
.mc__filter:not(.mc__filter--active) .mc__filter-count {
  background: var(--color-surface-2);
  color: var(--color-text-muted);
}

.mc__grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
  gap: 16px;
}

.mc__card {
  display: flex;
  flex-direction: column;
  transition: transform var(--duration-fast) var(--ease-out);
}
.mc__card:hover {
  transform: translateY(-2px);
  border-color: var(--color-primary-border);
}

.mc__card-head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 10px;
  padding: 18px 20px;
  background: rgba(28, 39, 66, 0.3);
  border-bottom: 1px solid var(--color-border-subtle);
}

.mc__card-code {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 0.72rem;
  font-weight: 700;
  color: var(--color-primary);
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
  text-transform: uppercase;
}

.mc__card-head h3 {
  margin: 4px 0 0;
  font-size: 1rem;
  color: var(--color-text-primary);
  line-height: 1.3;
  font-weight: 700;
}

.mc__card-body {
  padding: 16px 20px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  flex: 1;
}

.mc__info {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  font-size: 0.85rem;
  color: var(--color-text-secondary);
  line-height: 1.4;
}

.mc__info strong {
  color: var(--color-text-primary);
  font-weight: 700;
  margin-right: 4px;
}

.mc__nota {
  font-weight: 800;
  font-variant-numeric: tabular-nums;
}
.mc__nota--ok  { color: var(--color-success); }
.mc__nota--bad { color: var(--color-danger); }

.mc__card-foot {
  padding: 12px 20px 14px;
  border-top: 1px solid var(--color-border-subtle);
  display: flex;
  align-items: center;
  justify-content: flex-end;
}

.mc__inscrito {
  font-size: 0.74rem;
  color: var(--color-text-muted);
  font-weight: 600;
}

@media (max-width: 900px) {
  .mc__stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .mc__grid  { grid-template-columns: 1fr; }
}
</style>
