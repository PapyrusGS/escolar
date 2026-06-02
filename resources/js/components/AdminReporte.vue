<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { FileText, FileSpreadsheet, BarChart3 } from '@lucide/vue';
import AppCard from './ui/AppCard.vue';
import AppSelect from './ui/AppSelect.vue';
import AppButton from './ui/AppButton.vue';
import AppSpinner from './ui/AppSpinner.vue';
import AppEmptyState from './ui/AppEmptyState.vue';
import { toast } from '../lib/toast.js';

const props = defineProps({
  user: { type: Object, required: true },
});

const carreras = ref([]);
const selectedCarrera = ref('');
const reportData = ref([]);
const reportTitle = ref('Cargando...');
const loading = ref(false);

const userFullName = () => props.user ? `${props.user.Nombre1} ${props.user.Apellido1}` : 'Administrador';
const currentDateTime = () =>
  new Date().toLocaleString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });

onMounted(async () => {
  await loadCarreras();
  await fetchReportData();
});

const loadCarreras = async () => {
  try {
    const { data } = await axios.get('/api/cursos-materias/form-data');
    carreras.value = data.data?.carreras || [];
  } catch (err) {
    console.error(err);
  }
};

const fetchReportData = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/reportes/materias-carrera', {
      params: { IdCarrera: selectedCarrera.value },
    });
    reportData.value = data.data?.datos || [];
    reportTitle.value = data.data?.titulo || 'Reporte de Materias';
  } catch (err) {
    toast.error('No se pudo generar el reporte');
  } finally {
    loading.value = false;
  }
};

const printPDF = () => {
  if (reportData.value.length === 0) {
    toast.warning('No hay datos para imprimir');
    return;
  }
  window.print();
};

const exportExcel = () => {
  if (reportData.value.length === 0) {
    toast.warning('No hay datos para exportar');
    return;
  }
  let csv = '\uFEFF';
  csv += 'Carrera,Código Materia,Nombre Materia,Pensum,Semestre\n';
  reportData.value.forEach((row) => {
    const esc = (v) => `"${String(v ?? '').replace(/"/g, '""')}"`;
    csv += `${esc(row.Carrera)},${esc(row.CodigoMateria)},${esc(row.Materia)},${esc(row.Pensum)},${row.Semestre}\n`;
  });
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = `reporte_materias_${Date.now()}.csv`;
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(url);
  toast.success('Reporte exportado');
};
</script>

<template>
  <div class="report">
    <AppCard padding="lg" class="report__controls no-print">
      <div class="report__head">
        <div class="report__head-icon"><BarChart3 :size="22" /></div>
        <div>
          <h2>Reporte académico</h2>
          <p>Estrategia: {{ reportTitle }}</p>
        </div>
      </div>

      <div class="report__form">
        <AppSelect
          v-model="selectedCarrera"
          label="Filtrar carrera"
          :options="carreras"
          placeholder="Todas las carreras"
          value-key="IdCarrera"
          label-key="Nombre"
          @update:modelValue="fetchReportData"
        />
        <div class="report__actions">
          <AppButton variant="primary" :icon="FileText" @click="printPDF">Imprimir PDF</AppButton>
          <AppButton variant="success" :icon="FileSpreadsheet" @click="exportExcel">Exportar Excel</AppButton>
        </div>
      </div>
    </AppCard>

    <AppSpinner v-if="loading" :fullscreen="true" label="Generando reporte..." />

    <AppCard v-else padding="none" class="report__canvas">
      <!-- Print header -->
      <div class="report__print-head only-print">
        <div>
          <span class="report__print-badge">SISTEMA DE GESTIÓN ESCOLAR</span>
          <h1>{{ reportTitle }}</h1>
        </div>
        <div class="report__print-meta">
          <p><strong>Reporte:</strong> {{ reportTitle }}</p>
          <p><strong>Fecha:</strong> {{ currentDateTime() }}</p>
          <p><strong>Emitido por:</strong> {{ userFullName() }}</p>
        </div>
      </div>

      <div class="report__body">
        <div v-if="reportData.length === 0" class="report__empty">
          <AppEmptyState
            title="Sin datos"
            description="No se encontraron registros para este reporte."
          />
        </div>

        <div v-else class="report__table-wrap">
          <table class="report__table">
            <thead>
              <tr>
                <th style="width: 25%">Carrera</th>
                <th style="width: 15%">Código</th>
                <th style="width: 35%">Materia</th>
                <th style="width: 15%">Pensum</th>
                <th style="width: 10%" class="report__center">Semestre</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(row, idx) in reportData" :key="idx">
                <td><strong>{{ row.Carrera }}</strong></td>
                <td><span class="report__code">{{ row.CodigoMateria }}</span></td>
                <td>{{ row.Materia }}</td>
                <td>{{ row.Pensum }}</td>
                <td class="report__center report__bold">{{ row.Semestre }}°</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="report__print-foot only-print">
        <p>Documento oficial emitido por el Sistema Académico · Confidencial</p>
      </div>
    </AppCard>
  </div>
</template>

<style scoped>
.report {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.report__head {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 18px;
  padding-bottom: 18px;
  border-bottom: 1px solid var(--color-border-subtle);
}

.report__head-icon {
  display: grid;
  place-items: center;
  width: 44px;
  height: 44px;
  background: var(--color-primary-soft);
  color: var(--color-primary);
  border-radius: var(--radius-md);
}

.report__head h2 {
  margin: 0;
  font-size: 1.15rem;
  font-weight: 700;
}

.report__head p {
  margin: 2px 0 0;
  font-size: 0.85rem;
  color: var(--color-text-muted);
}

.report__form {
  display: flex;
  align-items: flex-end;
  gap: 16px;
  flex-wrap: wrap;
}

.report__form > :first-child {
  flex: 1;
  min-width: 220px;
}

.report__actions {
  display: flex;
  gap: 8px;
}

.report__body {
  padding: 22px;
}

.report__empty {
  padding: 32px 0;
}

.report__table-wrap {
  overflow-x: auto;
  border-radius: var(--radius-md);
  border: 1px solid var(--color-border-subtle);
}

.report__table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
  font-size: 0.9rem;
}

.report__table th {
  background: var(--color-surface-2);
  color: var(--color-primary);
  font-weight: 700;
  padding: 12px 16px;
  border-bottom: 2px solid var(--color-border-default);
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.report__table td {
  padding: 12px 16px;
  border-bottom: 1px solid var(--color-border-subtle);
  color: var(--color-text-primary);
}

.report__table tbody tr:last-child td {
  border-bottom: 0;
}

.report__table tbody tr:hover td {
  background: var(--color-primary-soft);
}

.report__code {
  display: inline-block;
  padding: 3px 8px;
  background: var(--color-info-soft);
  color: var(--color-info);
  border: 1px solid var(--color-info-border);
  border-radius: var(--radius-xs);
  font-family: var(--font-mono);
  font-weight: 600;
  font-size: 0.8rem;
}

.report__center { text-align: center; }
.report__bold { font-weight: 700; }

.report__print-head,
.report__print-foot {
  display: none;
}

@media print {
  .report__canvas {
    border: 0 !important;
    background: white !important;
    box-shadow: none !important;
  }
  .report__print-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    border-bottom: 2px solid black;
    padding: 0 22px 14px;
  }
  .report__print-foot {
    display: block;
    border-top: 1px solid #cbd5e1;
    padding: 14px 22px;
    text-align: center;
    font-size: 0.75rem;
    color: #64748b;
  }
  .report__table th {
    background: #f1f5f9 !important;
    color: black !important;
  }
  .report__table td {
    color: black !important;
  }
  .report__code {
    background: transparent !important;
    color: black !important;
    border: 0 !important;
    font-family: inherit !important;
  }
}
</style>
