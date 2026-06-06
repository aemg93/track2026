<template>

  <div v-if="model" class="space-y-10">

    <!-- 1. HEADER (IDENTIDAD + RANK + PROGRESO) -->
    <ModelHeader :model="model" />

    <!-- 2. KPIS -->
    <ModelKpis :model="model" />

    <!-- 3. FINANZAS (PRIORIDAD PRINCIPAL) -->
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

    <!-- 4. HISTORIAL FINANCIERO -->
    <section class="space-y-4">

      <div class="flex items-center justify-between">

        <h2 class="text-gray-400 text-xs uppercase tracking-[0.25em]">
          Historial financiero
        </h2>

      </div>

      <div class="space-y-6">

        <EarningsTable :earnings="model.earnings || []" />
        <BonusesTable :bonuses="model.bonuses || []" />
        <PenaltiesTable :penalties="model.penalties || []" />

      </div>

    </section>

    <!-- 5. PERFIL (COLAPSABLE / MENOS RUIDO VISUAL) -->
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

          <ModelPersonalInfo :model="model" />
          <ModelDocuments :model="model" />
          <ModelStudioInfo :model="model" />

        </div>

      </details>

    </section>

  </div>

  <!-- LOADING -->
  <div
    v-else
    class="flex justify-center items-center h-96 text-gray-400"
  >
    Cargando modelo...
  </div>

</template>

<script setup>

import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../services/api'

/* =======================
   COMPONENTS
======================= */

import ModelHeader from '../components/models/ModelHeader.vue'
import ModelKpis from '../components/models/ModelKpis.vue'

import ModelFinancialSummary from '../components/models/ModelFinancialSummary.vue'

import ModelPersonalInfo from '../components/models/ModelPersonalInfo.vue'
import ModelDocuments from '../components/models/ModelDocuments.vue'
import ModelStudioInfo from '../components/models/ModelStudioInfo.vue'

import EarningsTable from '../components/models/history/EarningsTable.vue'
import BonusesTable from '../components/models/history/BonusesTable.vue'
import PenaltiesTable from '../components/models/history/PenaltiesTable.vue'

/* =======================
   STATE
======================= */

const route = useRoute()
const model = ref(null)

/* =======================
   HELPERS
======================= */

const safe = (value) =>
  Array.isArray(value) ? value : []

/* =======================
   FINANCIAL CALCULATIONS
======================= */

const totalEarnings = computed(() =>
  safe(model.value?.earnings)
    .reduce((acc, e) =>
      acc + parseFloat(e.amount_usd || e.amount || 0),
      0
    )
    .toFixed(2)
)

const totalBonuses = computed(() =>
  safe(model.value?.bonuses)
    .reduce((acc, b) =>
      acc + parseFloat(b.amount || 0),
      0
    )
    .toFixed(2)
)

const totalPenalties = computed(() =>
  safe(model.value?.penalties)
    .reduce((acc, p) =>
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

/* =======================
   LOAD MODEL
======================= */

const load = async () => {
  try {

    const { data } = await api.get(
      `/models/${route.params.id}`
    )

    model.value = data.data

  } catch (error) {

    console.error(error)

    model.value = null
  }
}

onMounted(load)

</script>