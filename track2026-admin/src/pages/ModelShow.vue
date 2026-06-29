<template>

  <div
    v-if="performance"
    class="space-y-10"
  >

    <ModelHeader
      :model="performance"
    />

    <ModelKpis
      :model="performance"
    />

    <section class="space-y-4">

      <h2 class="text-gray-400 text-xs uppercase tracking-[0.25em]">
        Finanzas
      </h2>

      <FinancialSummary
        :financial="financial"
      />

    </section>

    <section class="space-y-4">

      <h2 class="text-gray-400 text-xs uppercase tracking-[0.25em]">
        Historial financiero
      </h2>

      <FinancialHistory
        :earnings="performance.earnings"
        :bonuses="performance.bonuses"
        :penalties="performance.penalties"
        :deductions="performance.deductions"
      />

    </section>

    <section>

      <details
        class="
          bg-gradient-to-br
          from-gray-900
          to-gray-950
          border
          border-gray-800
          rounded-3xl
          p-6
        "
      >

        <summary
          class="
            cursor-pointer
            text-white
            font-semibold
            text-lg
          "
        >
          Información del perfil
        </summary>


        <div
          class="
            mt-6
            grid
            grid-cols-1
            lg:grid-cols-3
            gap-6
          "
        >

          <ModelPersonalInfo
            :model="performance"
          />

          <ModelDocuments
            :model="performance"
          />

          <ModelStudioInfo
            :model="performance"
          />

        </div>


      </details>

    </section>


  </div>


  <div
    v-else
    class="
      flex
      justify-center
      items-center
      h-96
      text-gray-400
    "
  >

    Cargando...

  </div>

</template>

<script setup>

import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

import api from '../services/api'

import ModelHeader from '../components/models/ModelHeader.vue'
import ModelKpis from '../components/models/ModelKpis.vue'

import FinancialSummary from '../components/models/finances/FinancialSummary.vue'
import FinancialHistory from '../components/models/finances/FinancialHistory.vue'

import ModelPersonalInfo from '../components/models/ModelPersonalInfo.vue'
import ModelDocuments from '../components/models/ModelDocuments.vue'
import ModelStudioInfo from '../components/models/ModelStudioInfo.vue'

const route = useRoute()

const performance = ref(null)

const financial = ref(null)

const load = async () => {

  try {

    const { data } = await api.get(
      `/performances/${route.params.id}`
    )

    performance.value = data.data

    financial.value = data.financial

  } catch(error) {

    console.error(error)

  }

}

onMounted(load)

</script>