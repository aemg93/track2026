<template>

    <div class="space-y-8">

        <DashboardHeader
            v-if="dashboard && finance"
            :dashboard="dashboard"
            :finance="finance"
        />

        <LoadingCard
            v-if="loading"
            text="Cargando dashboard..."
        />

        <template v-else-if="dashboard && finance">

            <DashboardKpis
                :dashboard="dashboard"
                :finance="finance"
            />

            <FinanceChart
                :finance="finance"
            />

            <RankingTable
                :ranking="dashboard?.ranking ?? []"
            />

        </template>

        <EmptyState
            v-else
            text="No hay información disponible"
        />

    </div>

</template>


<script setup>

import { onMounted, ref } from 'vue'

import dashboardService from '@/modules/dashboard/services/dashboardService'

import EmptyState from '@/components/ui/EmptyState.vue'
import LoadingCard from '@/components/ui/LoadingCard.vue'

import DashboardHeader from '@/modules/dashboard/components/DashboardHeader.vue'
import DashboardKpis from '@/modules/dashboard/components/DashboardKpis.vue'
import RankingTable from '@/modules/dashboard/components/RankingTable.vue'

import FinanceChart from '@/modules/finances/components/FinanceChart.vue'


const dashboard = ref(null)

const finance = ref(null)

const loading = ref(false)



async function reloadDashboard() {

    try {

        const data = await dashboardService.index()

        dashboard.value = data.dashboard

        finance.value = data.finance

    } catch (error) {

        console.error(error)

        dashboard.value = null

        finance.value = null

    } finally {

        loading.value = false

    }

}



onMounted(() => {

    loading.value = true

    reloadDashboard()

})

</script>