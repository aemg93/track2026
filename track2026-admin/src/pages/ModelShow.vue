```vue
<template>
  <div
    v-if="model"
    class="space-y-8"
  >

    <!-- HEADER -->
    <ModelHeader :model="model" />

    <!-- KPIS -->
    <ModelKpis :model="model" />

    <!-- INFORMACION GENERAL -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <ModelPersonalInfo
        :model="model"
      />

      <ModelDocuments
        :model="model"
      />

      <ModelStudioInfo
        :model="model"
      />

    </div>

    <!-- FINANZAS -->
    <ModelFinancialSummary
      :earnings="totalEarnings"
      :bonuses="totalBonuses"
      :penalties="totalPenalties"
      :net="netTotal"
    />

    <!-- HISTORIAL -->
    <EarningsTable
      :earnings="model.earnings || []"
    />

    <BonusesTable
      :bonuses="model.bonuses || []"
    />

    <PenaltiesTable
      :penalties="model.penalties || []"
    />

  </div>

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

/*
|--------------------------------------------------------------------------
| COMPONENTS
|--------------------------------------------------------------------------
*/

import ModelHeader from '../components/models/ModelHeader.vue'
import ModelKpis from '../components/models/ModelKpis.vue'

import ModelPersonalInfo from '../components/models/ModelPersonalInfo.vue'
import ModelDocuments from '../components/models/ModelDocuments.vue'
import ModelStudioInfo from '../components/models/ModelStudioInfo.vue'

import ModelFinancialSummary from '../components/models/ModelFinancialSummary.vue'

import EarningsTable from '../components/models/history/EarningsTable.vue'
import BonusesTable from '../components/models/history/BonusesTable.vue'
import PenaltiesTable from '../components/models/history/PenaltiesTable.vue'

/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const route = useRoute()
const model = ref(null)

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/

const safe = (value) =>
  Array.isArray(value) ? value : []

/*
|--------------------------------------------------------------------------
| FINANCIAL TOTALS
|--------------------------------------------------------------------------
*/

const totalEarnings = computed(() =>
  safe(model.value?.earnings)
    .reduce(
      (acc, earning) =>
        acc + parseFloat(
          earning.amount_usd ||
          earning.amount ||
          0
        ),
      0
    )
    .toFixed(2)
)

const totalBonuses = computed(() =>
  safe(model.value?.bonuses)
    .reduce(
      (acc, bonus) =>
        acc + parseFloat(
          bonus.amount || 0
        ),
      0
    )
    .toFixed(2)
)

const totalPenalties = computed(() =>
  safe(model.value?.penalties)
    .reduce(
      (acc, penalty) =>
        acc + parseFloat(
          penalty.amount || 0
        ),
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

/*
|--------------------------------------------------------------------------
| LOAD MODEL
|--------------------------------------------------------------------------
*/

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
```
