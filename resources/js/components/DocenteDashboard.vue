<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import {
  Activity,
  ArrowRight,
  BarChart3,
  BookOpen,
  Calendar,
  CheckCircle2,
  Clock,
  ClipboardList,
  GraduationCap,
  Hash,
  Info,
  Lightbulb,
  MapPin,
  Plus,
  Sparkles,
  TrendingUp,
  UserCheck,
  Users,
} from '@lucide/vue';
import AppCard from './ui/AppCard.vue';
import AppBadge from './ui/AppBadge.vue';
import AppStatCard from './ui/AppStatCard.vue';
import AppSpinner from './ui/AppSpinner.vue';
import AppEmptyState from './ui/AppEmptyState.vue';
import { toast } from '../lib/toast.js';

const loading = ref(false);
const data = ref({
  resumen: [],
  materiasAsignadas: [],
  actividades: [],
  info_relevante: { mensaje: '', ayuda: '' },
});

const formatDate = (d) => {
  if (!d) return '—';
  return new Date(d).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' });
};
const formatTime = (t) => (t ? String(t).substring(0, 5) : '');

const stateVariant = (estado) => {
  if (estado === 'En curso') return 'info';
  if (estado === 'Finalizada') return 'success';
  if (estado === 'Próxima') return 'warning';
  return 'neutral';
};

const activeCount = computed(
  () => data.value.materiasAsignadas.filter((m) => m.Estado === 'En curso').length
);
const totalCapacity = computed(() =>
  data.value.materiasAsignadas.reduce((acc, m) => acc + (m.Inscritos || 0), 0)
);
const totalMax = computed(() =>
  data.value.materiasAsignadas.reduce((acc, m) => acc + (m.MaxInscritos || 0), 0)
);

const loadStats = async () => {
  loading.value = true;
  try {
    const { data: res } = await axios.get('/api/auth/dashboard/stats');
    if (res?.status && res.data) {
      data.value = res.data;
    } else {
      toast.error(res?.message || 'No se pudieron cargar las estadísticas.');
    }
  } catch (err) {
    toast.error('No se pudieron cargar las estadísticas.');
  } finally {
    loading.value = false;
  }
};

onMounted(loadStats);
</script>

<template>
  <div class="dd">
    <header class="dd__hero">
      <div>
        <span class="dd__eyebrow"><Sparkles :size="14" /> Panel del docente</span>
        <h1>¡Bienvenido de vuelta!</h1>
        <p class="dd__hero-sub">Supervisa tus cursos, estudiantes y carga académica del periodo.</p>
      </div>
    </header>

    <AppSpinner v-if="loading" :fullscreen="true" label="Cargando tu información..." />

    <template v-else>
      <section class="dd__stats">
        <AppStatCard
          v-for="(stat, i) in data.resumen"
          :key="i"
          :label="stat.titulo"
          :value="stat.valor"
          :variant="stat.variant || 'primary'"
          :count-up="typeof stat.valor === 'number'"
        />
      </section>

      <section class="dd__grid">
        <div class="dd__col-main">
          <AppCard>
            <template #title>
              <div class="dd__card-title">
                <GraduationCap :size="18" />
                <span>Mis materias asignadas</span>
              </div>
            </template>
            <template #actions>
              <AppBadge variant="primary" size="sm">{{ data.materiasAsignadas.length }} curso(s)</AppBadge>
            </template>

            <AppEmptyState
              v-if="data.materiasAsignadas.length === 0"
              :icon="BookOpen"
              title="Sin materias asignadas"
              description="Aún no tienes cursos asignados en el periodo actual."
            />

            <ul v-else class="dd__materias">
              <li v-for="m in data.materiasAsignadas" :key="m.IdCursoMateria" class="dd__materia">
                <div class="dd__materia-head">
                  <div class="dd__materia-icon"><BookOpen :size="18" /></div>
                  <div class="dd__materia-title">
                    <h4>{{ m.Materia }}</h4>
                    <span class="dd__materia-code"><Hash :size="11" /> {{ m.CodigoMateria }}</span>
                  </div>
                  <AppBadge :variant="stateVariant(m.Estado)" size="sm">{{ m.Estado }}</AppBadge>
                </div>

                <div class="dd__materia-info">
                  <div class="dd__info-row">
                    <MapPin :size="13" />
                    <span><strong>Aula:</strong> {{ m.Aula }}<template v-if="m.Piso"> (Piso {{ m.Piso }})</template></span>
                  </div>
                  <div class="dd__info-row">
                    <Clock :size="13" />
                    <span><strong>Turno:</strong> {{ m.Turno }} {{ formatTime(m.HoraInicio) }}–{{ formatTime(m.HoraFin) }}</span>
                  </div>
                  <div class="dd__info-row">
                    <Calendar :size="13" />
                    <span><strong>Vigencia:</strong> {{ formatDate(m.FechaInicio) }} al {{ formatDate(m.FechaFin) }}</span>
                  </div>
                  <div class="dd__info-row">
                    <Users :size="13" />
                    <span><strong>Inscritos:</strong> {{ m.Inscritos }} / {{ m.MaxInscritos }}</span>
                  </div>
                </div>

                <div class="dd__progress">
                  <div class="dd__progress-bar">
                    <div
                      class="dd__progress-fill"
                      :style="{ width: `${m.Progreso}%` }"
                    ></div>
                  </div>
                  <span class="dd__progress-label">{{ m.Progreso }}%</span>
                </div>
              </li>
            </ul>
          </AppCard>

          <AppCard>
            <template #title>
              <div class="dd__card-title">
                <Activity :size="18" />
                <span>Actividad reciente</span>
              </div>
            </template>

            <AppEmptyState
              v-if="data.actividades.length === 0"
              :icon="Clock"
              title="Sin actividad"
              description="Las inscripciones recientes en tus cursos aparecerán aquí."
            />

            <ul v-else class="dd__timeline">
              <li v-for="(act, i) in data.actividades" :key="i" class="dd__timeline-item">
                <div class="dd__timeline-badge"><UserCheck :size="16" /></div>
                <div class="dd__timeline-panel">
                  <div class="dd__timeline-head">
                    <h4>{{ act.titulo }}</h4>
                    <span class="dd__timeline-time">{{ act.fecha }}</span>
                  </div>
                  <p>{{ act.descripcion }}</p>
                </div>
              </li>
            </ul>
          </AppCard>
        </div>

        <div class="dd__col-side">
          <AppCard>
            <template #title>
              <div class="dd__card-title">
                <TrendingUp :size="18" />
                <span>Resumen del periodo</span>
              </div>
            </template>
            <div class="dd__summary">
              <div class="dd__summary-row">
                <span>Cursos activos</span>
                <strong>{{ activeCount }}</strong>
              </div>
              <div class="dd__summary-row">
                <span>Total de cursos</span>
                <strong>{{ data.materiasAsignadas.length }}</strong>
              </div>
              <div class="dd__summary-row">
                <span>Cupo total ocupado</span>
                <strong>{{ totalCapacity }} / {{ totalMax }}</strong>
              </div>
            </div>
          </AppCard>

          <AppCard v-if="data.info_relevante?.mensaje">
            <template #title>
              <div class="dd__card-title">
                <Info :size="18" />
                <span>Información relevante</span>
              </div>
            </template>
            <p class="dd__info-msg">{{ data.info_relevante.mensaje }}</p>
            <div v-if="data.info_relevante?.ayuda" class="dd__info-tip">
              <Lightbulb :size="16" />
              <p>{{ data.info_relevante.ayuda }}</p>
            </div>
          </AppCard>

          <AppCard>
            <template #title>
              <div class="dd__card-title">
                <Sparkles :size="18" />
                <span>Accesos rápidos</span>
              </div>
            </template>
            <div class="dd__shortcuts">
              <a href="/docente/cursos" class="dd__shortcut">
                <GraduationCap :size="16" /> Mis cursos
                <ArrowRight :size="14" />
              </a>
              <a href="/docente/notas" class="dd__shortcut">
                <ClipboardList :size="16" /> Gestionar notas
                <ArrowRight :size="14" />
              </a>
              <a href="/reportes" class="dd__shortcut">
                <BarChart3 :size="16" /> Reportes
                <ArrowRight :size="14" />
              </a>
              <a href="/perfil" class="dd__shortcut">
                <UserCheck :size="16" /> Mi perfil
                <ArrowRight :size="14" />
              </a>
            </div>
          </AppCard>
        </div>
      </section>
    </template>
  </div>
</template>

<style scoped>
.dd {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.dd__hero {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

.dd__eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.18em;
  color: var(--color-role-teacher);
  font-weight: 700;
  margin-bottom: 8px;
}

.dd__hero h1 {
  margin: 0;
  font-size: 1.85rem;
  font-weight: 800;
  letter-spacing: -0.02em;
  color: var(--color-text-primary);
}

.dd__hero-sub {
  margin: 8px 0 0;
  color: var(--color-text-secondary);
  font-size: 0.95rem;
  max-width: 640px;
}

.dd__stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 16px;
}

.dd__grid {
  display: grid;
  grid-template-columns: 1fr 340px;
  gap: 20px;
  align-items: start;
}

.dd__col-main,
.dd__col-side {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.dd__card-title {
  display: flex;
  align-items: center;
  gap: 8px;
}

.dd__materias {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.dd__materia {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 14px 16px;
  background: var(--color-surface-1);
  border: 1px solid var(--color-border-subtle);
  border-radius: var(--radius-md);
  transition: border-color var(--duration-fast) var(--ease-out);
}

.dd__materia:hover {
  border-color: var(--color-role-teacher-border);
}

.dd__materia-head {
  display: flex;
  align-items: center;
  gap: 12px;
}

.dd__materia-icon {
  display: grid;
  place-items: center;
  width: 40px;
  height: 40px;
  border-radius: var(--radius-sm);
  background: var(--color-role-teacher-soft);
  color: var(--color-role-teacher);
  flex-shrink: 0;
}

.dd__materia-title {
  flex: 1;
  min-width: 0;
}

.dd__materia-title h4 {
  margin: 0;
  font-size: 0.95rem;
  font-weight: 700;
  color: var(--color-text-primary);
}

.dd__materia-code {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 0.72rem;
  color: var(--color-text-muted);
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
  margin-top: 2px;
}

.dd__materia-info {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 6px 18px;
}

.dd__info-row {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.82rem;
  color: var(--color-text-secondary);
}

.dd__info-row strong {
  color: var(--color-text-primary);
  font-weight: 600;
  margin-right: 4px;
}

.dd__progress {
  display: flex;
  align-items: center;
  gap: 12px;
}

.dd__progress-bar {
  flex: 1;
  height: 8px;
  background: var(--color-surface-3);
  border-radius: var(--radius-full);
  overflow: hidden;
}

.dd__progress-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--color-role-teacher) 0%, var(--color-primary) 100%);
  border-radius: inherit;
  transition: width var(--duration-base) var(--ease-out);
}

.dd__progress-label {
  font-size: 0.78rem;
  font-weight: 700;
  color: var(--color-text-primary);
  min-width: 38px;
  text-align: right;
  font-variant-numeric: tabular-nums;
}

.dd__timeline {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
  position: relative;
}

.dd__timeline::before {
  content: '';
  position: absolute;
  top: 8px;
  bottom: 8px;
  left: 19px;
  width: 2px;
  background: var(--color-border-default);
}

.dd__timeline-item {
  display: flex;
  gap: 14px;
  position: relative;
}

.dd__timeline-badge {
  display: grid;
  place-items: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  flex-shrink: 0;
  background: var(--color-role-teacher-soft);
  color: var(--color-role-teacher);
  border: 2px solid var(--color-surface-1);
  z-index: 1;
}

.dd__timeline-panel {
  flex: 1;
  background: var(--color-surface-1);
  border: 1px solid var(--color-border-subtle);
  border-radius: var(--radius-md);
  padding: 12px 14px;
}

.dd__timeline-head {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  gap: 8px;
  margin-bottom: 4px;
}

.dd__timeline-head h4 {
  margin: 0;
  font-size: 0.9rem;
  font-weight: 700;
  color: var(--color-text-primary);
}

.dd__timeline-time {
  font-size: 0.72rem;
  color: var(--color-text-muted);
  white-space: nowrap;
}

.dd__timeline-panel p {
  margin: 0;
  font-size: 0.85rem;
  color: var(--color-text-secondary);
  line-height: 1.5;
}

.dd__summary {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.dd__summary-row {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px solid var(--color-border-subtle);
  font-size: 0.88rem;
}

.dd__summary-row:last-child {
  border-bottom: 0;
}

.dd__summary-row span {
  color: var(--color-text-secondary);
}

.dd__summary-row strong {
  color: var(--color-text-primary);
  font-weight: 700;
  font-variant-numeric: tabular-nums;
}

.dd__info-msg {
  margin: 0 0 12px;
  font-size: 0.9rem;
  color: var(--color-text-secondary);
  line-height: 1.5;
}

.dd__info-tip {
  display: flex;
  gap: 10px;
  padding: 12px;
  background: var(--color-warning-soft);
  border: 1px solid var(--color-warning-border);
  border-radius: var(--radius-md);
  color: var(--color-warning);
}

.dd__info-tip p {
  margin: 0;
  font-size: 0.85rem;
  color: var(--color-text-primary);
  line-height: 1.5;
}

.dd__shortcuts {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.dd__shortcut {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  background: var(--color-surface-1);
  border: 1px solid var(--color-border-subtle);
  border-radius: var(--radius-md);
  color: var(--color-text-secondary);
  font-weight: 600;
  font-size: 0.85rem;
  text-decoration: none;
  transition: all var(--duration-fast) var(--ease-out);
  min-height: 44px;
}

.dd__shortcut:hover {
  background: var(--color-role-teacher-soft);
  border-color: var(--color-role-teacher-border);
  color: var(--color-role-teacher);
  transform: translateX(2px);
}

.dd__shortcut :last-child {
  margin-left: auto;
}

@media (max-width: 1024px) {
  .dd__grid { grid-template-columns: 1fr; }
  .dd__materia-info { grid-template-columns: 1fr; }
}

@media (max-width: 640px) {
  .dd__hero h1 { font-size: 1.45rem; }
}
</style>
