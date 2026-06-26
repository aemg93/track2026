<template>

  <div v-if="performance" class="space-y-10">

    <ModelHeader :model="performance" />

    <ModelKpis :model="performance" />

    <section class="space-y-4">

      <h2 class="text-gray-400 text-xs uppercase tracking-[0.25em]">
        Finanzas
      </h2>

      <ModelFinancialSummary
        :earnings="totalEarnings"
        :bonuses="totalBonuses"
        :penalties="totalPenalties"
        :deductions="totalDeductions"
        :net="netTotal"
      />

    </section>


    <section class="space-y-4">

      <h2 class="text-gray-400 text-xs uppercase tracking-[0.25em]">
        Historial financiero
      </h2>

      <div class="space-y-6">

        <EarningsTable
          :earnings="performance.earnings || []"
        />

        <BonusesTable
          :bonuses="performance.bonuses || []"
        />

        <PenaltiesTable
          :penalties="performance.penalties || []"
        />

        <DeductionsTable
          :deductions="performance.deductions || []"
        />

      </div>

    </section>


    <section class="space-y-4">

      <details
        class="bg-gradient-to-br from-gray-900 to-gray-950 border border-gray-800 rounded-3xl p-6"
      >

        <summary class="cursor-pointer text-white font-semibold text-lg">
          Información del perfil
        </summary>


        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

          <ModelPersonalInfo :model="performance" />

          <ModelDocuments :model="performance" />

          <ModelStudioInfo :model="performance" />

        </div>

      </details>

    </section>


  </div>


  <div
    v-else
    class="flex justify-center items-center h-96 text-gray-400"
  >
    Cargando performance...
  </div>

</template>


<script setup>

import {
  ref,
  computed,
  onMounted
} from 'vue'

import {
  useRoute
} from 'vue-router'


import api from '../services/api'


import ModelHeader from '../components/models/ModelHeader.vue'
import ModelKpis from '../components/models/ModelKpis.vue'
import ModelFinancialSummary from '../components/models/ModelFinancialSummary.vue'
import ModelPersonalInfo from '../components/models/ModelPersonalInfo.vue'
import ModelDocuments from '../components/models/ModelDocuments.vue'
import ModelStudioInfo from '../components/models/ModelStudioInfo.vue'


import EarningsTable from '../components/models/history/EarningsTable.vue'
import BonusesTable from '../components/models/history/BonusesTable.vue'
import PenaltiesTable from '../components/models/history/PenaltiesTable.vue'
import DeductionsTable from '../components/models/history/DeductionsTable.vue'


const route = useRoute()


const performance = ref(null)

const financial = ref(null)


const safe = value => 
  Array.isArray(value) ? value : []



const totalEarnings = computed(() => {

  if (financial.value?.earnings !== undefined) {
    return Number(financial.value.earnings).toFixed(2)
  }


  return safe(performance.value?.earnings)
    .reduce(
      (total, item) =>
        total + Number(item.gross_usd || 0),
      0
    )
    .toFixed(2)

})



const totalBonuses = computed(() => {

  if (financial.value?.bonuses !== undefined) {
    return Number(financial.value.bonuses).toFixed(2)
  }


  return safe(performance.value?.bonuses)
    .reduce(
      (total, item) =>
        total + Number(item.amount || 0),
      0
    )
    .toFixed(2)

})



const totalPenalties = computed(() => {

  if (financial.value?.penalties !== undefined) {
    return Number(financial.value.penalties).toFixed(2)
  }


  return safe(performance.value?.penalties)
    .reduce(
      (total, item) =>
        total + Number(item.amount || 0),
      0
    )
    .toFixed(2)

})



const totalDeductions = computed(() => {

  if (financial.value?.deductions !== undefined) {
    return Number(financial.value.deductions).toFixed(2)
  }


  return safe(performance.value?.deductions)
    .reduce(
      (total, item) =>
        total + Number(item.amount || 0),
      0
    )
    .toFixed(2)

})



const netTotal = computed(() => {

  if (financial.value?.net !== undefined) {
    return Number(financial.value.net).toFixed(2)
  }


  return (
    Number(totalEarnings.value) +
    Number(totalBonuses.value) -
    Number(totalPenalties.value) -
    Number(totalDeductions.value)
  )
  .toFixed(2)

})



const load = async () => {

  try {

    const response = await api.get(
      `/performances/${route.params.id}`
    )


    console.log(
      'PERFORMANCE API',
      response.data
    )


    performance.value =
      response.data.data ?? null


    financial.value =
      response.data.financial ?? null


  } catch (error) {

    console.error(error)

    performance.value = null

    financial.value = null

  }

}



onMounted(load)

</script>


<style scoped>

</style>