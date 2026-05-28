<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const email = ref('admin@example.com')
const password = ref('123456')

const loading = ref(false)
const error = ref(null)
const submit = async () => {
    try {
        loading.value = true
        error.value = null

        await auth.login(email.value, password.value)

        router.push('/dashboard')
    } catch (err) {
        error.value = 'Credenciales inválidas'
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div class="login-page">
        <div class="login-card">
            <h1>Trackmodel</h1>

            <form @submit.prevent="submit">

                <input
                    v-model="email"
                    type="email"
                    placeholder="Email"
                />

                <input
                    v-model="password"
                    type="password"
                    placeholder="Password"
                />

                <button :disabled="loading">
                    {{ loading ? 'Ingresando...' : 'Login' }}
                </button>

                <p v-if="error">
                    {{ error }}
                </p>

            </form>
        </div>
    </div>
</template>

<style scoped>
.login-page {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #111827;
}

.login-card {
    width: 350px;
    padding: 30px;
    border-radius: 10px;
    background: white;
}

input {
    width: 100%;
    margin-bottom: 15px;
    padding: 10px;
}

button {
    width: 100%;
    padding: 10px;
}
</style>