<template>
  <section class="user-profile">
    <header class="user-profile__header">
      <div>
        <p class="eyebrow">RF03</p>
        <h1>Mi perfil</h1>
        <p>Visualiza tu información académica, personal y administra la seguridad de tu cuenta.</p>
      </div>
      <a class="back-link" href="/index">Volver al panel</a>
    </header>

    <!-- Alertas Generales -->
    <div v-if="message" class="alert" :class="`alert--${messageType}`">
      {{ message }}
      <button class="alert__close" @click="message = ''">&times;</button>
    </div>

    <div class="profile-card">
      <!-- Pestañas de Navegación -->
      <nav class="profile-tabs">
        <button 
          class="tab-link" 
          :class="{ 'tab-link--active': activeTab === 'datos' }" 
          @click="activeTab = 'datos'"
        >
          Datos Personales
        </button>
        <button 
          class="tab-link" 
          :class="{ 'tab-link--active': activeTab === 'seguridad' }" 
          @click="activeTab = 'seguridad'"
        >
          Seguridad
        </button>
      </nav>

      <!-- Pestaña 1: Datos Personales -->
      <div v-if="activeTab === 'datos'" class="tab-content">
        <form @submit.prevent="updateProfile" class="profile-form">
          <div class="grid">
            <label>
              Nombre 1
              <input v-model.trim="form.Nombre1" type="text" required />
            </label>

            <label>
              Nombre 2
              <input v-model.trim="form.Nombre2" type="text" />
            </label>

            <label>
              Apellido 1
              <input v-model.trim="form.Apellido1" type="text" required />
            </label>

            <label>
              Apellido 2
              <input v-model.trim="form.Apellido2" type="text" />
            </label>

            <label>
              CI
              <input v-model.trim="form.CI" type="text" required />
            </label>

            <label>
              Teléfono
              <input v-model.trim="form.Telefono" type="text" required />
            </label>

            <label>
              Correo Electrónico
              <input v-model.trim="form.Correo" type="email" required />
            </label>

            <label>
              Rol de Acceso <span class="optional">(No editable)</span>
              <input :value="form.Rol?.Nombre || 'Cargando...'" type="text" disabled class="input-disabled" />
            </label>
          </div>

          <!-- Información Académica Solo Para Estudiantes -->
          <div v-if="form.IdRol === 3" class="academic-panel">
            <h3>Información Académica</h3>
            <div class="academic-grid">
              <div class="academic-item">
                <span class="academic-item__label">Carrera</span>
                <span class="academic-item__value">{{ form.CarreraNombre || 'No asignada' }}</span>
              </div>
              <div class="academic-item">
                <span class="academic-item__label">Modalidad</span>
                <span class="academic-item__value badge badge--modalidad">{{ form.ModalidadNombre || 'No asignada' }}</span>
              </div>
            </div>
          </div>

          <div class="actions">
            <button type="submit" :disabled="submitting">
              {{ submitting ? 'Guardando...' : 'Guardar Cambios' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Pestaña 2: Seguridad / Cambiar Contraseña -->
      <div v-if="activeTab === 'seguridad'" class="tab-content">
        <form @submit.prevent="changePassword" class="profile-form">
          <div class="grid grid--single">
            <label>
              Contraseña Actual
              <input v-model="passwordForm.password_actual" type="password" required placeholder="Escribe tu contraseña actual para confirmar" />
            </label>

            <label>
              Nueva Contraseña
              <input v-model="passwordForm.contrasena" type="password" required minlength="6" placeholder="Mínimo 6 caracteres" />
            </label>

            <label>
              Confirmar Nueva Contraseña
              <input v-model="passwordForm.contrasena_confirmation" type="password" required placeholder="Repite la nueva contraseña" />
            </label>
          </div>

          <div class="actions">
            <button type="submit" class="btn-save" :disabled="submitting">
              {{ submitting ? 'Actualizando...' : 'Cambiar Contraseña' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>
</template>

<script>
import axios from 'axios';

export default {
  name: 'UserProfile',
  data() {
    return {
      activeTab: 'datos',
      submitting: false,
      message: '',
      messageType: 'error',
      form: {
        IdUsuario: null,
        IdRol: null,
        Rol: null,
        Nombre1: '',
        Nombre2: '',
        Apellido1: '',
        Apellido2: '',
        CI: '',
        Telefono: '',
        Correo: '',
        CarreraNombre: '',
        ModalidadNombre: '',
      },
      passwordForm: {
        password_actual: '',
        contrasena: '',
        contrasena_confirmation: '',
      }
    };
  },
  mounted() {
    this.loadProfile();
  },
  methods: {
    async loadProfile() {
      try {
        const token = localStorage.getItem('auth_token');
        if (token) {
          axios.defaults.headers.common.Authorization = `Bearer ${token}`;
        } else {
          window.location.href = '/';
          return;
        }

        const { data } = await axios.get('/api/auth/perfil');
        const user = data?.data?.user;
        if (user) {
          this.form = { ...user };
        }
      } catch (error) {
        this.setMessage('No se pudo recuperar la información del perfil.', 'error');
      }
    },
    async updateProfile() {
      this.submitting = true;
      this.message = '';

      try {
        const payload = {
          Nombre1: this.form.Nombre1,
          Nombre2: this.form.Nombre2,
          Apellido1: this.form.Apellido1,
          Apellido2: this.form.Apellido2,
          CI: this.form.CI,
          Telefono: this.form.Telefono,
          Correo: this.form.Correo,
        };

        const { data } = await axios.put('/api/auth/perfil', payload);
        const user = data?.data?.user;
        if (user) {
          this.form = { ...user };
          localStorage.setItem('auth_user', JSON.stringify(user));
        }
        this.setMessage('Perfil actualizado correctamente.', 'success');
      } catch (error) {
        if (error?.response?.status === 422) {
          const errors = error.response?.data?.errors;
          if (errors && typeof errors === 'object') {
            const messages = Object.values(errors).flat().join(' | ');
            this.setMessage(messages || 'Los datos de entrada no son válidos.', 'error');
          } else {
            const responseMsg = error.response?.data?.message;
            this.setMessage(responseMsg || 'Los datos de entrada no son válidos.', 'error');
          }
        } else {
          const responseMsg = error?.response?.data?.message;
          this.setMessage(responseMsg || 'Ocurrió un error al actualizar el perfil.', 'error');
        }
      } finally {
        this.submitting = false;
      }
    },
    async changePassword() {
      if (this.passwordForm.contrasena !== this.passwordForm.contrasena_confirmation) {
        this.setMessage('Las nuevas contraseñas no coinciden.', 'error');
        return;
      }

      this.submitting = true;
      this.message = '';

      try {
        const { data } = await axios.put('/api/auth/contrasena', this.passwordForm);
        this.setMessage(data?.message || 'Contraseña cambiada correctamente.', 'success');
        this.passwordForm = {
          password_actual: '',
          contrasena: '',
          contrasena_confirmation: '',
        };
      } catch (error) {
        const responseMsg = error?.response?.data?.message;
        this.setMessage(responseMsg || 'No se pudo cambiar la contraseña. Verifica tu clave actual.', 'error');
      } finally {
        this.submitting = false;
      }
    },
    setMessage(message, type = 'error') {
      this.message = message;
      this.messageType = type;
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  }
};
</script>

<style scoped>
.user-profile { min-height: 100vh; padding: 32px; background: linear-gradient(180deg, #07111f 0%, #101b2b 100%); color: #eef2ff; }
.user-profile__header { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 32px; }
.eyebrow { margin: 0 0 8px; color: #fbbf24; text-transform: uppercase; letter-spacing: .18em; font-size: .75rem; }
h1 { margin: 0; font-size: 2rem; }
p { margin: 8px 0 0; color: #cbd5e1; }
.back-link, button { border-radius: 999px; padding: 12px 24px; font-weight: 700; text-decoration: none; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; }
.back-link { background: transparent; border: 1px solid rgba(148, 163, 184, .22); color: #cbd5e1; }
button { background: #fbbf24; color: #0f172a; }

.alert { position: relative; margin: 20px 0; padding: 16px 40px 16px 18px; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; }
.alert--success { background: rgba(16, 185, 129, .16); color: #d1fae5; border: 1px solid rgba(16, 185, 129, .3); }
.alert--error { background: rgba(239, 68, 68, .16); color: #fecaca; border: 1px solid rgba(239, 68, 68, .3); }
.alert__close { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: transparent; border: none; font-size: 1.5rem; color: inherit; cursor: pointer; }

/* Tarjeta Perfil */
.profile-card { background: rgba(15, 23, 42, .86); border: 1px solid rgba(148, 163, 184, .18); border-radius: 24px; box-shadow: 0 20px 60px rgba(0, 0, 0, .25); overflow: hidden; }

/* Tabs */
.profile-tabs { display: flex; background: rgba(30, 41, 59, .5); border-bottom: 1px solid rgba(148, 163, 184, .12); }
.tab-link { background: transparent; color: #94a3b8; border-radius: 0; padding: 16px 24px; font-weight: 600; transition: all 0.2s ease; border-bottom: 2px solid transparent; }
.tab-link:hover { color: #f8fafc; background: rgba(255, 255, 255, .02); }
.tab-link--active { color: #fbbf24; border-bottom-color: #fbbf24; font-weight: 700; background: rgba(15, 23, 42, .4); }

/* Contenido de Pestañas */
.tab-content { padding: 24px; }
.profile-form .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
.profile-form .grid--single { grid-template-columns: 1fr; max-width: 500px; }
.profile-form label { display: flex; flex-direction: column; gap: 8px; font-weight: 600; color: #e2e8f0; font-size: 0.95rem; }
.profile-form input { border-radius: 14px; border: 1px solid rgba(148, 163, 184, .22); background: rgba(30, 41, 59, .82); color: #f8fafc; padding: 14px 16px; font-size: 1rem; outline: none; }
.profile-form input:focus { border-color: #fbbf24; box-shadow: 0 0 0 3px rgba(251, 191, 36, .18); }
.optional { color: #64748b; font-size: 0.78rem; font-weight: 400; }
.input-disabled { background: rgba(30, 41, 59, .4) !important; color: #94a3b8 !important; border-color: rgba(148, 163, 184, .08) !important; cursor: not-allowed; }

/* Panel Académico */
.academic-panel { margin-top: 32px; background: rgba(30, 41, 59, .4); border: 1px solid rgba(148, 163, 184, .12); border-radius: 20px; padding: 20px; }
.academic-panel h3 { margin: 0 0 16px; font-size: 1.15rem; color: #fbbf24; font-weight: 700; border-bottom: 1px solid rgba(148, 163, 184, .12); padding-bottom: 8px; }
.academic-grid { display: flex; gap: 40px; flex-wrap: wrap; }
.academic-item { display: flex; flex-direction: column; gap: 6px; }
.academic-item__label { color: #94a3b8; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
.academic-item__value { color: #f8fafc; font-size: 1.05rem; font-weight: 700; }

.badge { display: inline-flex; align-items: center; justify-content: center; padding: 4px 12px; border-radius: 999px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
.badge--modalidad { background: rgba(139, 92, 246, .16); color: #c4b5fd; border: 1px solid rgba(139, 92, 246, .3); align-self: flex-start; }

.actions { display: flex; justify-content: flex-end; margin-top: 24px; }
.btn-save { background: #fbbf24; color: #0f172a; }
button:disabled { opacity: 0.7; cursor: not-allowed; }

@media (max-width: 900px) {
  .user-profile__header { flex-direction: column; align-items: stretch; gap: 20px; }
  .profile-form .grid { grid-template-columns: 1fr; }
  .profile-form .grid--single { max-width: 100%; }
  .academic-grid { flex-direction: column; gap: 16px; }
}
</style>
