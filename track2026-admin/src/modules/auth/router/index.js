const authRoutes = [
    {
        path: '/login',
        component: () => import('@/modules/auth/pages/Login.vue'),
        meta: {
            guest: true,
        },
    },
]

export default authRoutes