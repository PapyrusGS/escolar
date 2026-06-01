<template>
  <div class="reports-container">
    <div class="report-controls no-print">
      <div class="report-header">
        <span class="report-icon">📊</span>
        <div>
          <h2>Panel de Reportes</h2>
          <p class="subtitle">Selecciona un reporte para visualizar los datos</p>
        </div>
      </div>

      <div class="controls-row">
        <div class="control-item">
          <label>Tipo de Reporte</label>
          <select v-model="selectedTipo" @change="onTipoChange">
            <option value="">-- Seleccionar Reporte --</option>
            <option v-for="(label, slug) in tiposReporte" :key="slug" :value="slug">
              {{ label }}
            </option>
          </select>
        </div>

        <template v-for="filtro in filtrosActuales" :key="filtro.nombre">
          <div class="control-item">
            <label>{{ filtro.label }}</label>
            <select v-model="filterValues[filtro.nombre]" @change="fetchReportData">
              <option value="">-- Todos --</option>
              <option v-for="opt in filterOptions[filtro.nombre] || []" :key="opt.Id || opt.IdUsuario || opt.IdCurso" :value="opt.Id || opt.IdUsuario || opt.IdCurso">
                {{ formatOption(opt, filtro.endpoint) }}
              </option>
            </select>
          </div>
        </template>

        <div class="control-actions">
          <button class="action-btn action-btn--pdf" @click="printPDF" :disabled="!reportData.length">
            <span>📄</span> PDF
          </button>
          <button class="action-btn action-btn--excel" @click="exportExcel" :disabled="!reportData.length">
            <span>📊</span> Excel
          </button>
        </div>
      </div>
    </div>

    <div v-if="loading" class="report-loading no-print">
      <div class="mini-spinner"></div>
      <p>Generando reporte...</p>
    </div>

    <div v-else-if="!selectedTipo" class="report-placeholder no-print">
      <p>Selecciona un tipo de reporte para comenzar</p>
    </div>

    <div v-else class="report-canvas">
      <div class="print-official-header only-print">
        <div class="official-logo-box">
          <span class="official-badge">SISTEMA DE GESTIÓN ESCOLAR</span>
          <h1>{{ reportTitle }}</h1>
        </div>
        <div class="official-meta">
          <p><strong>Reporte:</strong> {{ reportTitle }}</p>
          <p><strong>Fecha:</strong> {{ currentDateTime }}</p>
          <p><strong>Emitido Por:</strong> {{ userFullName }}</p>
        </div>
      </div>

      <div v-if="isInfoAlumno && reportData.datos_personales" class="info-alumno-section">
        <h3>Datos Personales del Alumno</h3>
        <table class="info-table">
          <tbody>
            <tr><td><strong>Nombre Completo</strong></td><td>{{ reportData.datos_personales.Nombre1 }} {{ reportData.datos_personales.Nombre2 }} {{ reportData.datos_personales.Apellido1 }} {{ reportData.datos_personales.Apellido2 }}</td></tr>
            <tr><td><strong>CI</strong></td><td>{{ reportData.datos_personales.CI }}</td></tr>
            <tr><td><strong>Teléfono</strong></td><td>{{ reportData.datos_personales.Telefono }}</td></tr>
            <tr><td><strong>Correo</strong></td><td>{{ reportData.datos_personales.Correo }}</td></tr>
            <tr><td><strong>Carrera</strong></td><td>{{ reportData.datos_personales.Carrera || 'Sin asignar' }}</td></tr>
            <tr><td><strong>Modalidad</strong></td><td>{{ reportData.datos_personales.Modalidad || 'Sin asignar' }}</td></tr>
          </tbody>
        </table>
        <h3 style="margin-top:24px;">Materias y Notas</h3>
      </div>

      <div v-if="reportDataLength === 0" class="report-empty">
        <p>No se encontraron registros para este reporte.</p>
      </div>

      <div v-else class="report-table-wrapper">
        <table class="report-table">
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

      <div class="print-official-footer only-print">
        <p>Documento oficial emitido por el Sistema Académico</p>
        <p>Confidencial</p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'ReportesApp',
  props: {
    user: { type: Object, required: true }
  },
  data() {
    const stored = localStorage.getItem('auth_user');
    const localUser = stored ? JSON.parse(stored) : null;
    return {
      currentUser: this.user || localUser,
      tiposReporte: {},
      selectedTipo: '',
      filtrosActuales: [],
      filterValues: {},
      filterOptions: {},
      reportData: [],
      reportTitle: '',
      loading: false,
    };
  },
  computed: {
    userFullName() {
      return this.currentUser ? `${this.currentUser.Nombre1} ${this.currentUser.Apellido1}` : 'Usuario';
    },
    currentDateTime() {
      return new Date().toLocaleString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    },
    isInfoAlumno() {
      return this.selectedTipo === 'info_alumno';
    },
    reportDataLength() {
      if (this.isInfoAlumno && this.reportData?.materias) {
        return this.reportData.materias.length;
      }
      if (Array.isArray(this.reportData)) {
        return this.reportData.length;
      }
      return 0;
    },
    displayRows() {
      if (this.isInfoAlumno && this.reportData?.materias) {
        return this.reportData.materias;
      }
      if (Array.isArray(this.reportData)) {
        return this.reportData;
      }
      return [];
    },
    columnas() {
      if (!this.displayRows.length) return [];
      const keys = Object.keys(this.displayRows[0]);
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
        CodigoMateria: 'Código',
      };
      return keys.map(k => ({ key: k, label: map[k] || k }));
    }
  },
  mounted() {
    if (!this.user) {
      const stored = localStorage.getItem('auth_user');
      if (stored) {
        this.currentUser = JSON.parse(stored);
      }
    }
    this.loadTiposReporte();
  },
  methods: {
    async loadTiposReporte() {
      try {
        const { data } = await axios.get('/api/reportes/tipos');
        this.tiposReporte = data.data || {};
      } catch (e) {
        console.error('Error al cargar tipos de reporte:', e);
      }
    },
    async onTipoChange() {
      this.reportData = [];
      this.reportTitle = '';
      this.filterValues = {};
      this.filtrosActuales = [];
      this.filterOptions = {};
      if (!this.selectedTipo) return;
      await this.loadFiltros();
      await this.fetchReportData();
    },
    async loadFiltros() {
      try {
        const { data } = await axios.get('/api/reportes/filtros', { params: { tipoReporte: this.selectedTipo } });
        this.filtrosActuales = data.data || [];
        for (const f of this.filtrosActuales) {
          this.filterValues[f.nombre] = '';
          await this.loadFilterOptions(f);
        }
      } catch (e) {
        console.error('Error al cargar filtros:', e);
      }
    },
    async loadFilterOptions(filtro) {
      try {
        const { data } = await axios.get(`/api/reportes/filter-data/${filtro.endpoint}`);
        this.filterOptions[filtro.nombre] = data.data || [];
      } catch (e) {
        console.error('Error al cargar opciones:', e);
        this.filterOptions[filtro.nombre] = [];
      }
    },
    async fetchReportData() {
      if (!this.selectedTipo) return;
      this.loading = true;
      try {
        const params = { tipoReporte: this.selectedTipo, ...this.filterValues };
        const { data } = await axios.get('/api/reportes/dinamico', { params });
        this.reportData = data.data?.datos || [];
        this.reportTitle = data.data?.titulo || '';
      } catch (e) {
        console.error('Error al generar reporte:', e);
        this.reportData = [];
      } finally {
        this.loading = false;
      }
    },
    formatOption(opt, endpoint) {
      if (endpoint === 'docentes' || endpoint === 'estudiantes' || endpoint === 'estudiantes_por_docente') {
        return `${opt.Nombre1} ${opt.Apellido1}${opt.CI ? ' - CI: ' + opt.CI : ''}`;
      }
      if (endpoint === 'cursos' || endpoint === 'cursos_por_docente') {
        return `${opt.Nombre || opt.Aula}${opt.Aula ? ' (' + opt.Aula + ')' : ''}`;
      }
      if (endpoint === 'materias') {
        return `${opt.Nombre} (${opt.CodigoMateria})`;
      }
      return opt.Nombre || opt.Aula || '';
    },
    getCellValue(row, col) {
      const val = row[col.key];
      if (col.key === 'Aprobado') {
        return val ? 'Sí' : 'No';
      }
      if (col.key === 'NotaEstado') {
        return val ? 'Registrada' : 'Sin nota';
      }
      return val ?? '';
    },
    printPDF() {
      window.print();
    },
    exportExcel() {
      if (!this.displayRows.length) {
        alert('No hay datos para exportar.');
        return;
      }
      let csv = '\uFEFF';
      csv += this.columnas.map(c => `"${c.label}"`).join(',') + '\n';
      this.displayRows.forEach(row => {
        csv += this.columnas.map(c => `"${String(this.getCellValue(row, c) || '').replace(/"/g, '""')}"`).join(',') + '\n';
      });
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = `reporte_${this.selectedTipo}_${Date.now()}.csv`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }
  }
};
</script>

<style scoped>
.reports-container { background: transparent; color: #eef2ff; }
.report-controls { background: rgba(15, 23, 42, 0.95); border: 1px solid rgba(148, 163, 184, 0.15); border-radius: 24px; padding: 24px; margin-bottom: 24px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); }
.report-header { display: flex; align-items: center; gap: 14px; border-bottom: 1px solid rgba(148, 163, 184, 0.12); padding-bottom: 16px; margin-bottom: 20px; }
.report-icon { font-size: 1.8rem; }
.report-header h2 { margin: 0; font-size: 1.4rem; color: #fbbf24; }
.subtitle { margin: 4px 0 0; font-size: 0.85rem; color: #94a3b8; }
.controls-row { display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end; }
.control-item { display: flex; flex-direction: column; gap: 6px; min-width: 200px; flex: 1; }
.control-item label { font-size: 0.82rem; font-weight: 700; color: #94a3b8; }
.control-item select { background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(148, 163, 184, 0.2); color: #f8fafc; padding: 10px 14px; border-radius: 12px; font-size: 0.88rem; }
.control-item select:focus { outline: none; border-color: #fbbf24; }
.control-actions { display: flex; gap: 12px; align-items: flex-end; padding-bottom: 2px; }
.action-btn { border-radius: 12px; padding: 10px 20px; font-weight: 700; font-size: 0.88rem; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
.action-btn--pdf { background: rgba(251, 191, 36, 0.12); border: 1px solid rgba(251, 191, 36, 0.3); color: #fbbf24; }
.action-btn--pdf:hover:not(:disabled) { background: #fbbf24; color: #0f172a; }
.action-btn--excel { background: rgba(52, 211, 153, 0.12); border: 1px solid rgba(52, 211, 153, 0.3); color: #34d399; }
.action-btn--excel:hover:not(:disabled) { background: #34d399; color: #0f172a; }
.action-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.report-loading { display: flex; flex-direction: column; align-items: center; padding: 60px; background: rgba(15, 23, 42, 0.95); border: 1px solid rgba(148, 163, 184, 0.15); border-radius: 24px; }
.mini-spinner { width: 28px; height: 28px; border: 3px solid rgba(251, 191, 36, 0.1); border-top-color: #fbbf24; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 14px; }
.report-placeholder { text-align: center; padding: 80px 20px; background: rgba(15, 23, 42, 0.95); border-radius: 24px; color: #94a3b8; }
.report-canvas { background: rgba(15, 23, 42, 0.95); border: 1px solid rgba(148, 163, 184, 0.15); border-radius: 24px; padding: 24px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); }
.report-empty { text-align: center; color: #94a3b8; padding: 40px 20px; }
.report-table-wrapper { overflow-x: auto; }
.report-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem; }
.report-table th { background: rgba(30, 41, 59, 0.5); color: #fbbf24; font-weight: 700; padding: 14px 16px; border-bottom: 2px solid rgba(148, 163, 184, 0.15); }
.report-table td { padding: 12px 16px; border-bottom: 1px solid rgba(148, 163, 184, 0.08); color: #e2e8f0; }
.report-table tr:hover td { background: rgba(255, 255, 255, 0.02); }
.info-alumno-section { margin-bottom: 24px; }
.info-alumno-section h3 { color: #fbbf24; margin: 0 0 12px; font-size: 1.1rem; }
.info-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
.info-table td { padding: 8px 16px; border-bottom: 1px solid rgba(148, 163, 184, 0.08); color: #e2e8f0; }
.info-table td:first-child { width: 200px; font-weight: 600; color: #94a3b8; }
.only-print { display: none; }
@keyframes spin { to { transform: rotate(360deg); } }

@media print {
  body { background: #ffffff !important; color: #000000 !important; }
  .no-print { display: none !important; }
  .only-print { display: block !important; }
  .report-canvas { border: none !important; background: transparent !important; padding: 0 !important; box-shadow: none !important; }
  .print-official-header { border-bottom: 2px solid #000; padding-bottom: 12px; margin-bottom: 24px; }
  .official-logo-box h1 { font-size: 1.4rem; margin: 4px 0 0; color: #000; }
  .official-badge { font-size: 0.7rem; border: 1px solid #000; padding: 2px 6px; font-weight: 800; }
  .official-meta { text-align: right; font-size: 0.8rem; color: #333; }
  .official-meta p { margin: 2px 0; }
  .report-table th { background: #f1f5f9 !important; color: #000 !important; border-bottom: 2px solid #000 !important; }
  .report-table td { color: #000 !important; border-bottom: 1px solid #e2e8f0 !important; }
  .info-table td { color: #000 !important; }
  .print-official-footer { margin-top: 40px; border-top: 1px solid #cbd5e1; padding-top: 10px; text-align: center; font-size: 0.75rem; color: #64748b; }
  .print-official-footer p { margin: 2px 0; }
}
</style>
