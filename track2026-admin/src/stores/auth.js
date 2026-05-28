import { defineStore } from 'pinia'
import api from '../services/api'

export const useAuthStore = defineStore('auth', {

    state: () => ({

        token: localStorage.getItem('token'),
        user: null,

        loading: false,
        initialized: false,
    }),

/* -------------------------------------------------
| GETTERS
------------------------------------------------- */

    getters: {

        isAuthenticated: (state) => !!state.token,

        isAdmin: (state) =>
            (state.user?.roles || []).includes('Super Admin'),

        roles: (state) => state.user?.roles || [],

        permissions: (state) => state.user?.permissions || [],

        can: (state) => (permission) =>
            (state.user?.permissions || []).includes(permission),
    },

/* -------------------------------------------------
| ACTIONS
------------------------------------------------- */

    actions: {

        async login(email, password) {

            const response = await api.post('/login', {
                email,
                password,
            })

            const data = response.data.data ?? response.data

            this.token = data.token
            this.user = data.user ?? data

            localStorage.setItem('token', this.token)
        },

        async me() {

            if (!this.token) {
                this.initialized = true
                return
            }

            try {

                this.loading = true

                const response = await api.get('/me')

                const data = response.data.data ?? response.data

                this.user = data.user ?? data

            } catch (error) {

                this.logoutLocal()

            } finally {

                this.loading = false
                this.initialized = true
            }
        },

        async logout() {

            try {
                await api.post('/logout')
            } catch (error) {
                console.error(error)
            }

            this.logoutLocal()
        },

        logoutLocal() {

            this.token = null
            this.user = null

            this.initialized = true

            localStorage.removeItem('token')
        },
    },
})