import { defineStore } from 'pinia'
import api from '../services/api'

export const useAuthStore = defineStore('auth', {

    state: () => ({

        token: localStorage.getItem('token'),
        user: null,
        loading: false,
        initialized: false,

    }),

    getters: {

        isAuthenticated: (state) => !!state.token,

    },
    actions: {

        async login(email, password) {

            const response = await api.post('/login', {

                email,
                password,

            })

            this.token = response.data.token
            this.user = response.data.user

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

                this.user = response.data.data

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