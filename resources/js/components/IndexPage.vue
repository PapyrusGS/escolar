<template>
  <section class="dashboard">
    <header class="dashboard__header">
      <div>
        <p class="eyebrow">Panel principal</p>
        <h1>Bienvenido{{ user?.Nombre1 ? `, ${user.Nombre1}` : '' }}</h1>
        <p class="lead">
          {{ roleLabel }} | {{ user?.Rol?.Descripcion || 'Acceso al sistema escolar' }}
        </p>
      </div>
      <button class="logout" @click="logout" :disabled="loading">
        {{ loading ? 'Saliendo...' : 'Cerrar sesión' }}
      </button>
    </header>

    <div class="role-banner" :class="roleClass">
      <p class="role-banner__title">{{ roleLabel }}</p>
      <p class="role-banner__text">{{ roleWelcome }}</p>
    </div>

    <div class="role-actions" v-if="user?.IdRol === 1">
      <a class="role-link" href="/usuarios/create">Registrar usuarios</a>
      <a class="role-link" href="/usuarios">Gestionar usuarios</a>
      <a class="role-link" href="/cursos">Gestionar cursos</a>
    </div>

    <div class="role-actions">
      <a class="role-link" href="/perfil">Mi perfil</a>
      <a class="role-link" href="/dashboard">Dashboard</a>
      <a class="role-link" href="/cursos/visualizacion">Ver cursos</a>
    </div>

    <div class="role-actions" v-if="user?.IdRol === 3">
      <a class="role-link" href="/inscripciones">Inscribirme a cursos</a>
    </div>

    <div class="role-actions" v-if="user?.IdRol === 2">
      <a class="role-link" href="/docente/inscripciones">Ver inscritos</a>
    </div>

    <div class="cards">
      <article v-for="card in visibleCards" :key="card.title" class="card">
        <h2>{{ card.title }}</h2>
        <p>{{ card.description }}</p>
      </article>
    </div>
  </section>
</template>

<script>
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';

export default {
  name: 'IndexPage',
  setup() {
    const user = ref(null);
    const loading = ref(false);

    const roleMap = {
      1: {
        label: 'Administrador',
        welcome: 'Tienes acceso completo al sistema y sus módulos de gestión.',
        className: 'role-banner--admin',
        cards: [
          { title: 'Usuarios', description: 'Crear, editar, activar y desactivar cuentas.' },
          { title: 'Roles y permisos', description: 'Administrar accesos por perfil de usuario.' },
          { title: 'Reportes', description: 'Visualizar estadísticas y control general.' },
          { title: 'Configuración', description: 'Ajustes globales del sistema.' },
        ],
      },
      2: {
        label: 'Docente',
        welcome: 'Gestiona tus cursos, materias y calificaciones asignadas.',
        className: 'role-banner--teacher',
        cards: [
          { title: 'Mis cursos', description: 'Ver las clases asignadas en el periodo actual.' },
          { title: 'Registro de notas', description: 'Cargar y actualizar calificaciones.' },
          { title: 'Estudiantes', description: 'Consultar listas e información de inscritos.' },
          { title: 'Notificaciones', description: 'Revisar avisos académicos y del sistema.' },
        ],
      },
      3: {
        label: 'Estudiante',
        welcome: 'Consulta tus materias, avances y notificaciones personales.',
        className: 'role-banner--student',
        cards: [
          { title: 'Mis inscripciones', description: 'Ver materias y cursos en los que estás inscrito.' },
          { title: 'Mis notas', description: 'Consultar tus calificaciones registradas.' },
          { title: 'Horario', description: 'Revisar tus horarios y turnos.' },
          { title: 'Notificaciones', description: 'Leer mensajes importantes del sistema.' },
        ],
      },
    };

    const currentRole = computed(() => roleMap[user.value?.IdRol] ?? {
      label: 'Usuario',
      welcome: 'Acceso general al sistema.',
      className: 'role-banner--default',
      cards: [
        { title: 'Panel', description: 'Contenido disponible según permisos.' },
      ],
    });

    const roleLabel = computed(() => currentRole.value.label);
    const roleWelcome = computed(() => currentRole.value.welcome);
    const roleClass = computed(() => currentRole.value.className);
    const visibleCards = computed(() => currentRole.value.cards);

    onMounted(async () => {
      const token = localStorage.getItem('auth_token');

      if (!token) {
        window.location.href = '/';
        return;
      }

      axios.defaults.headers.common.Authorization = `Bearer ${token}`;

      try {
        const storedUser = localStorage.getItem('auth_user');
        if (storedUser) {
          user.value = JSON.parse(storedUser);
        }

        const response = await axios.get('/api/auth/perfil');
        user.value = response.data.data.user;
        localStorage.setItem('auth_user', JSON.stringify(user.value));
      } catch (error) {
        console.error(error);
        localStorage.removeItem('auth_token');
        localStorage.removeItem('auth_user');
        delete axios.defaults.headers.common.Authorization;
        window.location.href = '/';
      }
    });

    const logout = async () => {
      try {
        loading.value = true;

        if (localStorage.getItem('auth_token')) {
          await axios.post('/api/auth/logout');
        }
      } catch (error) {
        console.error(error);
      } finally {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('auth_user');
        delete axios.defaults.headers.common.Authorization;
        window.location.href = '/';
        loading.value = false;
      }
    };

    return {
      user,
      loading,
      roleLabel,
      roleWelcome,
      roleClass,
      visibleCards,
      logout,
    };
  },
};
</script>

<style scoped>
.dashboard {
  display: grid;
  gap: 1.5rem;
  color: #e5e7eb;
}

.dashboard__header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
}

.eyebrow {
  margin: 0 0 0.35rem;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  font-size: 0.72rem;
  color: #fbbf24;
}

h1 {
  margin: 0;
  font-size: 1.9rem;
}

.lead {
  margin: 0.5rem 0 0;
  color: #cbd5e1;
}

.logout {
  border: 0;
  border-radius: 999px;
  padding: 0.8rem 1.1rem;
  background: linear-gradient(135deg, #f97316, #fbbf24);
  color: #111827;
  font-weight: 700;
  cursor: pointer;
}

.role-banner {
  padding: 1rem 1.1rem;
  border-radius: 18px;
  color: #111827;
}

.role-banner--admin {
  background: linear-gradient(135deg, #f59e0b, #ef4444);
}

.role-banner--teacher {
  background: linear-gradient(135deg, #38bdf8, #2563eb);
}

.role-banner--student {
  background: linear-gradient(135deg, #34d399, #10b981);
}

.role-banner--default {
  background: linear-gradient(135deg, #fbbf24, #f97316);
}

.role-banner__title {
  margin: 0 0 0.35rem;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.72rem;
  font-weight: 700;
}

.role-banner__text {
  margin: 0;
  font-weight: 600;
}

.cards {
  display: grid;
  gap: 1rem;
}

.card {
  padding: 1rem;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.card h2 {
  margin: 0 0 0.5rem;
  font-size: 1.05rem;
}

.card p {
  margin: 0;
  color: #cbd5e1;
}

.role-actions {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.role-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.8rem 1rem;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.08);
  color: #e5e7eb;
  text-decoration: none;
  font-weight: 700;
}
</style>
