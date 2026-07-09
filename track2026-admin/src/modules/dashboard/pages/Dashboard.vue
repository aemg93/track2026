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

              <StudioPanel
                :dashboard="dashboard"
                :finance="finance"
                @earning="openRevenueModal"
                @bonus="openBonusModal"
                @penalty="openPenaltyModal"
                @deduction="openDeductionModal"
                @finish="finishShift"
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

        <RegisterRevenueModal
             v-if="showRevenueModal"
            :performance-id="selectedPerformance?.id"
            :platforms="selectedPerformance?.platforms ?? []"
            @close="showRevenueModal = false"
            @saved="refreshDashboard"
        />

        <RegisterBonusModal
            v-if="showBonusModal"
            :performance-id="selectedPerformance?.id"
            @close="showBonusModal = false"
            @saved="refreshDashboard"
        />

        <RegisterPenaltyModal
            v-if="showPenaltyModal"
            :performance-id="selectedPerformance?.id"
            @close="showPenaltyModal = false"
            @saved="refreshDashboard"
        />

        <RegisterDeductionModal
            v-if="showDeductionModal"
            :performance-id="selectedPerformance?.id"
            @close="showDeductionModal = false"
            @saved="refreshDashboard"
        />


    </div>

</template>

<script setup>

import { ref, onMounted } from 'vue'
import api from '@/services/api'
import LoadingCard from '@/components/ui/LoadingCard.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import DashboardHeader from '../components/DashboardHeader.vue'
import DashboardKpis from '../components/DashboardKpis.vue'
import StudioPanel from '../components/StudioPanel.vue'
import RankingTable from '../components/RankingTable.vue'
import FinanceChart from '@/modules/finances/components/FinanceChart.vue'
import RegisterRevenueModal 
from '@/modules/finances/components/modals/RegisterRevenueModal.vue'
import RegisterBonusModal 
from '@/modules/finances/components/modals/RegisterBonusModal.vue'
import RegisterPenaltyModal 
from '@/modules/finances/components/modals/RegisterPenaltyModal.vue'
import RegisterDeductionModal 
from '@/modules/finances/components/modals/RegisterDeductionModal.vue'

const dashboard = ref(null)

const finance = ref(null)

const loading = ref(false)

const showRevenueModal = ref(false)

const showBonusModal = ref(false)

const showPenaltyModal = ref(false)

const showDeductionModal = ref(false)

const selectedModel = ref(null)

const selectedPerformance = ref(null)

const loadDashboard = async () => {

    loading.value = true

    try {

        const { data } = await api.get('/dashboard')

        dashboard.value =
            data.data.dashboard

        finance.value =
            data.data.finance

    } catch (error) {

        console.error(
            'Dashboard error:',
            error
        )

        dashboard.value = null

        finance.value = null

    } finally {

        loading.value = false

    }

}

function setSelection(model, performance) {

    selectedModel.value = model

    selectedPerformance.value = performance

}

function openRevenueModal(model, performance) {

    setSelection(
        model,
        performance
    )

    showRevenueModal.value = true

}

function openBonusModal(model, performance) {

    setSelection(
        model,
        performance
    )

    showBonusModal.value = true

}

function openPenaltyModal(model, performance) {

    setSelection(
        model,
        performance
    )

    showPenaltyModal.value = true

}

function openDeductionModal(model, performance) {

    setSelection(
        model,
        performance
    )

    showDeductionModal.value = true

}

function finishShift(model, performance) {

    console.log(
        'Finish shift',
        model,
        performance
    )

}

async function refreshDashboard() {

    showRevenueModal.value = false

    showBonusModal.value = false

    showPenaltyModal.value = false

    showDeductionModal.value = false

    await loadDashboard()

}

onMounted(loadDashboard)


</script>