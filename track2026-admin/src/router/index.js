import { createRouter, createWebHistory } from 'vue-router'

import Login from '../pages/Login.vue'

import Dashboard from '../pages/Dashboard.vue'
import Models from '../pages/Models.vue'
import ModelShow from '../pages/ModelShow.vue'
import ModelEdit from '../pages/ModelEdit.vue'
import ModelCreate from '../pages/ModelCreate.vue' // opcional (recomendado)

import Finances from '../pages/Finances.vue'
import Analytics from '../pages/Analytics.vue'
import Settings from '../pages/Settings.vue'

import AdminLayout from '../layouts/AdminLayout.vue'

const routes = [
    {
        path: '/',
        redirect: '/dashboard',
    },

    {
        path: '/login',
        component: Login,
        meta: { guest: true },
    },

    {
        path: '/',
        component: AdminLayout,
        meta: { requiresAuth: true },

        children: [

            {
                path: 'dashboard',
                component: Dashboard,
            },

            // =========================
            // MODELS MODULE
            // =========================

            {
                path: 'models',
                name: 'models.index',
                component: Models,
            },

            {
                path: 'models/create',
                name: 'models.create',
                component: ModelCreate,
            },

            {
                path: 'models/:id',
                name: 'models.show',
                component: ModelShow,
                props: true,
            },

            {
                path: 'models/:id/edit',
                name: 'models.edit',
                component: ModelEdit,
                props: true,
            },

            // =========================
            // OTHER MODULES
            // =========================

            {
                path: 'finances',
                component: Finances,
            },

            {
                path: 'analytics',
                component: Analytics,
            },

            {
                path: 'settings',
                component: Settings,
            },
        ],
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to) => {
    const token = localStorage.getItem('token')

    if (to.meta.requiresAuth && !token) {
        return '/login'
    }

    if (to.meta.guest && token) {
        return '/dashboard'
    }
})

export default router