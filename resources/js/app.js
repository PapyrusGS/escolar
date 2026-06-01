import './bootstrap';
import { createApp } from 'vue';
import LoginForm from './components/LoginForm.vue';
import IndexPage from './components/IndexPage.vue';
import UserCreateForm from './components/UserCreateForm.vue';
import UserManagement from './components/UserManagement.vue';
import UserProfile from './components/UserProfile.vue';
import CursosManagement from './components/CursosManagement.vue';
import CursosVisualizacion from './components/CursosVisualizacion.vue';

const mounts = [
    { id: 'login-app', component: LoginForm },
    { id: 'index-app', component: IndexPage },
    { id: 'user-create-app', component: UserCreateForm },
    { id: 'user-management-app', component: UserManagement },
    { id: 'user-profile-app', component: UserProfile },
    { id: 'cursos-management-app', component: CursosManagement },
    { id: 'cursos-visualizacion-app', component: CursosVisualizacion },
];

for (const mount of mounts) {
    const el = document.getElementById(mount.id);

    if (el) {
        createApp(mount.component).mount(el);
    }
}
