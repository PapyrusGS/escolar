<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { BarChart3, FileText, FileSpreadsheet } from '@lucide/vue';
import AppShell from './layout/AppShell.vue';
import PageTransition from './layout/PageTransition.vue';
import AppButton from './ui/AppButton.vue';
import AppCard from './ui/AppCard.vue';
import AppSelect from './ui/AppSelect.vue';
import AppSpinner from './ui/AppSpinner.vue';
import AppEmptyState from './ui/AppEmptyState.vue';
import { toast } from '../lib/toast.js';

const props = defineProps({
  user: { type: Object, default: null },
});

const currentUser = ref(props.user);
const tiposReporte = ref({});
const selectedTipo = ref('');
const filtrosActuales = ref([]);
const filterValues = ref({});
const filterOptions = ref({});
const reportData = ref([]);
const reportTitle = ref('');
const loading = ref(false);

const userFullName = computed(() =>
  currentUser.value ? `${currentUser.value.Nombre1} ${currentUser.value.Apellido1}` : 'Usuario'
);
const userRole = computed(() => {
  const r = currentUser.value?.IdRol;
  if (r === 1) return 'Administrador';
  if (r === 2) return 'Docente';
  if (r === 3) return 'Estudiante';
  return 'Usuario';
});

const filtrosAplicados = computed(() => {
  if (!filterValues.value || !Object.keys(filterValues.value).length) return 'Todos los registros';
  const parts = [];
  for (const f of filtrosActuales.value) {
    const v = filterValues.value[f.nombre];
    if (v === '' || v == null) continue;
    const opt = (filterOptions.value[f.nombre] || []).find((o) => String(o.Id ?? o.IdUsuario ?? o.IdCurso) === String(v));
    const label = opt ? formatOption(opt, f.endpoint) : v;
    parts.push(`${f.label}: ${label}`);
  }
  return parts.length ? parts.join(' · ') : 'Todos los registros';
});

const generatedAt = ref(new Date());
const currentDateTime = computed(() =>
  generatedAt.value.toLocaleString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' })
);

const isInfoAlumno = computed(() => selectedTipo.value === 'info_alumno');

const reportDataLength = computed(() => {
  if (isInfoAlumno.value && reportData.value?.materias) return reportData.value.materias.length;
  if (Array.isArray(reportData.value)) return reportData.value.length;
  return 0;
});

const displayRows = computed(() => {
  if (isInfoAlumno.value && reportData.value?.materias) return reportData.value.materias;
  if (Array.isArray(reportData.value)) return reportData.value;
  return [];
});

const columnas = computed(() => {
  if (!displayRows.value.length) return [];
  const keys = Object.keys(displayRows.value[0]);
  const map = {
    Carrera: 'Carrera',
    CodigoMateria: 'Código',
    Materia: 'Materia',
    Pensum: 'Pensum',
    Semestre: 'Semestre',
    Curso: 'Curso',
    Estudiante: 'Estudiante',
    Docente: 'Docente',
    DocenteAsignado: 'Docente',
    Nota: 'Nota',
    CI: 'CI',
    Correo: 'Correo',
    Fecha: 'Fecha',
    Aprobado: 'Aprobado',
    NotaEstado: 'Estado Nota',
  };
  return keys.map((k) => ({ key: k, label: map[k] || k }));
});

onMounted(async () => {
  if (!currentUser.value) {
    const stored = localStorage.getItem('auth_user');
    if (stored) currentUser.value = JSON.parse(stored);
  }
  await loadTiposReporte();
});

const handleLogout = async () => {
  try {
    if (localStorage.getItem('auth_token')) await axios.post('/api/auth/logout');
  } catch {}
  localStorage.clear();
  delete axios.defaults.headers.common.Authorization;
  window.location.href = '/';
};

const loadTiposReporte = async () => {
  try {
    const { data } = await axios.get('/api/reportes/tipos');
    tiposReporte.value = data.data || {};
  } catch (err) {
    console.error(err);
  }
};

const onTipoChange = async () => {
  reportData.value = [];
  reportTitle.value = '';
  filterValues.value = {};
  filtrosActuales.value = [];
  filterOptions.value = {};
  if (!selectedTipo.value) return;
  await loadFiltros();
  await fetchReportData();
};

const loadFiltros = async () => {
  try {
    const { data } = await axios.get('/api/reportes/filtros', { params: { tipoReporte: selectedTipo.value } });
    filtrosActuales.value = data.data || [];
    for (const f of filtrosActuales.value) {
      filterValues.value[f.nombre] = '';
      await loadFilterOptions(f);
    }
  } catch (err) {
    console.error(err);
  }
};

const loadFilterOptions = async (filtro) => {
  try {
    const { data } = await axios.get(`/api/reportes/filter-data/${filtro.endpoint}`);
    filterOptions.value[filtro.nombre] = data.data || [];
  } catch (err) {
    console.error(err);
    filterOptions.value[filtro.nombre] = [];
  }
};

const fetchReportData = async () => {
  if (!selectedTipo.value) return;
  loading.value = true;
  try {
    const params = { tipoReporte: selectedTipo.value, ...filterValues.value };
    const { data } = await axios.get('/api/reportes/dinamico', { params });
    reportData.value = data.data?.datos || [];
    reportTitle.value = data.data?.titulo || '';
  } catch (err) {
    toast.error('Error al generar el reporte');
    reportData.value = [];
  } finally {
    loading.value = false;
  }
};

const formatOption = (opt, endpoint) => {
  if (['docentes', 'estudiantes', 'estudiantes_por_docente'].includes(endpoint)) {
    return `${opt.Nombre1} ${opt.Apellido1}${opt.CI ? ' - CI: ' + opt.CI : ''}`;
  }
  if (['cursos', 'cursos_por_docente'].includes(endpoint)) {
    return `${opt.Nombre || opt.Aula}${opt.Aula ? ' (' + opt.Aula + ')' : ''}`;
  }
  if (endpoint === 'materias') return `${opt.Nombre} (${opt.CodigoMateria})`;
  return opt.Nombre || opt.Aula || '';
};

const getCellValue = (row, col) => {
  const v = row[col.key];
  if (col.key === 'Aprobado') return v ? 'Sí' : 'No';
  if (col.key === 'NotaEstado') return v ? 'Registrada' : 'Sin nota';
  return v ?? '';
};

const printPDF = () => {
  if (!displayRows.value.length) {
    toast.warning('No hay datos para imprimir');
    return;
  }
  generatedAt.value = new Date();
  setTimeout(() => window.print(), 80);
};

const exportExcel = () => {
  if (!displayRows.value.length) {
    toast.warning('No hay datos para exportar');
    return;
  }
  let csv = '\uFEFF';
  csv += columnas.value.map((c) => `"${c.label}"`).join(',') + '\n';
  displayRows.value.forEach((row) => {
    csv +=
      columnas.value
        .map((c) => `"${String(getCellValue(row, c) || '').replace(/"/g, '""')}"`)
        .join(',') + '\n';
  });
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = `reporte_${selectedTipo.value}_${Date.now()}.csv`;
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(url);
  toast.success('Reporte exportado');
};
</script>

<template>
  <AppShell v-if="currentUser" :user="currentUser" page-title="Reportes" @logout="handleLogout">
    <PageTransition>
      <div class="rep">
        <AppCard padding="lg" class="rep__controls no-print">
          <div class="rep__head">
            <div class="rep__head-icon"><BarChart3 :size="22" /></div>
            <div>
              <h2>Panel de reportes</h2>
              <p>Selecciona un reporte para visualizar los datos</p>
            </div>
          </div>

          <div class="rep__form">
        <AppSelect
          v-model="selectedTipo"
          label="Tipo de reporte"
          :options="Object.entries(tiposReporte).map(([k, v]) => ({ Id: k, Nombre: v }))"
          placeholder="Seleccionar reporte"
          required
          @update:modelValue="onTipoChange"
        />

        <AppSelect
          v-for="filtro in filtrosActuales"
          :key="filtro.nombre"
          v-model="filterValues[filtro.nombre]"
          :label="filtro.label"
          :options="(filterOptions[filtro.nombre] || []).map((opt) => ({
            Id: opt.Id || opt.IdUsuario || opt.IdCurso,
            Nombre: formatOption(opt, filtro.endpoint),
          }))"
          placeholder="Todos"
          @update:modelValue="fetchReportData"
        />

        <div class="rep__actions">
          <AppButton variant="primary" :icon="FileText" :disabled="!displayRows.length" @click="printPDF">
            PDF
          </AppButton>
          <AppButton variant="success" :icon="FileSpreadsheet" :disabled="!displayRows.length" @click="exportExcel">
            Excel
          </AppButton>
        </div>
      </div>
    </AppCard>

    <AppSpinner v-if="loading" :fullscreen="true" label="Generando reporte..." />

    <AppCard v-else-if="!selectedTipo" padding="lg" class="rep__placeholder no-print">
      <AppEmptyState
        title="Selecciona un reporte"
        description="Elige un tipo de reporte en el panel superior para comenzar."
      />
    </AppCard>

    <AppCard v-else padding="none" class="rep__canvas">
      <div class="rep__print-head only-print">
        <div class="rep__print-brand">
          <div class="rep__print-logo">SU</div>
          <div>
            <span class="rep__print-badge">SISTEMA UNIVERSITARIO</span>
            <h1>{{ reportTitle }}</h1>
          </div>
        </div>
        <div class="rep__print-meta">
          <div class="rep__print-meta-row"><span>Emitido por</span><strong>{{ userFullName }} · {{ userRole }}</strong></div>
          <div class="rep__print-meta-row"><span>Fecha</span><strong>{{ currentDateTime }}</strong></div>
          <div class="rep__print-meta-row"><span>Filtros</span><strong>{{ filtrosAplicados }}</strong></div>
          <div class="rep__print-meta-row"><span>Registros</span><strong>{{ displayRows.length }}</strong></div>
        </div>
      </div>

      <div class="rep__body">
        <div v-if="isInfoAlumno && reportData.datos_personales" class="rep__info-section">
          <h3>Datos personales del alumno</h3>
          <table class="rep__info-table">
            <tbody>
              <tr><td><strong>Nombre completo</strong></td><td>{{ reportData.datos_personales.Nombre1 }} {{ reportData.datos_personales.Nombre2 }} {{ reportData.datos_personales.Apellido1 }} {{ reportData.datos_personales.Apellido2 }}</td></tr>
              <tr><td><strong>CI</strong></td><td>{{ reportData.datos_personales.CI }}</td></tr>
              <tr><td><strong>Teléfono</strong></td><td>{{ reportData.datos_personales.Telefono }}</td></tr>
              <tr><td><strong>Correo</strong></td><td>{{ reportData.datos_personales.Correo }}</td></tr>
              <tr><td><strong>Carrera</strong></td><td>{{ reportData.datos_personales.Carrera || 'Sin asignar' }}</td></tr>
              <tr><td><strong>Modalidad</strong></td><td>{{ reportData.datos_personales.Modalidad || 'Sin asignar' }}</td></tr>
            </tbody>
          </table>
          <h3 class="rep__info-h3">Materias y notas</h3>
        </div>

        <div v-if="reportDataLength === 0" class="rep__empty">
          <AppEmptyState
            title="Sin datos"
            description="No se encontraron registros para este reporte."
          />
        </div>

        <div v-else class="rep__table-wrap">
          <table class="rep__table">
            <thead>
              <tr>
                <th v-for="col in columnas" :key="col.key">{{ col.label }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(row, idx) in displayRows" :key="idx">
                <td v-for="col in columnas" :key="col.key">
                  {{ getCellValue(row, col) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="displayRows.length" class="rep__signature only-print">
          <div class="rep__signature-line">
            <div class="rep__signature-rule"></div>
            <p class="rep__signature-name">{{ userFullName }}</p>
            <p class="rep__signature-role">{{ userRole }} · Sistema Universitario</p>
          </div>
        </div>
      </div>

      <div class="rep__print-foot only-print" aria-hidden="true"></div>
    </AppCard>
      </div>
    </PageTransition>
  </AppShell>
</template>

<style scoped>
.rep {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.rep__head {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 18px;
  padding-bottom: 18px;
  border-bottom: 1px solid var(--color-border-subtle);
}

.rep__head-icon {
  display: grid;
  place-items: center;
  width: 44px;
  height: 44px;
  background: var(--color-primary-soft);
  color: var(--color-primary);
  border-radius: var(--radius-md);
}

.rep__head h2 { margin: 0; font-size: 1.15rem; font-weight: 700; }
.rep__head p { margin: 2px 0 0; font-size: 0.85rem; color: var(--color-text-muted); }

.rep__form {
  display: flex;
  gap: 14px;
  flex-wrap: wrap;
  align-items: flex-end;
}

.rep__form > * {
  flex: 1;
  min-width: 200px;
}

.rep__actions {
  display: flex;
  gap: 8px;
  flex: 0 0 auto !important;
}

.rep__body { padding: 22px; }

.rep__empty { padding: 32px 0; }

.rep__table-wrap {
  overflow-x: auto;
  border-radius: var(--radius-md);
  border: 1px solid var(--color-border-subtle);
}

.rep__table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
  font-size: 0.9rem;
}

.rep__table th {
  background: var(--color-surface-2);
  color: var(--color-primary);
  font-weight: 700;
  padding: 12px 16px;
  border-bottom: 2px solid var(--color-border-default);
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.rep__table td {
  padding: 12px 16px;
  border-bottom: 1px solid var(--color-border-subtle);
  color: var(--color-text-primary);
}

.rep__table tbody tr:last-child td { border-bottom: 0; }
.rep__table tbody tr:hover td { background: var(--color-primary-soft); }

.rep__info-section h3 {
  margin: 0 0 12px;
  color: var(--color-primary);
  font-size: 1rem;
  font-weight: 700;
}

.rep__info-h3 { margin-top: 20px !important; }

.rep__info-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 16px;
  background: var(--color-surface-1);
  border-radius: var(--radius-md);
  overflow: hidden;
  border: 1px solid var(--color-border-subtle);
}

.rep__info-table td {
  padding: 10px 16px;
  border-bottom: 1px solid var(--color-border-subtle);
  font-size: 0.9rem;
}

.rep__info-table tr:last-child td { border-bottom: 0; }

.rep__info-table td:first-child {
  width: 200px;
  font-weight: 600;
  color: var(--color-text-muted);
}

.rep__print-head, .rep__print-foot { display: none; }

@media print {
  @page {
    size: A4 portrait;
    margin: 1.4cm 1.2cm 1.6cm 1.2cm;
    @bottom-left {
      content: 'Documento oficial · Sistema Universitario · Confidencial';
      font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      font-size: 7.5pt;
      color: #64748b;
      border-top: 1px solid #cbd5e1;
      padding-top: 6px;
    }
    @bottom-right {
      content: 'Página ' counter(page) ' de ' counter(pages);
      font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      font-size: 8pt;
      color: #0f172a;
      font-weight: 600;
      border-top: 1px solid #cbd5e1;
      padding-top: 6px;
    }
  }

  html, body {
    background: #fff !important;
    color: #000 !important;
    font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    font-size: 10pt;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  :deep(.no-print),
  :deep(.app-shell__backdrop),
  :deep(.app-sidebar),
  :deep(.app-topbar),
  :deep(.app-toast-stack) {
    display: none !important;
  }

  :deep(.app-shell),
  :deep(.app-shell__main),
  :deep(.app-shell__content) {
    display: block !important;
    background: #fff !important;
    padding: 0 !important;
    margin: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
  }

  :deep(.rep__canvas) {
    border: 0 !important;
    background: #fff !important;
    box-shadow: none !important;
    padding: 0 !important;
    margin: 0 !important;
    border-radius: 0 !important;
  }

  .rep__print-head {
    display: grid !important;
    grid-template-columns: 1fr auto;
    gap: 18px;
    align-items: start;
    border-bottom: 2.5px solid #1e293b;
    padding: 0 0 12px;
    margin-bottom: 16px;
  }

  .rep__print-brand {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .rep__print-logo {
    width: 46px;
    height: 46px;
    border-radius: 10px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff;
    display: grid;
    place-items: center;
    font-weight: 800;
    font-size: 16pt;
    letter-spacing: 0.04em;
    box-shadow: 0 2px 6px rgba(99, 102, 241, 0.25);
    flex-shrink: 0;
  }

  .rep__print-badge {
    display: inline-block;
    font-size: 7.5pt;
    font-weight: 800;
    letter-spacing: 0.18em;
    color: #6366f1;
    text-transform: uppercase;
    margin-bottom: 4px;
  }

  .rep__print-head h1 {
    margin: 0;
    font-size: 18pt;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.15;
    letter-spacing: -0.01em;
  }

  .rep__print-meta {
    display: flex;
    flex-direction: column;
    gap: 4px;
    min-width: 240px;
    border-left: 1px solid #cbd5e1;
    padding-left: 14px;
  }

  .rep__print-meta-row {
    display: grid;
    grid-template-columns: 80px 1fr;
    gap: 8px;
    font-size: 8.5pt;
    align-items: baseline;
  }

  .rep__print-meta-row span {
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-weight: 600;
    font-size: 7pt;
  }

  .rep__print-meta-row strong {
    color: #0f172a;
    font-weight: 700;
    text-align: right;
  }

  .rep__body { padding: 0 !important; }

  .rep__info-section h3 {
    color: #0f172a !important;
    font-size: 11pt;
    border-bottom: 1.5px solid #6366f1;
    padding-bottom: 4px;
    margin: 16px 0 10px !important;
  }

  .rep__info-table {
    border: 1px solid #cbd5e1 !important;
    border-radius: 6px !important;
  }
  .rep__info-table td { color: #0f172a !important; }

  .rep__table-wrap {
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    overflow: visible;
  }

  .rep__table {
    width: 100% !important;
    border-collapse: collapse !important;
    font-size: 9pt !important;
    page-break-inside: auto;
  }

  .rep__table thead { display: table-header-group; }

  .rep__table tr {
    page-break-inside: avoid;
    page-break-after: auto;
  }

  .rep__table th {
    background: #1e293b !important;
    color: #fff !important;
    padding: 8px 10px !important;
    font-size: 8pt !important;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    border: 0 !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  .rep__table td {
    padding: 7px 10px !important;
    border-bottom: 1px solid #e2e8f0 !important;
    color: #0f172a !important;
    background: #fff !important;
  }

  .rep__table tbody tr:nth-child(even) td {
    background: #f8fafc !important;
  }

  .rep__table tbody tr:last-child td { border-bottom: 0 !important; }

  .rep__signature {
    margin-top: 36px;
    display: flex;
    justify-content: flex-end;
  }

  .rep__signature-line {
    text-align: center;
    width: 240px;
  }

  .rep__signature-rule {
    height: 1px;
    background: #0f172a;
    margin-bottom: 6px;
  }

  .rep__signature-name {
    margin: 0;
    font-size: 10pt;
    font-weight: 700;
    color: #0f172a;
  }

  .rep__signature-role {
    margin: 2px 0 0;
    font-size: 8pt;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.04em;
  }

  .rep__print-foot {
    display: none !important;
  }
}
</style>
