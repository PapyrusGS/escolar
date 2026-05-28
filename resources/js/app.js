import './bootstrap';
import { createApp } from 'vue';
import LoginForm from './components/LoginForm.vue';
import IndexPage from './components/IndexPage.vue';
import UserCreateForm from './components/UserCreateForm.vue';
import UserManagementPage from './components/UserManagementPage.vue';
import ProfilePage from './components/ProfilePage.vue';
import CourseManagementPage from './components/CourseManagementPage.vue';
import EnrollmentPage from './components/EnrollmentPage.vue';
import TeacherEnrollmentPage from './components/TeacherEnrollmentPage.vue';
import CourseVisualizationPage from './components/CourseVisualizationPage.vue';
import DashboardPage from './components/DashboardPage.vue';

const loginApp = document.getElementById('login-app');
const indexApp = document.getElementById('index-app');
const userCreateApp = document.getElementById('user-create-app');
const userManagementApp = document.getElementById('user-management-app');
const profileApp = document.getElementById('profile-app');
const courseApp = document.getElementById('course-management-app');
const enrollmentApp = document.getElementById('enrollment-app');
const teacherEnrollmentApp = document.getElementById('teacher-enrollment-app');
const courseVisualizationApp = document.getElementById('course-visualization-app');
const dashboardApp = document.getElementById('dashboard-app');

if (loginApp) {
    createApp(LoginForm).mount(loginApp);
}

if (indexApp) {
    createApp(IndexPage).mount(indexApp);
}

if (userCreateApp) {
    createApp(UserCreateForm).mount(userCreateApp);
}

if (userManagementApp) {
    createApp(UserManagementPage).mount(userManagementApp);
}

if (profileApp) {
    createApp(ProfilePage).mount(profileApp);
}

if (courseApp) {
    createApp(CourseManagementPage).mount(courseApp);
}

if (enrollmentApp) {
    createApp(EnrollmentPage).mount(enrollmentApp);
}

if (teacherEnrollmentApp) {
    createApp(TeacherEnrollmentPage).mount(teacherEnrollmentApp);
}

if (courseVisualizationApp) {
    createApp(CourseVisualizationPage).mount(courseVisualizationApp);
}

if (dashboardApp) {
    createApp(DashboardPage).mount(dashboardApp);
}
