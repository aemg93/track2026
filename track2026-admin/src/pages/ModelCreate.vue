<template>

    <div class="space-y-6">

        <h1 class="text-3xl font-bold text-white">
            Nueva Modelo
        </h1>

        <div class="bg-gray-900 p-6 rounded-2xl border border-gray-800 space-y-4">

            <input
                v-model="form.name"
                class="w-full p-3 rounded-xl bg-gray-950 text-white border border-gray-800"
                placeholder="Nombre de la modelo"
            />

            <input
                v-model="form.studio_id"
                type="number"
                class="w-full p-3 rounded-xl bg-gray-950 text-white border border-gray-800"
                placeholder="Studio ID"
            />

            <label class="flex items-center gap-3 text-white">

                <input
                    v-model="form.active"
                    type="checkbox"
                />

                Activa

            </label>

            <div class="flex gap-3">

                <button
                    @click="createModel"
                    :disabled="loading"
                    class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl"
                >
                    {{ loading ? 'Creando...' : 'Crear Modelo' }}
                </button>

                <button
                    @click="router.push('/models')"
                    class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-xl"
                >
                    Cancelar
                </button>

            </div>

            <p
                v-if="error"
                class="text-red-400"
            >
                {{ error }}
            </p>

        </div>

    </div>

</template>

<script setup>

import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const router = useRouter()

const loading = ref(false)
const error = ref('')

const form = ref({
    name: '',
    studio_id: 1,
    active: true,
})

const createModel = async () => {

    loading.value = true
    error.value = ''

    try {

        const { data } = await api.post('/models', form.value)

        router.push(`/models/${data.data.id}`)

    } catch (e) {

        error.value = 'Error creando modelo'

    } finally {

        loading.value = false

    }

}

</script>