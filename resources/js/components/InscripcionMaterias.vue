<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import {
  ArrowLeft,
  Search,
  BookOpen,
  User as UserIcon,
  Hash,
  MapPin,
  Clock,
  Calendar,
  Sparkles,
  CheckCircle2,
  XCircle,
  RefreshCw,
  UserCheck,
  Plus,
  AlertTriangle,
  GraduationCap,
} from '@lucide/vue';
import AppShell from './layout/AppShell.vue';
import PageTransition from './layout/PageTransition.vue';
import AppCard from './ui/AppCard.vue';
import AppButton from './ui/AppButton.vue';
import AppInput from './ui/AppInput.vue';
import AppPageHeader from './ui/AppPageHeader.vue';
import AppBadge from './ui/AppBadge.vue';
import AppSpinner from './ui/AppSpinner.vue';
import AppEmptyState from './ui/AppEmptyState.vue';
import AppModal from './ui/AppModal.vue';
import AppAlert from './ui/AppAlert.vue';
import { toast } from '../lib/toast.js';

const user = ref(null);
const materias = ref([]);
const loading = ref(false);
const submitting = ref(false);
const searchQuery = ref('');
const showConfirm = ref(false);
const selectedMateria = ref(null);
const lastResult = ref(null);
const showResult = ref(false);

const filteredMaterias = computed(() => {
  const q = searchQuery.value.toLowerCase().trim();
  if (!q) return materias.value;
  return materias.value.filter((m) =>
    (m.Materia || '').toLowerCase().includes(q) ||
    (m.CodigoMateria || '').toLowerCase().includes(q) ||
    (m.Curso || '').toLowerCase().includes(q) ||
    (m.Aula || '').toLowerCase().includes(q)
  );
});

onMounted(async () => {
  const token = localStorage.getItem('auth_token');
  if (!token) {
    window.location.href = '/';
    return;
  }
  axios.defaults.headers.common.Authorization = `Bearer ${token}`;
  const stored = localStorage.getItem('auth_user');
  if (stored) user.value = JSON.parse(stored);
  if (user.value && Number(user.value.IdRol) !== 3) {
    window.location.href = '/dashboard';
    return;
  }
  await loadMaterias();
});

const loadMaterias = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/estudiante/cursos/disponibles');
    if (data?.status) {
      materias.value = data.data || [];
    } else {
      toast.error(data?.message || 'No se pudieron cargar las materias.');
    }
  } catch (err) {
    toast.error(err?.response?.data?.message || 'No se pudieron cargar las materias disponibles.');
  } finally {
    loading.value = false;
  }
};

const openConfirm = (m) => {
  selectedMateria.value = m;
  showConfirm.value = true;
  showResult.value = false;
};

const closeConfirm = () => {
  showConfirm.value = false;
  selectedMateria.value = null;
  lastResult.value = null;
};

const confirmInscribir = async () => {
  if (!selectedMateria.value || submitting.value) return;
  submitting.value = true;
  try {
    const { data } = await axios.post('/api/estudiante/cursos/inscribir', {
      IdCursoMateria: selectedMateria.value.IdCursoMateria,
    });
    lastResult.value = {
      ok: !!data?.status,
      message: data?.message || 'Resultado desconocido',
    };
    if (data?.status) {
      toast.success(data?.message || 'Inscripción realizada con éxito');
      await loadMaterias();
    } else {
      toast.error(data?.message || 'No se pudo completar la inscripción');
    }
    showResult.value = true;
  } catch (err) {
    lastResult.value = {
      ok: false,
      message: err?.response?.data?.message || 'Error al procesar la inscripción',
    };
    showResult.value = true;
    toast.error(lastResult.value.message);
  } finally {
    submitting.value = false;
  }
};

const closeAndContinue = () => {
  closeConfirm();
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
</script>

<template>
  <AppShell v-if="user" :user="user" page-title="Inscripción a materias" @logout="handleLogout">
    <PageTransition>
      <div class="im">
        <AppPageHeader
          title="Inscripción a materias"
          description="Selecciona las materias disponibles según tu modalidad y plan de estudios."
        >
          <template #actions>
            <AppButton variant="secondary" :icon="ArrowLeft" @click="window.location.href = '/dashboard'">
              Volver al Dashboard
            </AppButton>
            <AppButton variant="ghost" :icon="RefreshCw" @click="loadMaterias">Actualizar</AppButton>
          </template>
        </AppPageHeader>

        <AppCard padding="md">
          <div class="im__filters">
            <AppInput
              v-model="searchQuery"
              type="text"
              placeholder="Buscar por materia, código o aula..."
              :icon="Search"
            />
            <div class="im__counter">
              <GraduationCap :size="16" />
              <span><strong>{{ filteredMaterias.length }}</strong> materia(s) disponible(s)</span>
            </div>
          </div>
        </AppCard>

        <AppSpinner v-if="loading" :fullscreen="true" label="Cargando materias disponibles..." />

        <AppEmptyState
          v-else-if="filteredMaterias.length === 0"
          :icon="Sparkles"
          title="No hay materias disponibles para inscripción"
          description="Verifica tu modalidad académica o contacta al administrador para revisar tu plan de estudios."
        />

        <div v-else class="im__grid">
          <AppCard
            v-for="m in filteredMaterias"
            :key="m.IdCursoMateria"
            padding="none"
            class="im__card"
          >
            <header class="im__card-head">
              <div>
                <span class="im__card-code"><Hash :size="11" /> {{ m.CodigoMateria || m.Materia?.substring(0, 6) }}</span>
                <h3>{{ m.Materia }}</h3>
              </div>
              <AppBadge variant="primary" size="sm">Disponible</AppBadge>
            </header>
            <div class="im__card-body">
              <div class="im__info"><MapPin :size="14" /><span><strong>Aula:</strong> {{ m.Curso || '—' }}</span></div>
              <div class="im__info"><Clock :size="14" /><span><strong>Turno:</strong> {{ m.Turno || '—' }}</span></div>
              <div class="im__info"><Calendar :size="14" /><span><strong>Vigencia:</strong> {{ formatDate(m.FechaInicio) }} al {{ formatDate(m.FechaFin) }}</span></div>
            </div>
            <footer class="im__card-foot">
              <AppButton variant="primary" :icon="Plus" block @click="openConfirm(m)">
                Inscribirme
              </AppButton>
            </footer>
          </AppCard>
        </div>
      </div>

      <AppModal :open="showConfirm" size="md" @close="closeAndContinue">
        <template #header>
          <h2 class="im__modal-title">
            <template v-if="!showResult">Confirmar inscripción</template>
            <template v-else>Resultado de la inscripción</template>
          </h2>
        </template>

        <div v-if="!showResult && selectedMateria" class="im__confirm">
          <AppAlert variant="info" title="Verifica antes de inscribirte">
            Una vez confirmada, la inscripción será registrada en tu historial académico.
          </AppAlert>
          <div class="im__confirm-card">
            <div class="im__confirm-icon"><BookOpen :size="22" /></div>
            <div>
              <h4>{{ selectedMateria.Materia }}</h4>
              <p><strong>Aula:</strong> {{ selectedMateria.Curso || '—' }}</p>
              <p><strong>Turno:</strong> {{ selectedMateria.Turno || '—' }}</p>
              <p><strong>Vigencia:</strong> {{ formatDate(selectedMateria.FechaInicio) }} – {{ formatDate(selectedMateria.FechaFin) }}</p>
            </div>
          </div>
        </div>

        <div v-else-if="lastResult" class="im__result">
          <div :class="['im__result-icon', lastResult.ok ? 'im__result-icon--ok' : 'im__result-icon--bad']">
            <component :is="lastResult.ok ? CheckCircle2 : AlertTriangle" :size="48" />
          </div>
          <h3>{{ lastResult.ok ? '¡Inscripción exitosa!' : 'No se pudo inscribir' }}</h3>
          <p>{{ lastResult.message }}</p>
        </div>

        <template #footer>
          <template v-if="!showResult">
            <AppButton variant="secondary" :disabled="submitting" @click="closeAndContinue">Cancelar</AppButton>
            <AppButton variant="primary" :icon="UserCheck" :loading="submitting" @click="confirmInscribir">
              Confirmar inscripción
            </AppButton>
          </template>
          <template v-else>
            <AppButton variant="primary" @click="closeAndContinue">Aceptar</AppButton>
          </template>
        </template>
      </AppModal>
    </PageTransition>
  </AppShell>
</template>

<style scoped>
.im {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.im__filters {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 16px;
  align-items: center;
}

.im__counter {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 14px;
  background: var(--color-primary-soft);
  border: 1px solid var(--color-primary-border);
  border-radius: var(--radius-md);
  font-size: 0.85rem;
  color: var(--color-primary);
  font-weight: 600;
  white-space: nowrap;
}

.im__grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 16px;
}

.im__card {
  display: flex;
  flex-direction: column;
  transition: transform var(--duration-fast) var(--ease-out),
    border-color var(--duration-fast) var(--ease-out);
}

.im__card:hover {
  transform: translateY(-2px);
  border-color: var(--color-primary-border);
}

.im__card-head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 10px;
  padding: 18px 20px;
  background: rgba(28, 39, 66, 0.3);
  border-bottom: 1px solid var(--color-border-subtle);
}

.im__card-code {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 0.72rem;
  font-weight: 700;
  color: var(--color-primary);
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
  text-transform: uppercase;
}

.im__card-head h3 {
  margin: 4px 0 0;
  font-size: 1rem;
  color: var(--color-text-primary);
  line-height: 1.3;
  font-weight: 700;
}

.im__card-body {
  padding: 16px 20px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  flex: 1;
}

.im__info {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  font-size: 0.85rem;
  color: var(--color-text-secondary);
  line-height: 1.4;
}

.im__info strong {
  color: var(--color-text-primary);
  font-weight: 700;
  margin-right: 4px;
}

.im__card-foot {
  padding: 14px 20px 18px;
  border-top: 1px solid var(--color-border-subtle);
}

.im__modal-title {
  margin: 0;
  font-size: 1.15rem;
  font-weight: 700;
  color: var(--color-text-primary);
}

.im__confirm {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.im__confirm-card {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding: 16px;
  background: var(--color-surface-1);
  border: 1px solid var(--color-border-subtle);
  border-radius: var(--radius-md);
}

.im__confirm-icon {
  display: grid;
  place-items: center;
  width: 48px;
  height: 48px;
  background: var(--color-primary-soft);
  color: var(--color-primary);
  border-radius: var(--radius-md);
  flex-shrink: 0;
}

.im__confirm-card h4 {
  margin: 0 0 6px;
  font-size: 1.05rem;
  color: var(--color-text-primary);
  font-weight: 700;
}

.im__confirm-card p {
  margin: 0 0 4px;
  font-size: 0.88rem;
  color: var(--color-text-secondary);
}

.im__result {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 24px;
  text-align: center;
}

.im__result-icon {
  display: grid;
  place-items: center;
  width: 80px;
  height: 80px;
  border-radius: 50%;
}

.im__result-icon--ok {
  background: var(--color-success-soft);
  color: var(--color-success);
}

.im__result-icon--bad {
  background: var(--color-danger-soft);
  color: var(--color-danger);
}

.im__result h3 {
  margin: 0;
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--color-text-primary);
}

.im__result p {
  margin: 0;
  font-size: 0.95rem;
  color: var(--color-text-secondary);
  max-width: 420px;
  line-height: 1.5;
}

@media (max-width: 768px) {
  .im__filters { grid-template-columns: 1fr; }
  .im__counter { justify-self: start; }
  .im__grid { grid-template-columns: 1fr; }
}
</style>
