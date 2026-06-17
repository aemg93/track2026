<template>

  <div v-if="performance" class="space-y-10">

    <!-- HEADER -->
    <ModelHeader :model="performance" />

    <!-- KPIS -->
    <ModelKpis :model="performance" />

    <!-- FINANZAS -->
    <section class="space-y-4">

      <div>
        <h2 class="text-gray-400 text-xs uppercase tracking-[0.25em]">
          Finanzas
        </h2>
      </div>

      <ModelFinancialSummary
        :earnings="totalEarnings"
        :bonuses="totalBonuses"
        :penalties="totalPenalties"
        :net="netTotal"
      />

    </section>

    <!-- HISTORIAL -->
    <section class="space-y-4">

      <div class="flex items-center justify-between">

        <h2 class="text-gray-400 text-xs uppercase tracking-[0.25em]">
          Historial financiero
        </h2>

      </div>

      <div class="space-y-6">

        <EarningsTable :earnings="performance.earnings || []" />

        <BonusesTable :bonuses="performance.bonuses || []" />

        <PenaltiesTable :penalties="performance.penalties || []" />

      </div>

    </section>

    <!-- PERFIL -->
    <section class="space-y-4">

      <details
        class="bg-gradient-to-br from-gray-900 to-gray-950 border border-gray-800 rounded-3xl p-6"
      >

        <summary
          class="cursor-pointer text-white font-semibold text-lg"
        >
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

import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
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

const route = useRoute()

const performance = ref(null)

const safe = (value) =>
  Array.isArray(value) ? value : []

/*
|--------------------------------------------------------------------------
| GANANCIAS
|--------------------------------------------------------------------------
|
| Earning actualmente devuelve:
| gross_usd
| net_usd
| model_share_usd
|
| amount_usd NO EXISTE.
|
*/

const totalEarnings = computed(() =>
  safe(performance.value?.earnings)
    .reduce(
      (acc, e) =>
        acc + parseFloat(e.gross_usd || 0),
      0
    )
    .toFixed(2)
)

const totalBonuses = computed(() =>
  safe(performance.value?.bonuses)
    .reduce(
      (acc, b) =>
        acc + parseFloat(b.amount || 0),
      0
    )
    .toFixed(2)
)

const totalPenalties = computed(() =>
  safe(performance.value?.penalties)
    .reduce(
      (acc, p) =>
        acc + parseFloat(p.amount || 0),
      0
    )
    .toFixed(2)
)

const netTotal = computed(() =>
  (
    parseFloat(totalEarnings.value || 0) +
    parseFloat(totalBonuses.value || 0) -
    parseFloat(totalPenalties.value || 0)
  ).toFixed(2)
)

const load = async () => {

  try {

    const { data } = await api.get(
      `/performances/${route.params.id}`
    )

    console.log('PERFORMANCE API', data)

    performance.value = data.data

    console.log(
      'PERFORMANCE RAW',
      performance.value
    )

  } catch (error) {

    console.error(error)

    performance.value = null

  }

}

onMounted(load)

</script>