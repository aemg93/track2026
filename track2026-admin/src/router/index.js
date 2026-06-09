import { createRouter, createWebHistory } from 'vue-router'

import Login from '../pages/Login.vue'

import Dashboard from '../pages/Dashboard.vue'
import Models from '../pages/Models.vue'
import ModelShow from '../pages/ModelShow.vue'
import ModelEdit from '../pages/ModelEdit.vue'
import ModelCreate from '../pages/ModelCreate.vue'

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

            {
                path: 'performances',
                name: 'performances.index',
                component: Models,
            },

            {
                path: 'performances/create',
                name: 'performances.create',
                component: ModelCreate,
            },

            {
                path: 'performances/:id',
                name: 'performances.show',
                component: ModelShow,
                props: true,
            },

            {
                path: 'performances/:id/edit',
                name: 'performances.edit',
                component: ModelEdit,
                props: true,
            },

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