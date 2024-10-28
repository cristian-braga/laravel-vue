import Dashboard from './components/Dashboard.vue';
import AppointmentsList from './pages/appointments/AppointmentsList.vue';
import AppointmentForm from './pages/appointments/AppointmentForm.vue';
import UsersList from './pages/users/UsersList.vue';
import UpdateSettings from './pages/settings/UpdateSettings.vue';
import UpdateProfile from './pages/profile/UpdateProfile.vue';
import Login from './pages/auth/Login.vue';

export default [
    {
        path: '/login',
        name: 'admin.login',
        component: Login
    },
    {
        path: '/admin/dashboard',
        name: 'admin.dashboard',
        component: Dashboard
    },
    {
        path: '/admin/appointments',
        name: 'admin.appointments',
        component: AppointmentsList
    },
    {
        path: '/admin/appointments/create',
        name: 'admin.appointments.create',
        component: AppointmentForm
    },
    {
        path: '/admin/appointments/:id/edit',
        name: 'admin.appointments.edit',
        component: AppointmentForm
    },
    {
        path: '/admin/users',
        name: 'admin.users',
        component: UsersList
    },
    {
        path: '/admin/settings',
        name: 'admin.settings',
        component: UpdateSettings
    },
    {
        path: '/admin/profile',
        name: 'admin.profile',
        component: UpdateProfile
    }
];
