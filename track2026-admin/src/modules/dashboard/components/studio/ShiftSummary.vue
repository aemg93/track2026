<template>
  <div class="rounded-xl border border-gray-800 bg-gray-900 p-6">

    <h3 class="mb-6 text-lg font-semibold text-white">
      Resumen del turno
    </h3>

    <div class="grid gap-4 md:grid-cols-2">

      <div>
        <p class="text-sm text-gray-400">
          Ganancias
        </p>

        <h2 class="mt-1 text-2xl font-bold text-emerald-400">
          ${{ financial.gross_usd.toFixed(2) }}
        </h2>
      </div>

      <div>
        <p class="text-sm text-gray-400">
          Bonos
        </p>

        <h2 class="mt-1 text-2xl font-bold text-blue-400">
          ${{ financial.bonus_usd.toFixed(2) }}
        </h2>
      </div>

      <div>
        <p class="text-sm text-gray-400">
          Penalizaciones
        </p>

        <h2 class="mt-1 text-2xl font-bold text-yellow-400">
          ${{ financial.penalty_usd.toFixed(2) }}
        </h2>
      </div>

      <div>
        <p class="text-sm text-gray-400">
          Descuentos
        </p>

        <h2 class="mt-1 text-2xl font-bold text-red-400">
          ${{ financial.deduction_usd.toFixed(2) }}
        </h2>
      </div>

      <div class="md:col-span-2 border-t border-gray-800 pt-4">
        <p class="text-sm text-gray-400">
          Neto
        </p>

        <h2 class="mt-1 text-3xl font-bold text-white">
          ${{ financial.net_usd.toFixed(2) }}
        </h2>
      </div>

    </div>

  </div>
</template>

<script setup>
import { computed } from 'vue'

defineOptions({
  name: 'ShiftSummary',
})

const props = defineProps({
  performance: {
    type: Object,
    default: null,
  },
})

const financial = computed(() => {
  const source = props.performance?.financial ?? {}

  return {
    gross_usd: Number(source.gross_usd ?? 0),
    bonus_usd: Number(source.bonus_usd ?? 0),
    penalty_usd: Number(source.penalty_usd ?? 0),
    deduction_usd: Number(source.deduction_usd ?? 0),
    net_usd: Number(source.net_usd ?? 0),
  }
})
</script>