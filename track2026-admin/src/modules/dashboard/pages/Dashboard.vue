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
        @start-shift="startPerformanceShift"
        @earning="modalHandlers.revenue"
        @bonus="modalHandlers.bonus"
        @penalty="modalHandlers.penalty"
        @deduction="modalHandlers.deduction"
        @pause="pausePerformanceShift"
        @resume="resumePerformanceShift"
        @finish="finishPerformanceShift"
      />

      <FinanceChart :finance="finance" />

      <RankingTable :ranking="dashboard?.ranking ?? []" />

    </template>

    <EmptyState
      v-else
      text="No hay información disponible"
    />

    <RegisterRevenueModal
      v-if="modals.revenue"
      :performance-id="selectedPerformance?.id"
      :platforms="selectedPerformance?.platforms ?? []"
      @close="closeModal('revenue')"
      @saved="handleFinanceSaved"
    />

    <RegisterBonusModal
      v-if="modals.bonus"
      :performance-id="selectedPerformance?.id"
      @close="closeModal('bonus')"
      @saved="handleFinanceSaved"
    />

    <RegisterPenaltyModal
      v-if="modals.penalty"
      :performance-id="selectedPerformance?.id"
      @close="closeModal('penalty')"
      @saved="handleFinanceSaved"
    />

    <RegisterDeductionModal
      v-if="modals.deduction"
      :performance-id="selectedPerformance?.id"
      @close="closeModal('deduction')"
      @saved="handleFinanceSaved"
    />

  </div>
</template>

<script setup>
import {
  onMounted,
  reactive,
  ref,
} from 'vue'

import dashboardService from '../services/dashboardService'
import { useShifts } from '../composables/useShifts'

import EmptyState from '@/components/ui/EmptyState.vue'
import LoadingCard from '@/components/ui/LoadingCard.vue'

import DashboardHeader from '../components/DashboardHeader.vue'
import DashboardKpis from '../components/DashboardKpis.vue'
import RankingTable from '../components/RankingTable.vue'
import StudioPanel from '../components/StudioPanel.vue'

import FinanceChart from '@/modules/finances/components/FinanceChart.vue'

import RegisterBonusModal from '@/modules/finances/components/modals/RegisterBonusModal.vue'
import RegisterDeductionModal from '@/modules/finances/components/modals/RegisterDeductionModal.vue'
import RegisterPenaltyModal from '@/modules/finances/components/modals/RegisterPenaltyModal.vue'
import RegisterRevenueModal from '@/modules/finances/components/modals/RegisterRevenueModal.vue'

const dashboard = ref(null)
const finance = ref(null)
const operations = ref(null)

const loading = ref(false)

const selectedPerformance = ref(null)

const modals = reactive({
  revenue: false,
  bonus: false,
  penalty: false,
  deduction: false,
})

const {
  startShift,
  pauseShift,
  resumeShift,
  finishShift,
} = useShifts()

/*
|--------------------------------------------------------------------------
| Modales
|--------------------------------------------------------------------------
*/

function openModal(name, performance) {
  if (!performance?.id) {
    return
  }

  selectedPerformance.value = performance
  modals[name] = true
}

function closeModal(name) {
  modals[name] = false

  if (!Object.values(modals).some(Boolean)) {
    selectedPerformance.value = null
  }
}

function closeModals() {
  Object.keys(modals).forEach(name => {
    modals[name] = false
  })

  selectedPerformance.value = null
}

const modalHandlers = {
  revenue: performance => openModal('revenue', performance),
  bonus: performance => openModal('bonus', performance),
  penalty: performance => openModal('penalty', performance),
  deduction: performance => openModal('deduction', performance),
}

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

async function loadDashboard() {
  loading.value = true

  try {

    const data = await dashboardService.index()

    dashboard.value = data.dashboard
    finance.value = data.finance
    operations.value = data.operations

  } catch (error) {

    console.error(error)

    dashboard.value = null
    finance.value = null
    operations.value = null

  } finally {

    loading.value = false

  }
}

/*
|--------------------------------------------------------------------------
| Refresh Parcial
|--------------------------------------------------------------------------
*/

async function refreshOperations() {

  try {

    const data = await dashboardService.index()

    operations.value = data.operations

  } catch (error) {

    console.error(error)

  }

}

async function refreshFinance() {

  try {

    const data = await dashboardService.index()

    finance.value = data.finance

  } catch (error) {

    console.error(error)

  }

}

async function refreshRanking() {

  try {

    const data = await dashboardService.index()

    dashboard.value.ranking = data.dashboard.ranking

  } catch (error) {

    console.error(error)

  }

}

/*
|--------------------------------------------------------------------------
| Shift Actions
|--------------------------------------------------------------------------
*/

async function startPerformanceShift(performance) {

  if (!performance?.id) {
    return
  }

  try {

    await startShift(performance.id)

    await refreshOperations()

  } catch (error) {

    console.error(error)

  }

}

async function pausePerformanceShift(shift) {

  if (!shift?.id) {
    return
  }

  try {

    await pauseShift(shift.id)

    await refreshOperations()

  } catch (error) {

    console.error(error)

  }

}

async function resumePerformanceShift(shift) {

  if (!shift?.id) {
    return
  }

  try {

    await resumeShift(shift.id)

    await refreshOperations()

  } catch (error) {

    console.error(error)

  }

}

async function finishPerformanceShift(shift) {

  if (!shift?.id) {
    return
  }

  try {

    await finishShift(shift.id)

    await refreshOperations()

  } catch (error) {

    console.error(error)

  }

}

/*
|--------------------------------------------------------------------------
| Finanzas
|--------------------------------------------------------------------------
*/

async function handleFinanceSaved() {

  closeModals()

  await Promise.all([
    refreshFinance(),
    refreshRanking(),
  ])

}

/*
|--------------------------------------------------------------------------
| Lifecycle
|--------------------------------------------------------------------------
*/

onMounted(loadDashboard)
</script>