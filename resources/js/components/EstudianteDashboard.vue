<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import {
  Activity,
  ArrowRight,
  Award,
  BookOpen,
  Calendar,
  CheckCircle2,
  Clock,
  GraduationCap,
  Hash,
  Info,
  Lightbulb,
  MapPin,
  Plus,
  Sparkles,
  Target,
  TrendingUp,
  UserCheck,
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
  materiasInscritas: [],
  materiasActivas: [],
  progreso: { porcentaje: 0, estado: '', fechaInicio: null, fechaFin: null },
  actividades: [],
  info_relevante: { mensaje: '', ayuda: '' },
  carrera: '',
  modalidad: '',
  modalidadTipo: '',
});

const formatDate = (d) => {
  if (!d) return '—';
  return new Date(d).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' });
};
const formatTime = (t) => (t ? String(t).substring(0, 5) : '');

const progressStateVariant = computed(() => {
  const s = data.value.progreso?.estado;
  if (s === 'En curso') return 'info';
  if (s === 'Finalizado') return 'success';
  if (s === 'Próximo') return 'warning';
  return 'neutral';
});

const typeIcon = {
  inscripcion_estudiante: Plus,
  inscripcion: GraduationCap,
  sistema: Sparkles,
};

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
  <div class="ed">
    <header class="ed__hero">
      <div>
        <span class="ed__eyebrow"><Sparkles :size="14" /> Panel del estudiante</span>
        <h1>¡Bienvenido de vuelta!</h1>
        <p v-if="data.carrera || data.modalidad" class="ed__hero-sub">
          <span v-if="data.carrera"><GraduationCap :size="14" /> {{ data.carrera }}</span>
          <span v-if="data.modalidad" class="ed__hero-pill">{{ data.modalidad }} · {{ data.modalidadTipo }}</span>
        </p>
      </div>
    </header>

    <AppSpinner v-if="loading" :fullscreen="true" label="Cargando tu información..." />

    <template v-else>
      <section class="ed__stats">
        <AppStatCard
          v-for="(stat, i) in data.resumen"
          :key="i"
          :label="stat.titulo"
          :value="stat.valor"
          :variant="stat.variant || 'primary'"
          :count-up="typeof stat.valor === 'number'"
        />
      </section>

      <section class="ed__grid">
        <div class="ed__col-main">
          <AppCard>
            <template #title>
              <div class="ed__card-title">
                <BookOpen :size="18" />
                <span>{{ data.modalidadTipo === 'Modular' ? 'Materia activa del periodo' : 'Materias activas del periodo' }}</span>
              </div>
            </template>
            <template #actions>
              <AppBadge
                :variant="progressStateVariant"
                size="sm"
              >
                {{ data.progreso.estado || 'Sin materias activas' }}
              </AppBadge>
            </template>

            <div v-if="data.materiasActivas.length === 0">
              <AppEmptyState
                :icon="Clock"
                title="No tienes materias activas"
                description="Cuando inicien tus clases del periodo actual aparecerán aquí."
              />
            </div>

            <ul v-else class="ed__materias">
              <li v-for="m in data.materiasActivas" :key="m.IdCursoMateria" class="ed__materia">
                <div class="ed__materia-head">
                  <div class="ed__materia-icon"><BookOpen :size="18" /></div>
                  <div class="ed__materia-title">
                    <h4>{{ m.Materia }}</h4>
                    <span class="ed__materia-code"><Hash :size="11" /> {{ m.CodigoMateria }}</span>
                  </div>
                </div>
                <div class="ed__materia-info">
                  <div class="ed__info-row">
                    <UserCheck :size="13" />
                    <span><strong>Docente:</strong> {{ m.Docente || 'No asignado' }}</span>
                  </div>
                  <div class="ed__info-row">
                    <MapPin :size="13" />
                    <span><strong>Aula:</strong> {{ m.Aula }}<template v-if="m.Piso"> (Piso {{ m.Piso }})</template></span>
                  </div>
                  <div class="ed__info-row">
                    <Clock :size="13" />
                    <span><strong>Turno:</strong> {{ m.Turno }} {{ formatTime(m.HoraInicio) }}–{{ formatTime(m.HoraFin) }}</span>
                  </div>
                  <div class="ed__info-row">
                    <Calendar :size="13" />
                    <span><strong>Vigencia:</strong> {{ formatDate(m.FechaInicio) }} al {{ formatDate(m.FechaFin) }}</span>
                  </div>
                </div>
              </li>
            </ul>
          </AppCard>

          <AppCard>
            <template #title>
              <div class="ed__card-title">
                <Activity :size="18" />
                <span>Actividad reciente</span>
              </div>
            </template>

            <AppEmptyState
              v-if="data.actividades.length === 0"
              :icon="Clock"
              title="Sin actividad"
              description="Tu historial académico aparecerá aquí."
            />

            <ul v-else class="ed__timeline">
              <li
                v-for="(act, i) in data.actividades"
                :key="i"
                class="ed__timeline-item"
              >
                <div class="ed__timeline-badge">
                  <component :is="typeIcon[act.tipo] || Activity" :size="16" />
                </div>
                <div class="ed__timeline-panel">
                  <div class="ed__timeline-head">
                    <h4>{{ act.titulo }}</h4>
                    <span class="ed__timeline-time">{{ act.fecha }}</span>
                  </div>
                  <p>{{ act.descripcion }}</p>
                </div>
              </li>
            </ul>
          </AppCard>
        </div>

        <div class="ed__col-side">
          <AppCard>
            <template #title>
              <div class="ed__card-title">
                <Target :size="18" />
                <span>Progreso académico</span>
              </div>
            </template>

            <div class="ed__progress">
              <div class="ed__progress-circle" :style="{ '--p': data.progreso.porcentaje }">
                <strong>{{ data.progreso.porcentaje }}%</strong>
              </div>
              <div class="ed__progress-meta">
                <AppBadge :variant="progressStateVariant" size="sm">{{ data.progreso.estado || '—' }}</AppBadge>
                <p v-if="data.progreso.fechaInicio">
                  <Calendar :size="13" />
                  {{ formatDate(data.progreso.fechaInicio) }} → {{ formatDate(data.progreso.fechaFin) }}
                </p>
              </div>
            </div>
          </AppCard>

          <AppCard v-if="data.info_relevante?.mensaje">
            <template #title>
              <div class="ed__card-title">
                <Info :size="18" />
                <span>Información relevante</span>
              </div>
            </template>
            <p class="ed__info-msg">{{ data.info_relevante.mensaje }}</p>
            <div v-if="data.info_relevante?.ayuda" class="ed__info-tip">
              <Lightbulb :size="16" />
              <p>{{ data.info_relevante.ayuda }}</p>
            </div>
          </AppCard>

          <AppCard>
            <template #title>
              <div class="ed__card-title">
                <Sparkles :size="18" />
                <span>Accesos rápidos</span>
              </div>
            </template>
            <div class="ed__shortcuts">
              <a href="/cursos/inscripcion" class="ed__shortcut">
                <UserCheck :size="16" /> Inscripción a materias
                <ArrowRight :size="14" />
              </a>
              <a href="/mis-notas" class="ed__shortcut">
                <Award :size="16" /> Mis calificaciones
                <ArrowRight :size="14" />
              </a>
              <a href="/cursos/visualizacion" class="ed__shortcut">
                <BookOpen :size="16" /> Mis cursos
                <ArrowRight :size="14" />
              </a>
              <a href="/reportes" class="ed__shortcut">
                <TrendingUp :size="16" /> Mis reportes
                <ArrowRight :size="14" />
              </a>
              <a href="/perfil" class="ed__shortcut">
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
.ed {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.ed__hero {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

.ed__eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.18em;
  color: var(--color-primary);
  font-weight: 700;
  margin-bottom: 8px;
}

.ed__hero h1 {
  margin: 0;
  font-size: 1.85rem;
  font-weight: 800;
  letter-spacing: -0.02em;
  color: var(--color-text-primary);
}

.ed__hero-sub {
  margin: 8px 0 0;
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  color: var(--color-text-secondary);
  font-size: 0.95rem;
}

.ed__hero-pill {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  background: var(--color-role-student-soft);
  color: var(--color-role-student);
  border: 1px solid var(--color-role-student-border);
  border-radius: var(--radius-full);
  font-size: 0.78rem;
  font-weight: 700;
}

.ed__stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 16px;
}

.ed__grid {
  display: grid;
  grid-template-columns: 1fr 340px;
  gap: 20px;
  align-items: start;
}

.ed__col-main,
.ed__col-side {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.ed__card-title {
  display: flex;
  align-items: center;
  gap: 8px;
}

.ed__materias {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.ed__materia {
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 14px 16px;
  background: var(--color-surface-1);
  border: 1px solid var(--color-border-subtle);
  border-radius: var(--radius-md);
  transition: border-color var(--duration-fast) var(--ease-out);
}

.ed__materia:hover {
  border-color: var(--color-primary-border);
}

.ed__materia-head {
  display: flex;
  align-items: center;
  gap: 12px;
}

.ed__materia-icon {
  display: grid;
  place-items: center;
  width: 40px;
  height: 40px;
  border-radius: var(--radius-sm);
  background: var(--color-primary-soft);
  color: var(--color-primary);
  flex-shrink: 0;
}

.ed__materia-title h4 {
  margin: 0;
  font-size: 0.95rem;
  font-weight: 700;
  color: var(--color-text-primary);
}

.ed__materia-code {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 0.72rem;
  color: var(--color-text-muted);
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
  margin-top: 2px;
}

.ed__materia-info {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 6px 18px;
}

.ed__info-row {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.82rem;
  color: var(--color-text-secondary);
}

.ed__info-row strong {
  color: var(--color-text-primary);
  font-weight: 600;
  margin-right: 4px;
}

.ed__timeline {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
  position: relative;
}

.ed__timeline::before {
  content: '';
  position: absolute;
  top: 8px;
  bottom: 8px;
  left: 19px;
  width: 2px;
  background: var(--color-border-default);
}

.ed__timeline-item {
  display: flex;
  gap: 14px;
  position: relative;
}

.ed__timeline-badge {
  display: grid;
  place-items: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  flex-shrink: 0;
  background: var(--color-primary-soft);
  color: var(--color-primary);
  border: 2px solid var(--color-surface-1);
  z-index: 1;
}

.ed__timeline-panel {
  flex: 1;
  background: var(--color-surface-1);
  border: 1px solid var(--color-border-subtle);
  border-radius: var(--radius-md);
  padding: 12px 14px;
}

.ed__timeline-head {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  gap: 8px;
  margin-bottom: 4px;
}

.ed__timeline-head h4 {
  margin: 0;
  font-size: 0.9rem;
  font-weight: 700;
  color: var(--color-text-primary);
}

.ed__timeline-time {
  font-size: 0.72rem;
  color: var(--color-text-muted);
  white-space: nowrap;
}

.ed__timeline-panel p {
  margin: 0;
  font-size: 0.85rem;
  color: var(--color-text-secondary);
  line-height: 1.5;
}

.ed__progress {
  display: flex;
  align-items: center;
  gap: 18px;
  padding: 8px 4px;
}

.ed__progress-circle {
  --p: 0;
  --size: 96px;
  width: var(--size);
  height: var(--size);
  border-radius: 50%;
  background: conic-gradient(var(--color-primary) calc(var(--p) * 1%), var(--color-surface-3) 0);
  display: grid;
  place-items: center;
  position: relative;
  flex-shrink: 0;
}

.ed__progress-circle::before {
  content: '';
  position: absolute;
  inset: 8px;
  border-radius: 50%;
  background: var(--color-surface-1);
}

.ed__progress-circle strong {
  position: relative;
  font-size: 1.15rem;
  font-weight: 800;
  color: var(--color-text-primary);
  font-variant-numeric: tabular-nums;
}

.ed__progress-meta {
  display: flex;
  flex-direction: column;
  gap: 8px;
  min-width: 0;
}

.ed__progress-meta p {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  margin: 0;
  font-size: 0.78rem;
  color: var(--color-text-muted);
}

.ed__info-msg {
  margin: 0 0 12px;
  font-size: 0.9rem;
  color: var(--color-text-secondary);
  line-height: 1.5;
}

.ed__info-tip {
  display: flex;
  gap: 10px;
  padding: 12px;
  background: var(--color-warning-soft);
  border: 1px solid var(--color-warning-border);
  border-radius: var(--radius-md);
  color: var(--color-warning);
}

.ed__info-tip p {
  margin: 0;
  font-size: 0.85rem;
  color: var(--color-text-primary);
  line-height: 1.5;
}

.ed__shortcuts {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.ed__shortcut {
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

.ed__shortcut:hover {
  background: var(--color-primary-soft);
  border-color: var(--color-primary-border);
  color: var(--color-primary);
  transform: translateX(2px);
}

.ed__shortcut :last-child {
  margin-left: auto;
}

@media (max-width: 1024px) {
  .ed__grid { grid-template-columns: 1fr; }
  .ed__materia-info { grid-template-columns: 1fr; }
}

@media (max-width: 640px) {
  .ed__hero h1 { font-size: 1.45rem; }
}
</style>
