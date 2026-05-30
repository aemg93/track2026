import { createRouter, createWebHistory } from 'vue-router'

import Login from '../pages/Login.vue'

import Dashboard from '../pages/Dashboard.vue'
import Models from '../pages/Models.vue'
import ModelShow from '../pages/ModelShow.vue'
import Finances from '../pages/Finances.vue'
import Analytics from '../pages/Analytics.vue'
import Settings from '../pages/Settings.vue'

import AdminLayout from '../layouts/AdminLayout.vue'

/*
|--------------------------------------------------------------------------
| ROUTES
|--------------------------------------------------------------------------
*/

const routes = [

    {
        path: '/',
        redirect: '/dashboard',
    },

    /*
    |--------------------------------------------------------------------------
    | GUEST ROUTES
    |--------------------------------------------------------------------------
    */

    {
        path: '/login',
        component: Login,

        meta: {
            guest: true,
        },
    },

    /*
    |--------------------------------------------------------------------------
    | ADMIN LAYOUT
    |--------------------------------------------------------------------------
    */

    {
        path: '/',
        component: AdminLayout,

        meta: {
            requiresAuth: true,
        },

        children: [

            {
                path: 'dashboard',
                component: Dashboard,
            },

            {
                path: 'models',
                component: Models,
            },

            {
                path: 'models/:id',
                component: ModelShow,
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

/*
|--------------------------------------------------------------------------
| ROUTER
|--------------------------------------------------------------------------
*/

const router = createRouter({

    history: createWebHistory(),

    routes,
})

/*
|--------------------------------------------------------------------------
| AUTH GUARD
|--------------------------------------------------------------------------
*/

router.beforeEach((to) => {

    const token = localStorage.getItem('token')

    const isAuthRoute = to.meta.requiresAuth

    const isGuestRoute = to.meta.guest

    if (isAuthRoute && !token) {

        return '/login'
    }

    if (isGuestRoute && token) {

        return '/dashboard'
    }

})

export default router