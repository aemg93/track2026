import { createRouter, createWebHistory } from 'vue-router'

import Login from '../pages/Login.vue'

import Dashboard from '../pages/Dashboard.vue'
import Performances from '../pages/Performances.vue'
import Earnings from '../pages/Earnings.vue'
import Bonuses from '../pages/Bonuses.vue'
import Penalties from '../pages/Penalties.vue'

import AdminLayout from '../layouts/AdminLayout.vue'


const routes = [


    {
        path: '/',
        redirect: '/dashboard',
    },


    {
        path: '/login',

        component: Login,

        meta: {
            guest: true,
        },
    },

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
                path: 'performances',

                component: Performances,
            },

            {
                path: 'earnings',

                component: Earnings,
            },

            {
                path: 'bonuses',

                component: Bonuses,
            },

            {
                path: 'penalties',

                component: Penalties,
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