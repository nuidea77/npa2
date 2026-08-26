import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from './stores/auth';

const routes = [
    { path: '/', component: () => import('./pages/Home.vue') },
    { path: '/about', component: () => import('./pages/About.vue') },

    { path: '/programs', component: () => import('./pages/Programs.vue') },
    { path: '/programs/passport', component: () => import('./pages/ProgramPassport.vue') },
    { path: '/programs/khuraldai', component: () => import('./pages/ProgramKhuraldai.vue') },
    { path: '/programs/junior-ranger', component: () => import('./pages/ProgramJunior.vue') },
    { path: '/programs/regional-training', component: () => import('./pages/ProgramRegional.vue') },
    { path: '/programs/sister-parks', component: () => import('./pages/ProgramSisterParks.vue') },
    { path: '/programs/volunteer', component: () => import('./pages/ProgramVolunteer.vue') },
    { path: '/programs/internship', component: () => import('./pages/ProgramInternship.vue') },

    { path: '/jobs', component: () => import('./pages/Jobs.vue') },
    { path: '/jobs/:id', component: () => import('./pages/JobDetail.vue') },

    { path: '/parks', component: () => import('./pages/Parks.vue') },
    { path: '/parks/:id', component: () => import('./pages/ParkDetail.vue') },

    { path: '/news', component: () => import('./pages/NewsList.vue') },
    { path: '/news/:id', component: () => import('./pages/NewsDetail.vue') },

    { path: '/help', component: () => import('./pages/Help.vue') },

    { path: '/login', component: () => import('./pages/Login.vue') },
    { path: '/register', component: () => import('./pages/Register.vue') },
    { path: '/forgot', component: () => import('./pages/Forgot.vue') },

    { path: '/events/:id/register', component: () => import('./pages/EventRegister.vue') },

    { path: '/dashboard', component: () => import('./pages/Dashboard.vue'), meta: { auth: true } },

    {
        path: '/admin',
        component: () => import('./pages/admin/AdminLayout.vue'),
        meta: { auth: true, admin: true },
        children: [
            { path: '', component: () => import('./pages/admin/AdminHome.vue') },
            { path: 'users', component: () => import('./pages/admin/AdminUsers.vue') },
            { path: 'orgs', component: () => import('./pages/admin/AdminOrgs.vue') },
            { path: 'parks', component: () => import('./pages/admin/AdminParks.vue') },
            { path: 'news', component: () => import('./pages/admin/AdminNews.vue') },
            { path: 'jobs', component: () => import('./pages/admin/AdminJobs.vue') },
            { path: 'stamps', component: () => import('./pages/admin/AdminStamps.vue') },
            { path: 'events', component: () => import('./pages/admin/AdminEvents.vue') },
            { path: 'inbox', component: () => import('./pages/admin/AdminInbox.vue') },
            { path: 'faq', component: () => import('./pages/admin/AdminFaq.vue') },
            { path: 'trainings', component: () => import('./pages/admin/AdminTrainings.vue') },
            { path: 'settings', component: () => import('./pages/admin/AdminSettings.vue') },
        ],
    },

    { path: '/:pathMatch(.*)*', component: () => import('./pages/NotFound.vue') },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to) {
        if (to.hash) return { el: to.hash, behavior: 'smooth', top: 90 };
        return { top: 0 };
    },
});

router.beforeEach(async (to) => {
    const auth = useAuthStore();
    if (!auth.loaded) await auth.fetchUser();

    if (to.meta.auth && !auth.isLoggedIn) {
        return { path: '/login', query: { next: to.fullPath } };
    }
    if (to.meta.admin && !auth.isAdmin) {
        return { path: '/' };
    }
});

export default router;
