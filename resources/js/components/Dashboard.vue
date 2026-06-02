<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import {
  ArrowLeft,
  Users,
  GraduationCap,
  BookOpen,
  ClipboardList,
  UserPlus,
  BarChart3,
  Bell,
  Clock,
  Info,
  Lightbulb,
  Activity,
  FileText,
  Calendar,
  Award,
  CheckCircle2,
  Plus,
  Sparkles,
} from '@lucide/vue';
import AppShell from './layout/AppShell.vue';
import PageTransition from './layout/PageTransition.vue';
import AppCard from './ui/AppCard.vue';
import AppButton from './ui/AppButton.vue';
import AppBadge from './ui/AppBadge.vue';
import AppSpinner from './ui/AppSpinner.vue';
import AppRoleBadge from './ui/AppRoleBadge.vue';
import AdminNotificacion from './AdminNotificacion.vue';
import AdminReporte from './AdminReporte.vue';
import ReportesApp from './ReportesApp.vue';
import { toast } from '../lib/toast.js';

const props = defineProps({
  user: { type: Object, default: null },
  pageTitle: { type: String, default: 'Dashboard' },
  unreadNotifications: { type: Number, default: 0 },
});

const user = ref(props.user);
const activeTab = ref('summary');
const loading = ref(false);
const dashboardData = ref({
  rol: '',
  resumen: [],
  actividades: [],
  info_relevante: { mensaje: '', ayuda: '' },
});

const roleMap = {
  1: { label: 'Administrador', className: 'admin' },
  2: { label: 'Docente', className: 'teacher' },
  3: { label: 'Estudiante', className: 'student' },
};
const userRole = computed(() => roleMap[user.value?.IdRol] || { label: 'Usuario', className: 'default' });

const stats = computed(() => dashboardData.value.resumen || []);
const activities = computed(() => dashboardData.value.actividades || []);

const academicCareer = computed(() => {
  const u = user.value;
  if (u?.IdCarrera === 1) return 'Ingeniería de Sistemas';
  if (u?.IdCarrera === 2) return 'Medicina';
  if (u?.IdCarrera === 3) return 'Administración de Empresas';
  return 'Carrera General';
});

const academicModalidad = computed(() => {
  const u = user.value;
  if (u?.IdModalidad === 1) return 'Presencial';
  if (u?.IdModalidad === 2) return 'Semipresencial';
  if (u?.IdModalidad === 3) return 'Virtual';
  return 'Regular';
});

const typeIcon = {
  usuario: Users,
  curso: Calendar,
  inscripcion: GraduationCap,
  inscripcion_estudiante: Plus,
};

const formatDateTime = (d) => {
  if (!d) return '';
  return new Date(d).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
};

onMounted(async () => {
  loading.value = true;
  const token = localStorage.getItem('auth_token');
  if (token) axios.defaults.headers.common.Authorization = `Bearer ${token}`;
  else { window.location.href = '/'; return; }

  if (!user.value) {
    const stored = localStorage.getItem('auth_user');
    if (stored) user.value = JSON.parse(stored);
  }

  try {
    const { data } = await axios.get('/api/dashboard/stats');
    dashboardData.value = data?.data || { rol: '', resumen: [], actividades: [], info_relevante: {} };
  } catch (err) {
    toast.error('No se pudieron cargar las estadísticas');
  } finally {
    loading.value = false;
  }
});

const handleLogout = async () => {
  try {
    if (localStorage.getItem('auth_token')) await axios.post('/api/auth/logout');
  } catch (err) { console.error(err); }
  finally {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('auth_user');
    delete axios.defaults.headers.common.Authorization;
    window.location.href = '/';
  }
};
</script>

<template>
  <AppShell
    v-if="user"
    :user="user"
    :unread-notifications="unreadNotifications"
    :page-title="activeTab === 'summary' ? 'Dashboard' : ''"
    @logout="handleLogout"
  >
    <PageTransition>
      <div class="dash">
        <!-- Header -->
        <header class="dash__header no-print">
          <div>
            <div class="dash__eyebrow">
              <AppRoleBadge :role="userRole.className" :label="userRole.label" />
              <span>RF08 · Panel de usuario</span>
            </div>
            <h1>¡Bienvenido de vuelta, {{ user.Nombre1 }}!</h1>
            <p>Aquí tienes un resumen de tu actividad académica reciente.</p>
          </div>
          <div class="dash__actions">
            <a class="dash__link" href="/index">Volver al panel</a>
            <a class="dash__link dash__link--primary" href="/perfil">Mi perfil</a>
          </div>
        </header>

        <AppSpinner v-if="loading" :fullscreen="true" label="Cargando estadísticas..." />

        <template v-else>
          <div v-if="activeTab !== 'summary'" class="dash__back no-print">
            <AppButton variant="ghost" size="sm" :icon="ArrowLeft" @click="activeTab = 'summary'">
              Volver al resumen
            </AppButton>
          </div>

          <!-- Summary Tab -->
          <div v-if="activeTab === 'summary'" class="dash__summary">
            <!-- Stats grid -->
            <div class="dash__stats">
              <AppStatCard
                v-for="(stat, i) in stats"
                :key="i"
                :label="stat.titulo"
                :value="stat.valor"
                :variant="stat.variant || 'primary'"
                :count-up="typeof stat.valor === 'number'"
              />
            </div>

            <!-- Main grid -->
            <div class="dash__main">
              <!-- Timeline -->
              <AppCard>
                <template #title>
                  <div class="dash__card-title">
                    <Activity :size="18" />
                    <span>Actividades recientes</span>
                  </div>
                </template>
                <template #actions>
                  <AppBadge variant="neutral" size="sm">Historial</AppBadge>
                </template>

                <div v-if="activities.length === 0">
                  <AppEmptyState
                    :icon="Clock"
                    title="Sin actividades"
                    description="No se registran actividades recientes en tu panel."
                  />
                </div>

                <ul v-else class="dash__timeline">
                  <li
                    v-for="(act, index) in activities"
                    :key="index"
                    :class="['dash__timeline-item', `dash__timeline-item--${act.tipo}`]"
                  >
                    <div :class="['dash__timeline-badge', `dash__timeline-badge--${act.tipo}`]">
                      <component :is="typeIcon[act.tipo] || Activity" :size="18" />
                    </div>
                    <div class="dash__timeline-panel">
                      <div class="dash__timeline-head">
                        <h4>{{ act.titulo }}</h4>
                        <span class="dash__timeline-time">{{ formatDateTime(act.fecha) }}</span>
                      </div>
                      <p>{{ act.descripcion }}</p>
                    </div>
                  </li>
                </ul>
              </AppCard>

              <!-- Right column -->
              <div class="dash__sidebar">
                <AppCard>
                  <template #title>
                    <div class="dash__card-title">
                      <Info :size="18" />
                      <span>Información relevante</span>
                    </div>
                  </template>

                  <div class="dash__info">
                    <p class="dash__info-msg">{{ dashboardData.info_relevante?.mensaje }}</p>
                    <div v-if="dashboardData.info_relevante?.ayuda" class="dash__info-tip">
                      <Lightbulb :size="16" />
                      <p>{{ dashboardData.info_relevante.ayuda }}</p>
                    </div>

                    <div v-if="user.IdRol === 3" class="dash__academic">
                      <h4>Ficha académica</h4>
                      <div class="dash__meta-row">
                        <span>Carrera</span>
                        <strong>{{ academicCareer }}</strong>
                      </div>
                      <div class="dash__meta-row">
                        <span>Modalidad</span>
                        <strong>{{ academicModalidad }}</strong>
                      </div>
                    </div>
                  </div>
                </AppCard>

                <AppCard>
                  <template #title>
                    <div class="dash__card-title">
                      <Sparkles :size="18" />
                      <span>Accesos rápidos</span>
                    </div>
                  </template>

                  <div class="dash__shortcuts">
                    <template v-if="user.IdRol === 1">
                      <button class="dash__shortcut dash__shortcut--accent" @click="activeTab = 'notifications'">
                        <Bell :size="18" /> Notificaciones
                      </button>
                      <button class="dash__shortcut" @click="activeTab = 'reports'">
                        <BarChart3 :size="18" /> Reporte de materias
                      </button>
                      <button class="dash__shortcut dash__shortcut--premium" @click="activeTab = 'reports-v2'">
                        <FileText :size="18" /> Reportes del sistema
                      </button>
                      <a href="/usuarios/create" class="dash__shortcut">
                        <UserPlus :size="18" /> Registrar usuario
                      </a>
                      <a href="/usuarios" class="dash__shortcut">
                        <Users :size="18" /> Gestionar usuarios
                      </a>
                      <a href="/cursos" class="dash__shortcut">
                        <GraduationCap :size="18" /> Gestionar cursos
                      </a>
                    </template>
                    <template v-else-if="user.IdRol === 2">
                      <button class="dash__shortcut dash__shortcut--premium" @click="activeTab = 'reports-v2'">
                        <FileText :size="18" /> Reportes
                      </button>
                      <a href="/docente/cursos" class="dash__shortcut">
                        <BookOpen :size="18" /> Mis cursos
                      </a>
                      <a href="/docente/notas" class="dash__shortcut">
                        <ClipboardList :size="18" /> Gestionar notas
                      </a>
                    </template>
                    <template v-else>
                      <button class="dash__shortcut dash__shortcut--premium" @click="activeTab = 'reports-v2'">
                        <FileText :size="18" /> Mis reportes
                      </button>
                      <a href="/cursos/visualizacion" class="dash__shortcut">
                        <BookOpen :size="18" /> Ver cursos
                      </a>
                      <a href="/perfil" class="dash__shortcut">
                        <Award :size="18" /> Cambiar contraseña
                      </a>
                    </template>
                  </div>
                </AppCard>
              </div>
            </div>
          </div>

          <!-- Notifications tab -->
          <div v-else-if="activeTab === 'notifications'">
            <AdminNotificacion :user-role="user.IdRol" />
          </div>

          <!-- Reports tab -->
          <div v-else-if="activeTab === 'reports'">
            <AdminReporte :user="user" />
          </div>

          <!-- Reports v2 tab -->
          <div v-else-if="activeTab === 'reports-v2'">
            <ReportesApp :user="user" />
          </div>
        </template>
      </div>
    </PageTransition>
  </AppShell>
</template>

<style scoped>
.dash {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.dash__header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

.dash__eyebrow {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 6px;
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.18em;
  color: var(--color-text-muted);
  font-weight: 600;
}

.dash__header h1 {
  margin: 0;
  font-size: 1.7rem;
  font-weight: 800;
  letter-spacing: -0.02em;
}

.dash__header p {
  margin: 6px 0 0;
  color: var(--color-text-secondary);
  font-size: 0.95rem;
}

.dash__actions {
  display: flex;
  gap: 10px;
}

.dash__link {
  display: inline-flex;
  align-items: center;
  padding: 10px 18px;
  background: var(--color-surface-2);
  border: 1px solid var(--color-border-default);
  color: var(--color-text-secondary);
  border-radius: var(--radius-md);
  font-weight: 600;
  font-size: 0.85rem;
  text-decoration: none;
  transition: all var(--duration-fast) var(--ease-out);
  min-height: 44px;
}

.dash__link:hover {
  background: var(--color-surface-3);
  color: var(--color-text-primary);
}

.dash__link--primary {
  background: var(--color-primary);
  color: white;
  border-color: transparent;
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.dash__link--primary:hover {
  background: var(--color-primary-hover);
  color: white;
  transform: translateY(-1px);
}

.dash__back {
  display: flex;
  justify-content: flex-start;
}

.dash__stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 16px;
}

.dash__main {
  display: grid;
  grid-template-columns: 1fr 340px;
  gap: 20px;
  align-items: start;
}

.dash__card-title {
  display: flex;
  align-items: center;
  gap: 8px;
}

.dash__timeline {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 14px;
  position: relative;
}

.dash__timeline::before {
  content: '';
  position: absolute;
  top: 8px;
  bottom: 8px;
  left: 19px;
  width: 2px;
  background: var(--color-border-default);
}

.dash__timeline-item {
  display: flex;
  gap: 14px;
  position: relative;
}

.dash__timeline-badge {
  display: grid;
  place-items: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  flex-shrink: 0;
  border: 2px solid var(--color-surface-1);
  z-index: 1;
}

.dash__timeline-badge--usuario {
  background: var(--color-info-soft);
  color: var(--color-info);
  border-color: var(--color-info-border);
}

.dash__timeline-badge--curso {
  background: var(--color-primary-soft);
  color: var(--color-primary);
  border-color: var(--color-primary-border);
}

.dash__timeline-badge--inscripcion,
.dash__timeline-badge--inscripcion_estudiante {
  background: var(--color-success-soft);
  color: var(--color-success);
  border-color: var(--color-success-border);
}

.dash__timeline-panel {
  flex: 1;
  background: var(--color-surface-1);
  border: 1px solid var(--color-border-subtle);
  border-radius: var(--radius-md);
  padding: 12px 14px;
}

.dash__timeline-head {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  gap: 8px;
  margin-bottom: 4px;
}

.dash__timeline-head h4 {
  margin: 0;
  font-size: 0.92rem;
  font-weight: 700;
  color: var(--color-text-primary);
}

.dash__timeline-time {
  font-size: 0.72rem;
  color: var(--color-text-muted);
  white-space: nowrap;
}

.dash__timeline-panel p {
  margin: 0;
  font-size: 0.85rem;
  color: var(--color-text-secondary);
  line-height: 1.5;
}

.dash__sidebar {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.dash__info-msg {
  margin: 0 0 12px;
  font-size: 0.9rem;
  color: var(--color-text-secondary);
  line-height: 1.5;
}

.dash__info-tip {
  display: flex;
  gap: 10px;
  padding: 12px;
  background: var(--color-warning-soft);
  border: 1px solid var(--color-warning-border);
  border-radius: var(--radius-md);
  color: var(--color-warning);
}

.dash__info-tip p {
  margin: 0;
  font-size: 0.85rem;
  color: var(--color-text-primary);
  line-height: 1.5;
}

.dash__academic {
  margin-top: 20px;
  padding-top: 16px;
  border-top: 1px solid var(--color-border-subtle);
}

.dash__academic h4 {
  margin: 0 0 10px;
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--color-text-muted);
  font-weight: 700;
}

.dash__meta-row {
  display: flex;
  justify-content: space-between;
  font-size: 0.88rem;
  padding: 6px 0;
}

.dash__meta-row span {
  color: var(--color-text-secondary);
}

.dash__meta-row strong {
  color: var(--color-text-primary);
  font-weight: 700;
}

.dash__shortcuts {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.dash__shortcut {
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
  cursor: pointer;
  transition: all var(--duration-fast) var(--ease-out);
  text-align: left;
  min-height: 44px;
}

.dash__shortcut:hover {
  background: var(--color-primary-soft);
  border-color: var(--color-primary-border);
  color: var(--color-primary);
  transform: translateX(2px);
}

.dash__shortcut--accent {
  background: var(--color-primary-soft);
  border-color: var(--color-primary-border);
  color: var(--color-primary);
}

.dash__shortcut--premium {
  background: var(--color-role-student-soft);
  border-color: var(--color-role-student-border);
  color: var(--color-role-student);
}

.dash__shortcut--premium:hover {
  background: var(--color-role-student);
  color: white;
}

@media (max-width: 1024px) {
  .dash__main {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .dash__header {
    flex-direction: column;
    align-items: stretch;
  }
}
</style>
