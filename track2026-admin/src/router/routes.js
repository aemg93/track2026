import authRoutes from '@/modules/auth/router/index.js'

import { dashboardRoutes } from '@/modules/dashboard'
import { financesRoutes } from '@/modules/finances'
import { performancesRoutes } from '@/modules/performances'
import { analyticsRoutes } from '@/modules/analytics'
import { settingsRoutes } from '@/modules/settings'

import AdminLayout from '@/layouts/AdminLayout.vue'

const routes = [
    ...authRoutes,

    {
        path: '/',
        redirect: '/dashboard',
    },

    {
        path: '/',
        component: AdminLayout,
        meta: {
            requiresAuth: true,
        },

        children: [
            ...dashboardRoutes,
            ...financesRoutes,
            ...performancesRoutes,
            ...analyticsRoutes,
            ...settingsRoutes,
        ],
    },
]

export default routes