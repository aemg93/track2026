import Operations from '../pages/Operations.vue'

export const operationsRoutes = [
    {
        path: '/operations',
        name: 'operations',
        component: Operations,
        meta: {
            requiresAuth: true,
        },
    },
]