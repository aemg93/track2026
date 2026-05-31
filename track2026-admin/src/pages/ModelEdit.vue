<template>

    <div class="space-y-6">

        <h1 class="text-3xl font-bold text-white">
            Editar Modelo
        </h1>
<h1 class="text-red-500 text-4xl">
    ESTOY EN EDIT
</h1>
        <div class="bg-gray-900 p-6 rounded-2xl border border-gray-800 space-y-4">

            <p class="text-gray-400">
                ID: {{ model?.id }}
            </p>

            <input
                v-model="form.name"
                class="w-full p-3 rounded-xl bg-gray-950 text-white border border-gray-800"
                placeholder="Nombre del modelo"
            />

            <div class="flex gap-3">

                <button
                    @click="updateModel"
                    :disabled="loading"
                    class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl disabled:opacity-50"
                >
                    {{ loading ? 'Guardando...' : 'Guardar cambios' }}
                </button>

                <button
                    @click="goBack"
                    class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-xl"
                >
                    Cancelar
                </button>

            </div>

            <p v-if="error" class="text-red-400 text-sm">
                {{ error }}
            </p>

            <p v-if="success" class="text-green-400 text-sm">
                Modelo actualizado correctamente
            </p>

        </div>

    </div>

</template>

<script setup>

import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api'

const route = useRoute()
const router = useRouter()

const model = ref(null)

const form = ref({
    name: ''
})

const loading = ref(false)
const error = ref('')
const success = ref(false)

/*
|--------------------------------------------------------------------------
| LOAD MODEL
|--------------------------------------------------------------------------
*/

const loadModel = async () => {

    try {

        const { data } = await api.get(`/models/${route.params.id}`)

        model.value = data.data
        form.value.name = model.value.name

    } catch {

        error.value = 'Error cargando modelo'
    }
}

/*
|--------------------------------------------------------------------------
| UPDATE MODEL
|--------------------------------------------------------------------------
*/

const updateModel = async () => {

    loading.value = true
    error.value = ''
    success.value = false

    try {

        await api.put(`/models/${route.params.id}`, form.value)

        success.value = true

        setTimeout(() => {
            router.push('/models')
        }, 600)

    } catch {

        error.value = 'Error actualizando modelo'

    } finally {

        loading.value = false
    }
}

/*
|--------------------------------------------------------------------------
| NAVIGATION
|--------------------------------------------------------------------------
*/

const goBack = () => {
    router.push('/models')
}

/*
|--------------------------------------------------------------------------
| INIT
|--------------------------------------------------------------------------
*/

onMounted(loadModel)

</script>