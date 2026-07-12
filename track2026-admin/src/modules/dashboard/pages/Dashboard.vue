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
                :operations="operations"
                @start-shift="startShift"
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

import RegisterRevenueModal from '@/modules/finances/components/modals/RegisterRevenueModal.vue'
import RegisterBonusModal from '@/modules/finances/components/modals/RegisterBonusModal.vue'
import RegisterPenaltyModal from '@/modules/finances/components/modals/RegisterPenaltyModal.vue'
import RegisterDeductionModal from '@/modules/finances/components/modals/RegisterDeductionModal.vue'


const dashboard = ref(null)
const finance = ref(null)
const operations = ref(null)

const loading = ref(false)


const showRevenueModal = ref(false)
const showBonusModal = ref(false)
const showPenaltyModal = ref(false)
const showDeductionModal = ref(false)


const selectedPerformance = ref(null)



async function loadDashboard() {

    loading.value = true

    try {

        const { data } = await api.get('/dashboard')


        dashboard.value = data.data.dashboard

        finance.value = data.data.finance

        operations.value = data.data.operations


    } catch (error) {

        console.error(
            'Dashboard error:',
            error
        )


        dashboard.value = null

        finance.value = null

        operations.value = null


    } finally {

        loading.value = false

    }

}



function setSelection(performance) {

    selectedPerformance.value = performance

}



/*
|--------------------------------------------------------------------------
| Iniciar turno
|--------------------------------------------------------------------------
*/

async function startShift(performance) {

    try {

        await api.post(
            `/shifts/${performance.id}/start`
        )


        await loadDashboard()


    } catch (error) {

        console.error(
            'Start shift error:',
            error
        )

    }

}



/*
|--------------------------------------------------------------------------
| Modales financieros
|--------------------------------------------------------------------------
*/


function openRevenueModal(performance) {


    console.log(
        '========== REVENUE =========='
    )


    console.log(
        'Performance recibida:',
        performance
    )


    console.log(
        'ID:',
        performance?.id
    )


    console.log(
        'Plataformas:',
        performance?.platforms
    )


    console.log(
        '============================='
    )



    if (!performance) {

        return

    }


    setSelection(performance)


    showRevenueModal.value = true

}



function openBonusModal(performance) {


    console.log(
        'Bonus performance:',
        performance
    )


    if (!performance) {

        return

    }


    setSelection(performance)


    showBonusModal.value = true

}



function openPenaltyModal(performance) {


    console.log(
        'Penalty performance:',
        performance
    )


    if (!performance) {

        return

    }


    setSelection(performance)


    showPenaltyModal.value = true

}



function openDeductionModal(performance) {


    console.log(
        'Deduction performance:',
        performance
    )


    if (!performance) {

        return

    }


    setSelection(performance)


    showDeductionModal.value = true

}



/*
|--------------------------------------------------------------------------
| Finalizar turno
|--------------------------------------------------------------------------
*/

async function finishShift(shift) {


    if (!shift?.id) {

        console.error(
            'No existe shift para finalizar:',
            shift
        )

        return

    }



    try {


        await api.post(
            `/shifts/${shift.id}/finish`
        )


        await loadDashboard()



    } catch (error) {


        console.error(
            'Finish shift error:',
            error
        )


    }

}



/*
|--------------------------------------------------------------------------
| Refrescar dashboard
|--------------------------------------------------------------------------
*/

async function refreshDashboard() {


    showRevenueModal.value = false

    showBonusModal.value = false

    showPenaltyModal.value = false

    showDeductionModal.value = false



    await loadDashboard()

}



onMounted(loadDashboard)

</script>