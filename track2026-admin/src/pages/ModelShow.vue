<template>

    <div class="space-y-8">

        <!-- HEADER -->
        <div class="flex items-center justify-between">

            <div>

                <h1 class="text-4xl font-bold text-white">
                    {{ model?.name || 'Modelo' }}
                </h1>

                <p class="text-gray-400 mt-2">
                    Perfil detallado del modelo #{{ model?.id }}
                </p>

            </div>

            <button
                class="px-6 py-3 rounded-2xl bg-blue-500 hover:bg-blue-600 text-white"
            >
                Editar Modelo
            </button>

        </div>

        <!-- KPIS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="bg-gray-900 border border-gray-800 rounded-3xl p-6">

                <p class="text-gray-400">Horas transmitidas</p>

                <h2 class="text-3xl font-bold text-white mt-2">
                    {{ model?.hours_streamed || 0 }} hrs
                </h2>

            </div>

            <div class="bg-gray-900 border border-gray-800 rounded-3xl p-6">

                <p class="text-gray-400">Ranking</p>

                <h2 class="text-3xl font-bold text-blue-400 mt-2">
                    {{ model?.ranking_score || 0 }}
                </h2>

            </div>

            <div class="bg-gray-900 border border-gray-800 rounded-3xl p-6">

                <p class="text-gray-400">Estado</p>

                <h2 class="text-3xl font-bold text-green-400 mt-2">
                    {{ model?.status || 'active' }}
                </h2>

            </div>

        </div>

        <!-- FINANCIAL BREAKDOWN -->
        <div class="bg-gray-900 border border-gray-800 rounded-3xl p-6">

            <h2 class="text-xl font-bold text-white mb-4">
                Resumen financiero
            </h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                <div>
                    <p class="text-gray-400">Ganancias</p>
                    <p class="text-white font-bold">
                        ${{ model?.earnings || 0 }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-400">Bonos</p>
                    <p class="text-green-400 font-bold">
                        ${{ model?.bonuses || 0 }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-400">Multas</p>
                    <p class="text-red-400 font-bold">
                        ${{ model?.penalties || 0 }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-400">Descuentos</p>
                    <p class="text-yellow-400 font-bold">
                        ${{ model?.deductions || 0 }}
                    </p>
                </div>

            </div>

        </div>

        <!-- PLACEHOLDER ANALYTICS -->
        <div class="bg-gray-900 border border-gray-800 rounded-3xl p-6">

            <h2 class="text-xl font-bold text-white mb-4">
                Performance (próximo paso)
            </h2>

            <div class="h-64 flex items-center justify-center text-gray-500">
                Aquí van gráficos (ECharts)
            </div>

        </div>

    </div>

</template>

<script setup>

import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../services/api'

const route = useRoute()

const model = ref(null)

/**
 * LOAD MODEL
 */
const loadModel = async () => {

    try {

        const response = await api.get(`/models/${route.params.id}`)

        model.value = response.data.data

    } catch (error) {

        console.error('Error loading model:', error)
    }
}

onMounted(() => {
    loadModel()
})

</script>