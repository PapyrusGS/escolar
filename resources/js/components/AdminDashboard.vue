<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import {
  Activity,
  ArrowRight,
  Award,
  BarChart3,
  BookOpen,
  Calendar,
  Clock,
  GraduationCap,
  Info,
  Lightbulb,
  Plus,
  Shield,
  Sparkles,
  UserCheck,
  UserPlus,
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
  actividades: [],
  info_relevante: { mensaje: '', ayuda: '' },
});

const typeIcon = {
  usuario: Users,
  curso: Calendar,
  inscripcion: GraduationCap,
  inscripcion_estudiante: Plus,
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
  <div class="ad">
    <header class="ad__hero">
      <div>
        <span class="ad__eyebrow"><Shield :size="14" /> Panel del administrador</span>
        <h1>¡Bienvenido de vuelta!</h1>
        <p class="ad__hero-sub">Control total sobre la carga académica, periodos escolares y operaciones del sistema.</p>
      </div>
    </header>

    <AppSpinner v-if="loading" :fullscreen="true" label="Cargando estadísticas..." />

    <template v-else>
      <section class="ad__stats">
        <AppStatCard
          v-for="(stat, i) in data.resumen"
          :key="i"
          :label="stat.titulo"
          :value="stat.valor"
          :variant="stat.variant || 'primary'"
          :count-up="typeof stat.valor === 'number'"
        />
      </section>

      <section class="ad__grid">
        <div class="ad__col-main">
          <AppCard>
            <template #title>
              <div class="ad__card-title">
                <Activity :size="18" />
                <span>Actividad reciente del sistema</span>
              </div>
            </template>
            <template #actions>
              <AppBadge variant="primary" size="sm">Últimos movimientos</AppBadge>
            </template>

            <AppEmptyState
              v-if="data.actividades.length === 0"
              :icon="Clock"
              title="Sin actividad"
              description="Cuando haya nuevos registros académicos aparecerán aquí."
            />

            <ul v-else class="ad__timeline">
              <li
                v-for="(act, i) in data.actividades"
                :key="i"
                :class="['ad__timeline-item', `ad__timeline-item--${act.tipo}`]"
              >
                <div :class="['ad__timeline-badge', `ad__timeline-badge--${act.tipo}`]">
                  <component :is="typeIcon[act.tipo] || Activity" :size="16" />
                </div>
                <div class="ad__timeline-panel">
                  <div class="ad__timeline-head">
                    <h4>{{ act.titulo }}</h4>
                    <span class="ad__timeline-time">{{ act.fecha }}</span>
                  </div>
                  <p>{{ act.descripcion }}</p>
                </div>
              </li>
            </ul>
          </AppCard>
        </div>

        <div class="ad__col-side">
          <AppCard v-if="data.info_relevante?.mensaje">
            <template #title>
              <div class="ad__card-title">
                <Info :size="18" />
                <span>Información relevante</span>
              </div>
            </template>
            <p class="ad__info-msg">{{ data.info_relevante.mensaje }}</p>
            <div v-if="data.info_relevante?.ayuda" class="ad__info-tip">
              <Lightbulb :size="16" />
              <p>{{ data.info_relevante.ayuda }}</p>
            </div>
          </AppCard>

          <AppCard>
            <template #title>
              <div class="ad__card-title">
                <Sparkles :size="18" />
                <span>Accesos rápidos</span>
              </div>
            </template>
            <div class="ad__shortcuts">
              <a href="/usuarios" class="ad__shortcut">
                <Users :size="16" /> Gestionar usuarios
                <ArrowRight :size="14" />
              </a>
              <a href="/usuarios/create" class="ad__shortcut">
                <UserPlus :size="16" /> Registrar usuario
                <ArrowRight :size="14" />
              </a>
              <a href="/cursos" class="ad__shortcut">
                <GraduationCap :size="16" /> Gestionar cursos
                <ArrowRight :size="14" />
              </a>
              <a href="/cursos/visualizacion" class="ad__shortcut">
                <BookOpen :size="16" /> Cursos por usuario
                <ArrowRight :size="14" />
              </a>
              <a href="/reportes" class="ad__shortcut">
                <BarChart3 :size="16" /> Reportes
                <ArrowRight :size="14" />
              </a>
              <a href="/perfil" class="ad__shortcut">
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
.ad {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.ad__hero {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

.ad__eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.18em;
  color: var(--color-role-admin);
  font-weight: 700;
  margin-bottom: 8px;
}

.ad__hero h1 {
  margin: 0;
  font-size: 1.85rem;
  font-weight: 800;
  letter-spacing: -0.02em;
  color: var(--color-text-primary);
}

.ad__hero-sub {
  margin: 8px 0 0;
  color: var(--color-text-secondary);
  font-size: 0.95rem;
  max-width: 640px;
}

.ad__stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 16px;
}

.ad__grid {
  display: grid;
  grid-template-columns: 1fr 340px;
  gap: 20px;
  align-items: start;
}

.ad__col-main,
.ad__col-side {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.ad__card-title {
  display: flex;
  align-items: center;
  gap: 8px;
}

.ad__timeline {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
  position: relative;
}

.ad__timeline::before {
  content: '';
  position: absolute;
  top: 8px;
  bottom: 8px;
  left: 19px;
  width: 2px;
  background: var(--color-border-default);
}

.ad__timeline-item {
  display: flex;
  gap: 14px;
  position: relative;
}

.ad__timeline-badge {
  display: grid;
  place-items: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  flex-shrink: 0;
  border: 2px solid var(--color-surface-1);
  z-index: 1;
}

.ad__timeline-badge--usuario {
  background: var(--color-info-soft);
  color: var(--color-info);
  border-color: var(--color-info-border);
}

.ad__timeline-badge--curso {
  background: var(--color-primary-soft);
  color: var(--color-primary);
  border-color: var(--color-primary-border);
}

.ad__timeline-badge--inscripcion,
.ad__timeline-badge--inscripcion_estudiante {
  background: var(--color-success-soft);
  color: var(--color-success);
  border-color: var(--color-success-border);
}

.ad__timeline-panel {
  flex: 1;
  background: var(--color-surface-1);
  border: 1px solid var(--color-border-subtle);
  border-radius: var(--radius-md);
  padding: 12px 14px;
}

.ad__timeline-head {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  gap: 8px;
  margin-bottom: 4px;
}

.ad__timeline-head h4 {
  margin: 0;
  font-size: 0.9rem;
  font-weight: 700;
  color: var(--color-text-primary);
}

.ad__timeline-time {
  font-size: 0.72rem;
  color: var(--color-text-muted);
  white-space: nowrap;
}

.ad__timeline-panel p {
  margin: 0;
  font-size: 0.85rem;
  color: var(--color-text-secondary);
  line-height: 1.5;
}

.ad__info-msg {
  margin: 0 0 12px;
  font-size: 0.9rem;
  color: var(--color-text-secondary);
  line-height: 1.5;
}

.ad__info-tip {
  display: flex;
  gap: 10px;
  padding: 12px;
  background: var(--color-warning-soft);
  border: 1px solid var(--color-warning-border);
  border-radius: var(--radius-md);
  color: var(--color-warning);
}

.ad__info-tip p {
  margin: 0;
  font-size: 0.85rem;
  color: var(--color-text-primary);
  line-height: 1.5;
}

.ad__shortcuts {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.ad__shortcut {
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

.ad__shortcut:hover {
  background: var(--color-role-admin-soft);
  border-color: var(--color-role-admin-border);
  color: var(--color-role-admin);
  transform: translateX(2px);
}

.ad__shortcut :last-child {
  margin-left: auto;
}

@media (max-width: 1024px) {
  .ad__grid { grid-template-columns: 1fr; }
}

@media (max-width: 640px) {
  .ad__hero h1 { font-size: 1.45rem; }
}
</style>
