<template>
  <div class="reports-container">
    <!-- Panel del Reporte (no se muestra en impresión de PDF) -->
    <div class="report-controls no-print">
      <div class="report-header">
        <span class="report-icon">📊</span>
        <div>
          <h2>Reporte Académico</h2>
          <p class="subtitle">Estrategia: {{ reportTitle }}</p>
        </div>
      </div>

      <div class="controls-grid">
        <!-- Filtro por Carrera -->
        <div class="control-item">
          <label>Filtrar Carrera:</label>
          <select v-model="selectedCarrera" @change="fetchReportData">
            <option value="">Todas las Carreras</option>
            <option v-for="c in carreras" :key="c.IdCarrera" :value="c.IdCarrera">
              {{ c.Nombre }}
            </option>
          </select>
        </div>

        <!-- Acciones Premium -->
        <div class="control-actions">
          <button class="action-btn action-btn--pdf" @click="printPDF">
            <span>📄</span> Imprimir PDF
          </button>
          <button class="action-btn action-btn--excel" @click="exportExcel">
            <span>💚</span> Exportar Excel
          </button>
        </div>
      </div>
    </div>

    <!-- Estado de Carga -->
    <div v-if="loading" class="report-loading no-print">
      <div class="mini-spinner"></div>
      <p>Consultando la estrategia de reporte...</p>
    </div>

    <!-- Tabla del Reporte (Vista en Pantalla e Impresión Limpia) -->
    <div v-else class="report-canvas">
      <!-- Encabezado Oficial del Reporte (Solo para impresión de PDF) -->
      <div class="print-official-header only-print">
        <div class="official-logo-box">
          <span class="official-badge">GOBIERNO ACADÉMICO</span>
          <h1>SISTEMA DE GESTIÓN ESCOLAR</h1>
        </div>
        <div class="official-meta">
          <p><strong>Reporte:</strong> {{ reportTitle }}</p>
          <p><strong>Fecha Generación:</strong> {{ currentDateTime }}</p>
          <p><strong>Emitido Por:</strong> {{ userFullName }}</p>
        </div>
      </div>

      <div v-if="reportData.length === 0" class="report-empty">
        <p>No se encontraron registros para este reporte.</p>
      </div>

      <div v-else class="report-table-wrapper">
        <table class="report-table">
          <thead>
            <tr>
              <th style="width: 25%">Carrera</th>
              <th style="width: 15%">Código</th>
              <th style="width: 35%">Materia</th>
              <th style="width: 15%">Pensum</th>
              <th style="width: 10%" class="text-center">Semestre</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(row, idx) in reportData" :key="idx">
              <td>
                <strong>{{ row.Carrera }}</strong>
              </td>
              <td>
                <span class="code-badge">{{ row.CodigoMateria }}</span>
              </td>
              <td>{{ row.Materia }}</td>
              <td>{{ row.Pensum }}</td>
              <td class="text-center font-bold">{{ row.Semestre }}°</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pie de página Oficial para el PDF impreso -->
      <div class="print-official-footer only-print">
        <p>Este documento es un reporte oficial emitido por el Sistema Académico.</p>
        <p>Página 1 de 1 • Confidencial</p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminReporte',
  props: {
    user: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      carreras: [],
      selectedCarrera: '',
      reportData: [],
      reportTitle: 'Cargando...',
      loading: false,
    };
  },
  computed: {
    userFullName() {
      if (!this.user) return 'Administrador';
      return `${this.user.Nombre1} ${this.user.Apellido1}`;
    },
    currentDateTime() {
      const now = new Date();
      return now.toLocaleString('es-ES', { 
        day: '2-digit', 
        month: '2-digit', 
        year: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit' 
      });
    }
  },
  mounted() {
    this.loadCarreras();
    this.fetchReportData();
  },
  methods: {
    async loadCarreras() {
      try {
        const { data } = await axios.get('/api/cursos-materias/form-data');
        this.carreras = data.data?.carreras || [];
      } catch (error) {
        console.error('Error al cargar carreras:', error);
      }
    },
    async fetchReportData() {
      this.loading = true;
      try {
        const { data } = await axios.get('/api/reportes/materias-carrera', {
          params: { IdCarrera: this.selectedCarrera }
        });
        this.reportData = data.data?.datos || [];
        this.reportTitle = data.data?.titulo || 'Reporte de Materias';
      } catch (error) {
        console.error('Error al generar reporte:', error);
      } finally {
        this.loading = false;
      }
    },
    printPDF() {
      window.print();
    },
    exportExcel() {
      if (this.reportData.length === 0) {
        alert('No hay datos para exportar.');
        return;
      }

      // Generamos un CSV compatible con Excel agregando el BOM UTF-8 (\uFEFF)
      let csvContent = '\uFEFF';
      csvContent += 'Carrera,Código Materia,Nombre Materia,Pensum,Semestre\n';

      this.reportData.forEach(row => {
        // Escapar comillas dobles y comas
        const carrera = `"${row.Carrera.replace(/"/g, '""')}"`;
        const codigo = `"${row.CodigoMateria.replace(/"/g, '""')}"`;
        const materia = `"${row.Materia.replace(/"/g, '""')}"`;
        const pensum = `"${row.Pensum.replace(/"/g, '""')}"`;
        const semestre = row.Semestre;

        csvContent += `${carrera},${codigo},${materia},${pensum},${semestre}\n`;
      });

      const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.setAttribute('href', url);
      link.setAttribute('download', `reporte_materias_carrera_${Date.now()}.csv`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }
  }
};
</script>

<style scoped>
.reports-container {
  background: transparent;
  color: #eef2ff;
}

.report-controls {
  background: rgba(15, 23, 42, 0.95);
  border: 1px solid rgba(148, 163, 184, 0.15);
  border-radius: 24px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.report-header {
  display: flex;
  align-items: center;
  gap: 14px;
  border-bottom: 1px solid rgba(148, 163, 184, 0.12);
  padding-bottom: 16px;
  margin-bottom: 20px;
}

.report-icon {
  font-size: 1.8rem;
}

.report-header h2 {
  margin: 0;
  font-size: 1.4rem;
  color: #fbbf24;
}

.subtitle {
  margin: 4px 0 0;
  font-size: 0.85rem;
  color: #94a3b8;
  font-weight: 600;
}

.controls-grid {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  flex-wrap: wrap;
  gap: 16px;
}

.control-item {
  display: flex;
  flex-direction: column;
  gap: 6px;
  width: min(100%, 300px);
}

.control-item label {
  font-size: 0.82rem;
  font-weight: 700;
  color: #94a3b8;
}

.control-item select {
  background: rgba(15, 23, 42, 0.7);
  border: 1px solid rgba(148, 163, 184, 0.2);
  color: #f8fafc;
  padding: 10px 14px;
  border-radius: 12px;
  font-size: 0.88rem;
}

.control-item select:focus {
  outline: none;
  border-color: #fbbf24;
}

.control-actions {
  display: flex;
  gap: 12px;
}

.action-btn {
  border-radius: 12px;
  padding: 10px 20px;
  font-weight: 700;
  font-size: 0.88rem;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s;
}

.action-btn--pdf {
  background: rgba(251, 191, 36, 0.12);
  border: 1px solid rgba(251, 191, 36, 0.3);
  color: #fbbf24;
}

.action-btn--pdf:hover {
  background: #fbbf24;
  color: #0f172a;
}

.action-btn--excel {
  background: rgba(52, 211, 153, 0.12);
  border: 1px solid rgba(52, 211, 153, 0.3);
  color: #34d399;
}

.action-btn--excel:hover {
  background: #34d399;
  color: #0f172a;
}

/* Loading State */
.report-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 60px;
  background: rgba(15, 23, 42, 0.95);
  border: 1px solid rgba(148, 163, 184, 0.15);
  border-radius: 24px;
}

.mini-spinner {
  width: 28px;
  height: 28px;
  border: 3px solid rgba(251, 191, 36, 0.1);
  border-top-color: #fbbf24;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 14px;
}

/* Canvas y Tabla del Reporte */
.report-canvas {
  background: rgba(15, 23, 42, 0.95);
  border: 1px solid rgba(148, 163, 184, 0.15);
  border-radius: 24px;
  padding: 24px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.report-empty {
  text-align: center;
  color: #94a3b8;
  padding: 40px 20px;
}

.report-table-wrapper {
  overflow-x: auto;
}

.report-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
  font-size: 0.9rem;
}

.report-table th {
  background: rgba(30, 41, 59, 0.5);
  color: #fbbf24;
  font-weight: 700;
  padding: 14px 16px;
  border-bottom: 2px solid rgba(148, 163, 184, 0.15);
}

.report-table td {
  padding: 12px 16px;
  border-bottom: 1px solid rgba(148, 163, 184, 0.08);
  color: #e2e8f0;
}

.report-table tr:hover td {
  background: rgba(255, 255, 255, 0.02);
}

.code-badge {
  background: rgba(56, 189, 248, 0.12);
  color: #38bdf8;
  border: 1px solid rgba(56, 189, 248, 0.25);
  padding: 3px 8px;
  border-radius: 6px;
  font-family: monospace;
  font-weight: 700;
  font-size: 0.8rem;
}

.text-center {
  text-align: center;
}

.font-bold {
  font-weight: 700;
}

/* Ocultar elementos de impresión en pantalla */
.only-print {
  display: none;
}

/* Estilos de Animación */
@keyframes spin {
  to { transform: rotate(360deg); }
}

/* IMPRESIÓN LIMPIA DE PDF */
@media print {
  body {
    background: #ffffff !important;
    color: #000000 !important;
  }

  .no-print {
    display: none !important;
  }

  .only-print {
    display: block !important;
  }

  .report-canvas {
    border: none !important;
    background: transparent !important;
    padding: 0 !important;
    box-shadow: none !important;
  }

  .print-official-header {
    border-bottom: 2px solid #000000;
    padding-bottom: 12px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
  }

  .official-logo-box h1 {
    font-size: 1.6rem;
    margin: 4px 0 0;
    color: #000000;
    font-weight: 800;
  }

  .official-badge {
    font-size: 0.7rem;
    border: 1px solid #000000;
    padding: 2px 6px;
    font-weight: 800;
    letter-spacing: 0.05em;
  }

  .official-meta {
    text-align: right;
    font-size: 0.8rem;
    color: #333333;
  }

  .official-meta p {
    margin: 2px 0;
  }

  .report-table th {
    background: #f1f5f9 !important;
    color: #000000 !important;
    border-bottom: 2px solid #000000 !important;
  }

  .report-table td {
    color: #000000 !important;
    border-bottom: 1px solid #e2e8f0 !important;
  }

  .code-badge {
    background: transparent !important;
    border: none !important;
    color: #000000 !important;
    padding: 0 !important;
    font-family: inherit;
  }

  .print-official-footer {
    margin-top: 40px;
    border-top: 1px solid #cbd5e1;
    padding-top: 10px;
    text-align: center;
    font-size: 0.75rem;
    color: #64748b;
  }

  .print-official-footer p {
    margin: 2px 0;
  }
}
</style>
