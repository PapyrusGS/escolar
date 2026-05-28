<template>
  <section class="page">
    <header class="page__header">
      <div>
        <p class="eyebrow">RF08</p>
        <h1>Dashboard</h1>
        <p class="lead">{{ roleLabel }} | Resumen de cursos, actividades recientes e informacion relevante.</p>
      </div>
      <a class="back-link" href="/index">Volver al panel</a>
    </header>

    <div v-if="error" class="alert alert--error">{{ error }}</div>

    <section class="stats">
      <article v-for="item in summaryCards" :key="item.label" class="stat">
        <span>{{ item.label }}</span>
        <strong>{{ item.value }}</strong>
      </article>
    </section>

    <section class="layout">
      <article class="panel">
        <div class="panel__header">
          <div>
            <h2>Actividades recientes</h2>
            <p>Movimientos importantes detectados en tu perfil.</p>
          </div>
        </div>

        <div v-if="recentActivities.length" class="activity-list">
          <article v-for="activity in recentActivities" :key="activity.title + activity.meta" class="activity">
            <div>
              <p class="activity__title">{{ activity.title }}</p>
              <p class="activity__desc">{{ activity.description }}</p>
            </div>
            <span class="activity__meta">{{ activity.meta }}</span>
          </article>
        </div>
        <div v-else class="empty">Aun no hay actividades para mostrar.</div>
      </article>

      <article class="panel">
        <div class="panel__header">
          <div>
            <h2>Informacion relevante</h2>
            <p>Datos utiles segun tu rol y estado actual.</p>
          </div>
        </div>

        <div v-if="relevantInfo" class="info-box">
          <div class="info-box__item">
            <span>Usuario</span>
            <strong>{{ relevantInfo.NombreCompleto }}</strong>
          </div>
          <div class="info-box__item">
            <span>Correo</span>
            <strong>{{ relevantInfo.Correo }}</strong>
          </div>
          <div class="info-box__item">
            <span>Rol</span>
            <strong>{{ relevantInfo.Rol }}</strong>
          </div>
          <div class="info-box__item" v-if="relevantInfo.Carrera">
            <span>Carrera</span>
            <strong>{{ relevantInfo.Carrera }}</strong>
          </div>
          <div class="info-box__item" v-if="relevantInfo.Semestre">
            <span>Semestre</span>
            <strong>{{ relevantInfo.Semestre }}</strong>
          </div>
          <div class="info-box__item">
            <span>Estado</span>
            <strong>{{ relevantInfo.Estado ? 'Activo' : 'Inactivo' }}</strong>
          </div>
        </div>
      </article>
    </section>
  </section>
</template>

<script>
import axios from 'axios';

export default {
  name: 'DashboardPage',
  data() {
    return {
      loading: false,
      error: '',
      roleLabel: 'Usuario',
      summary: {},
      recentActivities: [],
      relevantInfo: null,
    };
  },
  computed: {
    summaryCards() {
      const entries = Object.entries(this.summary || {});

      return entries.map(([key, value]) => ({
        label: this.labelForSummaryKey(key),
        value: this.formatValue(key, value),
      }));
    },
  },
  mounted() {
    const token = localStorage.getItem('auth_token');

    if (!token) {
      window.location.href = '/';
      return;
    }

    axios.defaults.headers.common.Authorization = `Bearer ${token}`;
    this.loadData();
  },
  methods: {
    async loadData() {
      try {
        this.loading = true;
        this.error = '';

        const response = await axios.get('/api/dashboard');
        const payload = response.data.data;

        this.summary = payload.summary || {};
        this.recentActivities = payload.recent_activities || [];
        this.relevantInfo = payload.relevant_info?.user || null;
        this.roleLabel = this.relevantInfo?.Rol || 'Usuario';
      } catch (error) {
        console.error(error);
        this.error = error.response?.data?.message || 'No se pudo cargar el dashboard.';

        if (error.response?.status === 401) {
          window.location.href = '/';
        }
      } finally {
        this.loading = false;
      }
    },
    labelForSummaryKey(key) {
      const map = {
        total_users: 'Usuarios totales',
        total_active_courses: 'Cursos activos',
        total_active_enrollments: 'Inscripciones activas',
        total_active_grades: 'Notas activas',
        assigned_courses: 'Cursos asignados',
        active_enrollments: 'Inscritos activos',
        active_grades: 'Notas registradas',
        enrolled_courses: 'Cursos inscritos',
        average_grade: 'Promedio',
        active_notifications: 'Notificaciones',
        total_notifications: 'Notificaciones',
      };

      return map[key] || key;
    },
    formatValue(key, value) {
      if (key === 'average_grade') {
        return Number(value).toFixed(2);
      }

      return value;
    },
  },
};
</script>

<style scoped>
.page {
  display: grid;
  gap: 1.25rem;
  color: #e5e7eb;
}

.page__header {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  align-items: flex-start;
}

.eyebrow {
  margin: 0 0 0.35rem;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  font-size: 0.72rem;
  color: #60a5fa;
}

h1, h2, p {
  margin: 0;
}

h1 {
  font-size: 2rem;
}

.lead {
  margin-top: 0.5rem;
  color: #cbd5e1;
}

.back-link {
  align-self: center;
  text-decoration: none;
  color: #111827;
  background: linear-gradient(135deg, #60a5fa, #fbbf24);
  font-weight: 700;
  padding: 0.8rem 1rem;
  border-radius: 999px;
}

.alert {
  border-radius: 16px;
  padding: 0.9rem 1rem;
  font-weight: 600;
}

.alert--error {
  background: rgba(239, 68, 68, 0.16);
  color: #fecaca;
  border: 1px solid rgba(239, 68, 68, 0.25);
}

.stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 0.75rem;
}

.stat,
.panel {
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 20px;
}

.stat {
  padding: 1rem;
  display: grid;
  gap: 0.35rem;
}

.stat span,
.activity__meta,
.info-box__item span {
  color: #cbd5e1;
}

.stat strong {
  font-size: 1.4rem;
}

.layout {
  display: grid;
  grid-template-columns: 1.2fr 0.8fr;
  gap: 1rem;
}

.panel {
  padding: 1rem;
  display: grid;
  gap: 1rem;
}

.panel__header p {
  color: #cbd5e1;
  margin-top: 0.35rem;
}

.activity-list {
  display: grid;
  gap: 0.75rem;
}

.activity {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  padding: 0.95rem 1rem;
  border-radius: 16px;
  background: rgba(15, 23, 42, 0.55);
}

.activity__title {
  font-weight: 700;
}

.activity__desc {
  margin-top: 0.3rem;
  color: #e5e7eb;
}

.info-box {
  display: grid;
  gap: 0.75rem;
}

.info-box__item {
  padding: 0.9rem 1rem;
  border-radius: 16px;
  background: rgba(15, 23, 42, 0.55);
  display: grid;
  gap: 0.35rem;
}

.empty {
  padding: 1rem;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.04);
  color: #cbd5e1;
}

@media (max-width: 900px) {
  .page__header,
  .layout {
    grid-template-columns: 1fr;
    display: grid;
  }
}
</style>
