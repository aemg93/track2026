<template>

    <div class="space-y-8">

        <!-- HEADER -->
        <PageHeader
            title="Dashboard"
            description="SaaS administration panel"
        >

            <div class="bg-blue-600 text-white px-5 py-3 rounded-2xl font-semibold shadow-lg">

                {{ dashboard?.total_models || 0 }} modelos

            </div>

        </PageHeader>

        <!-- LOADING -->
        <LoadingCard
            v-if="loading"
            text="Cargando dashboard..."
        />

        <!-- CONTENT -->
        <template v-else-if="dashboard">

            <!-- STATS -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <StatCard
                    title="Modelos"
                    :value="dashboard.total_models"
                />

                <StatCard
                    title="Vista"
                    :value="dashboard.view"
                />

            </div>

            <!-- PERFORMANCE -->
            <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">

                <div class="flex items-center justify-between p-6 border-b border-gray-800">

                    <div>

                        <h2 class="text-2xl font-bold text-white">
                            Rendimiento
                        </h2>

                        <p class="text-gray-400 text-sm mt-1">
                            Modelos destacadas
                        </p>

                    </div>

                </div>

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-gray-800">

                            <tr>

                                <th class="table-head">
                                    Nombre
                                </th>

                                <th class="table-head">
                                    Horas
                                </th>

                                <th class="table-head">
                                    Puntuación
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <tr
                                v-for="item in dashboard.ranking"
                                :key="item.id"
                                class="border-t border-gray-800 hover:bg-gray-800/50 transition"
                            >

                                <!-- NAME -->
                                <td class="table-cell">

                                    <div>

                                        <p class="font-semibold text-white">
                                            {{ item.name }}
                                        </p>

                                        <p class="text-sm text-gray-500">
                                            Modelo #{{ item.id }}
                                        </p>

                                    </div>

                                </td>

                                <!-- HOURS -->
                                <td class="table-cell">

                                    <span class="text-white font-medium">

                                        {{ item.hours_streamed }} hrs

                                    </span>

                                </td>

                                <!-- SCORE -->
                                <td class="table-cell">

                                    <Badge variant="blue">

                                        {{ item.ranking_score }}

                                    </Badge>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </template>

        <!-- EMPTY -->
        <EmptyState
            v-else
            text="No hay información disponible"
        />

    </div>

</template>

<script setup>

import { ref, onMounted } from 'vue'

import api from '../services/api'

import PageHeader from '../components/ui/PageHeader.vue'
import StatCard from '../components/ui/StatCard.vue'
import Badge from '../components/ui/Badge.vue'
import LoadingCard from '../components/ui/LoadingCard.vue'
import EmptyState from '../components/ui/EmptyState.vue'

const dashboard = ref(null)

const loading = ref(false)

const loadDashboard = async () => {

    try {

        loading.value = true

        const response = await api.get('/dashboard')

        dashboard.value = response.data.data

    } catch (error) {

        console.error(error)

    } finally {

        loading.value = false
    }
}

onMounted(() => {

    loadDashboard()
})

</script>

<style scoped>

.table-head {
    padding: 18px 24px;

    text-align: left;

    font-size: 14px;

    font-weight: 600;

    color: #9ca3af;
}

.table-cell {
    padding: 20px 24px;
}

</style>