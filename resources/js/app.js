import './bootstrap';
import { createApp } from 'vue';
import LoginForm from './components/LoginForm.vue';
import IndexPage from './components/IndexPage.vue';
import UserCreateForm from './components/UserCreateForm.vue';

const mounts = [
    { id: 'login-app', component: LoginForm },
    { id: 'index-app', component: IndexPage },
    { id: 'user-create-app', component: UserCreateForm },
];

for (const mount of mounts) {
    const el = document.getElementById(mount.id);

    if (el) {
        createApp(mount.component).mount(el);
    }
}
