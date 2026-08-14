<template>
  <div class="space-y-10">
    <!-- LOADING -->
    <div
      v-if="loading"
      class="
        flex
        h-96
        items-center
        justify-center
        text-gray-400
      "
    >
      Cargando información del modelo...
    </div>

    <!-- ERROR -->
    <div
      v-else-if="error"
      class="
        rounded-3xl
        border
        border-red-500/20
        bg-red-500/10
        p-6
        text-red-400
      "
    >
      {{ error }}
    </div>

    <!-- CONTENT -->
    <template v-else-if="performance">
      <ModelHeader
        :model="performance"
      />

      <ModelKpis
        :model="performance"
      />

      <!-- FINANZAS -->
      <section class="space-y-4">
        <h2
          class="
            text-xs
            uppercase
            tracking-[0.25em]
            text-gray-400
          "
        >
          Finanzas
        </h2>

        <FinancialSummary
          :financial="financial"
        />
      </section>

      <!-- HISTORIAL FINANCIERO -->
      <section class="space-y-4">
        <h2
          class="
            text-xs
            uppercase
            tracking-[0.25em]
            text-gray-400
          "
        >
          Historial financiero
        </h2>

        <FinancialHistory
          :earnings="performance.earnings"
          :bonuses="performance.bonuses"
          :penalties="performance.penalties"
          :deductions="performance.deductions"
        />
      </section>

      <!-- INFORMACIÓN DEL PERFIL -->
      <section>
        <details
          class="
            rounded-3xl
            border
            border-gray-800
            bg-gradient-to-br
            from-gray-900
            to-gray-950
            p-6
          "
        >
          <summary
            class="
              cursor-pointer
              text-lg
              font-semibold
              text-white
            "
          >
            Información del perfil
          </summary>

          <div
            class="
              mt-6
              grid
              grid-cols-1
              gap-6
              lg:grid-cols-3
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
    </template>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'

import api from '@/services/api'

import ModelHeader from '@/modules/performances/components/ModelHeader.vue'
import ModelKpis from '@/modules/performances/components/ModelKpis.vue'

import FinancialSummary from '@/modules/finances/components/FinancialSummary.vue'
import FinancialHistory from '@/modules/finances/components/FinancialHistory.vue'

import ModelPersonalInfo from '@/modules/performances/components/ModelPersonalInfo.vue'
import ModelDocuments from '@/modules/performances/components/ModelDocuments.vue'
import ModelStudioInfo from '@/modules/performances/components/ModelStudioInfo.vue'

const route = useRoute()

const performance = ref(null)
const financial = ref(null)

const loading = ref(true)
const error = ref(null)

const load = async () => {
  loading.value = true
  error.value = null

  try {
    const performanceId = route.params.id

    if (!performanceId) {
      throw new Error(
        'No se encontró el ID del modelo.'
      )
    }

    const response = await api.get(
      `/performances/${performanceId}`
    )

    const payload = response.data

    if (!payload?.success) {
      throw new Error(
        payload?.message ||
        'No fue posible cargar la información del modelo.'
      )
    }

    performance.value =
      payload.data ?? null

    financial.value =
      payload.financial ?? null

    if (!performance.value) {
      throw new Error(
        'La API no devolvió información del modelo.'
      )
    }
  } catch (err) {
    console.error(
      'Error loading performance:',
      err
    )

    error.value =
      err?.response?.data?.message ||
      err?.message ||
      'No fue posible cargar la información del modelo.'

    performance.value = null
    financial.value = null
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

