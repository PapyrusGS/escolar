<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import {
  Award,
  CheckCircle2,
  XCircle,
  Clock,
  ArrowLeft,
  TrendingUp,
  TrendingDown,
  Target,
  BookOpen,
  User as UserIcon,
  Hash,
  MapPin,
  Calendar,
  Sparkles,
} from '@lucide/vue';
import AppShell from './layout/AppShell.vue';
import PageTransition from './layout/PageTransition.vue';
import AppCard from './ui/AppCard.vue';
import AppButton from './ui/AppButton.vue';
import AppPageHeader from './ui/AppPageHeader.vue';
import AppBadge from './ui/AppBadge.vue';
import AppSpinner from './ui/AppSpinner.vue';
import AppEmptyState from './ui/AppEmptyState.vue';
import AppAlert from './ui/AppAlert.vue';
import { useGoTo } from '../composables/useGoTo.js';

const { goTo } = useGoTo();

const user = ref(null);
const notas = ref([]);
const stats = ref({ promedio: 0, aprobadas: 0, reprobadas: 0, sinNota: 0, total: 0 });
const loading = ref(false);
const errorMsg = ref('');

onMounted(async () => {
  const token = localStorage.getItem('auth_token');
  if (!token) {
    window.location.href = '/';
    return;
  }
  axios.defaults.headers.common.Authorization = `Bearer ${token}`;
  const stored = localStorage.getItem('auth_user');
  if (stored) user.value = JSON.parse(stored);
  await loadNotas();
});

const loadNotas = async () => {
  loading.value = true;
  errorMsg.value = '';
  try {
    const { data } = await axios.get('/api/estudiante/notas');
    if (data?.status) {
      notas.value = data.data?.notas || [];
      stats.value = {
        promedio: data.data?.promedio || 0,
        aprobadas: data.data?.aprobadas || 0,
        reprobadas: data.data?.reprobadas || 0,
        sinNota: data.data?.sinNota || 0,
        total: data.data?.total || 0,
      };
    } else {
      errorMsg.value = data?.message || 'No se pudieron cargar las calificaciones.';
    }
  } catch (err) {
    errorMsg.value = err?.response?.data?.message || 'No se pudieron cargar las calificaciones.';
  } finally {
    loading.value = false;
  }
};

const formatDate = (d) => {
  if (!d) return '—';
  return new Date(d).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' });
};
const formatTime = (t) => (t ? String(t).substring(0, 5) : '');

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
  <AppShell v-if="user" :user="user" page-title="Mis calificaciones" @logout="handleLogout">
    <PageTransition>
      <div class="mn">
        <AppPageHeader
          title="Mis calificaciones"
          description="Consulta tus notas registradas en cada materia inscrita del ciclo lectivo."
        >
          <template #actions>
            <AppButton variant="secondary" :icon="ArrowLeft" @click="goTo('/dashboard')">
              Volver al Dashboard
            </AppButton>
          </template>
        </AppPageHeader>

        <AppAlert v-if="errorMsg" variant="danger" :title="errorMsg" dismissible @dismiss="errorMsg = ''" />

        <AppSpinner v-if="loading" :fullscreen="true" label="Cargando calificaciones..." />

        <template v-else>
          <section class="mn__stats">
            <AppCard padding="md" class="mn__stat">
              <div class="mn__stat-icon mn__stat-icon--primary"><Target :size="20" /></div>
              <div>
                <p class="mn__stat-label">Promedio general</p>
                <p class="mn__stat-value">{{ stats.promedio }}</p>
              </div>
            </AppCard>
            <AppCard padding="md" class="mn__stat">
              <div class="mn__stat-icon mn__stat-icon--success"><CheckCircle2 :size="20" /></div>
              <div>
                <p class="mn__stat-label">Aprobadas</p>
                <p class="mn__stat-value">{{ stats.aprobadas }}</p>
              </div>
            </AppCard>
            <AppCard padding="md" class="mn__stat">
              <div class="mn__stat-icon mn__stat-icon--danger"><XCircle :size="20" /></div>
              <div>
                <p class="mn__stat-label">Reprobadas</p>
                <p class="mn__stat-value">{{ stats.reprobadas }}</p>
              </div>
            </AppCard>
            <AppCard padding="md" class="mn__stat">
              <div class="mn__stat-icon mn__stat-icon--info"><Clock :size="20" /></div>
              <div>
                <p class="mn__stat-label">Sin nota</p>
                <p class="mn__stat-value">{{ stats.sinNota }}</p>
              </div>
            </AppCard>
          </section>

          <AppEmptyState
            v-if="!loading && notas.length === 0"
            :icon="Sparkles"
            title="Aún no tienes calificaciones"
            description="Cuando un docente registre notas en tus materias inscritas aparecerán aquí."
          />

          <div v-else class="mn__list">
            <AppCard
              v-for="n in notas"
              :key="n.IdInscripcion"
              padding="none"
              :class="['mn__item', !n.Nota && 'mn__item--pending']"
            >
              <div class="mn__item-head">
                <div class="mn__item-title">
                  <div class="mn__item-icon"><BookOpen :size="20" /></div>
                  <div>
                    <h3>{{ n.Materia }}</h3>
                    <span class="mn__item-code"><Hash :size="11" /> {{ n.CodigoMateria }}</span>
                  </div>
                </div>
                <div class="mn__item-grade">
                  <template v-if="n.Nota !== null">
                    <strong
                      :class="[
                        'mn__grade-value',
                        n.Nota >= 51 ? 'mn__grade-value--ok' : 'mn__grade-value--bad'
                      ]"
                    >{{ n.Nota }}</strong>
                    <span class="mn__grade-label">/ 100</span>
                  </template>
                  <AppBadge v-else variant="neutral" size="md">Sin nota</AppBadge>
                </div>
              </div>

              <div class="mn__item-body">
                <div class="mn__info">
                  <UserIcon :size="14" />
                  <span><strong>Docente:</strong> {{ n.Docente || 'No asignado' }}</span>
                </div>
                <div class="mn__info">
                  <MapPin :size="14" />
                  <span><strong>Aula:</strong> {{ n.Aula || '—' }} <template v-if="n.Piso">(Piso {{ n.Piso }})</template></span>
                </div>
                <div class="mn__info">
                  <Clock :size="14" />
                  <span><strong>Turno:</strong> {{ n.Turno || '—' }}</span>
                </div>
                <div class="mn__info">
                  <Calendar :size="14" />
                  <span><strong>Vigencia:</strong> {{ formatDate(n.FechaInicio) }} al {{ formatDate(n.FechaFin) }}</span>
                </div>
              </div>

              <div class="mn__item-foot">
                <AppBadge :variant="n.Estado === 'Aprobada' ? 'success' : n.Estado === 'Reprobada' ? 'danger' : 'neutral'" size="sm">
                  <component :is="n.Estado === 'Aprobada' ? CheckCircle2 : n.Estado === 'Reprobada' ? XCircle : Clock" :size="11" />
                  {{ n.Estado }}
                </AppBadge>
                <span class="mn__item-date">Inscrito el {{ formatDate(n.FechaInscripcion) }}</span>
              </div>
            </AppCard>
          </div>
        </template>
      </div>
    </PageTransition>
  </AppShell>
</template>

<style scoped>
.mn {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.mn__stats {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 14px;
}

.mn__stat {
  display: flex;
  align-items: center;
  gap: 12px;
}

.mn__stat-icon {
  display: grid;
  place-items: center;
  width: 44px;
  height: 44px;
  border-radius: var(--radius-md);
  background: var(--color-primary-soft);
  color: var(--color-primary);
  flex-shrink: 0;
}
.mn__stat-icon--success { background: var(--color-success-soft); color: var(--color-success); }
.mn__stat-icon--danger { background: var(--color-danger-soft); color: var(--color-danger); }
.mn__stat-icon--info { background: var(--color-info-soft); color: var(--color-info); }

.mn__stat-label {
  margin: 0;
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  font-weight: 700;
  color: var(--color-text-muted);
}

.mn__stat-value {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 800;
  color: var(--color-text-primary);
  line-height: 1;
}

.mn__list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
  gap: 16px;
}

.mn__item {
  display: flex;
  flex-direction: column;
  transition: transform var(--duration-fast) var(--ease-out);
}

.mn__item:hover {
  transform: translateY(-2px);
  border-color: var(--color-primary-border);
}

.mn__item--pending {
  opacity: 0.92;
}

.mn__item-head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 14px;
  padding: 18px 20px;
  background: rgba(28, 39, 66, 0.3);
  border-bottom: 1px solid var(--color-border-subtle);
}

.mn__item-title {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
  flex: 1;
}

.mn__item-icon {
  display: grid;
  place-items: center;
  width: 40px;
  height: 40px;
  border-radius: var(--radius-sm);
  background: var(--color-primary-soft);
  color: var(--color-primary);
  flex-shrink: 0;
}

.mn__item-title h3 {
  margin: 0;
  font-size: 1rem;
  font-weight: 700;
  color: var(--color-text-primary);
  line-height: 1.3;
}

.mn__item-code {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 0.72rem;
  color: var(--color-text-muted);
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
  margin-top: 2px;
}

.mn__item-grade {
  display: flex;
  align-items: baseline;
  gap: 4px;
  flex-shrink: 0;
}

.mn__grade-value {
  font-size: 1.85rem;
  font-weight: 800;
  line-height: 1;
  font-variant-numeric: tabular-nums;
}

.mn__grade-value--ok { color: var(--color-success); }
.mn__grade-value--bad { color: var(--color-danger); }

.mn__grade-label {
  font-size: 0.78rem;
  color: var(--color-text-muted);
  font-weight: 600;
}

.mn__item-body {
  padding: 16px 20px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  flex: 1;
}

.mn__info {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  font-size: 0.85rem;
  color: var(--color-text-secondary);
  line-height: 1.4;
}

.mn__info strong {
  color: var(--color-text-primary);
  font-weight: 700;
  margin-right: 4px;
}

.mn__item-foot {
  padding: 12px 20px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  border-top: 1px solid var(--color-border-subtle);
}

.mn__item-date {
  font-size: 0.78rem;
  color: var(--color-text-muted);
}

@media (max-width: 900px) {
  .mn__stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .mn__list { grid-template-columns: 1fr; }
}
</style>
