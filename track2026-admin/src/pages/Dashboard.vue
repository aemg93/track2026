<template>

    <div class="space-y-8">

        <!-- HERO -->
        <div
            class="relative overflow-hidden rounded-3xl border border-gray-800 bg-gradient-to-br from-gray-950 via-gray-900 to-black p-8"
        >

            <div
                class="absolute top-0 right-0 w-96 h-96 bg-blue-500/10 blur-3xl rounded-full"
            />

            <div
                class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6"
            >

                <div>

                    <h1 class="text-4xl font-bold text-white tracking-tight">
                        Financial Dashboard
                    </h1>

                    <p class="text-gray-400 mt-3 text-lg">
                        Executive overview
                    </p>

                </div>

                <div class="flex flex-wrap gap-4">

                    <div
                        class="bg-blue-500/10 border border-blue-500/20 rounded-2xl px-6 py-4"
                    >

                        <p class="text-gray-400 text-sm">
                            Modelos
                        </p>

                        <h2 class="text-3xl font-bold text-blue-400">
                            {{ dashboard?.total_models || 0 }}
                        </h2>

                    </div>

                    <div
                        class="bg-emerald-500/10 border border-emerald-500/20 rounded-2xl px-6 py-4"
                    >

                        <p class="text-gray-400 text-sm">
                            Balance Neto
                        </p>

                        <h2 class="text-3xl font-bold text-emerald-400">
                            ${{ finance?.net_balance || 0 }}
                        </h2>

                    </div>

                </div>

            </div>

        </div>

        <!-- LOADING -->
        <LoadingCard
            v-if="loading"
            text="Cargando dashboard..."
        />

        <!-- CONTENT -->
        <template v-else-if="dashboard && finance">

            <!-- KPI STATS -->
            <DashboardStats
                :dashboard="dashboard"
                :finance="finance"
            />

            <!-- FINANCIAL CHART -->
            <FinanceChart
                :finance="finance"
            />

            <!-- STUDIO PANEL -->
            <StudioPanel
                :dashboard="dashboard"
                :finance="finance"
            />

            <!-- RANKING -->
            <RankingTable
                :ranking="dashboard.ranking"
            />

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

import LoadingCard from '../components/ui/LoadingCard.vue'
import EmptyState from '../components/ui/EmptyState.vue'

import DashboardStats from '../components/dashboard/DashboardStats.vue'
import RankingTable from '../components/dashboard/RankingTable.vue'
import FinanceChart from '../components/dashboard/FinanceChart.vue'
import StudioPanel from '../components/dashboard/StudioPanel.vue'

const dashboard = ref(null)

const finance = ref(null)

const loading = ref(false)

const loadDashboard = async () => {

    try {

        loading.value = true

        const response = await api.get('/dashboard')

        dashboard.value = response.data.data.dashboard

        finance.value = response.data.data.finance

    } catch (error) {

        console.error(error)

    } finally {

        loading.value = false
    }
}

onMounted(loadDashboard)

</script>
```
