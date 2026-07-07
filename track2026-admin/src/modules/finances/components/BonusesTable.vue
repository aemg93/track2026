<template>

  <div
    class="
      bg-gradient-to-br
      from-gray-900
      to-gray-950
      border
      border-gray-800
      rounded-3xl
      overflow-hidden
      shadow-xl
      shadow-black/20
    "
  >

    <!-- HEADER -->

    <div
      class="
        px-8
        py-6
        border-b
        border-gray-800
        flex
        items-center
        justify-between
      "
    >

      <div>

        <p
          class="
            text-gray-500
            text-xs
            uppercase
            tracking-[0.25em]
          "
        >
          Finanzas
        </p>

        <h2
          class="
            text-3xl
            font-bold
            text-white
            mt-2
          "
        >
          Historial de Bonos
        </h2>

        <p
          class="
            text-gray-400
            text-sm
            mt-2
          "
        >
          Bonificaciones registradas para el modelo
        </p>

      </div>

      <!-- BADGE -->

      <div
        class="
          hidden
          lg:flex
          items-center
          gap-2
          px-4
          py-2
          rounded-2xl
          bg-green-500/10
          border
          border-green-500/20
        "
      >

        <span class="w-2 h-2 rounded-full bg-green-400" />

        <span class="text-green-400 text-sm font-medium">
          Bonificaciones
        </span>

      </div>

    </div>

    <!-- TABLE -->

    <DataTable
      :columns="columns"
      :items="normalizedBonuses"
    >

      <!-- DATE -->

      <template #date="{ item }">

        <div class="flex flex-col">

          <span class="text-white font-medium">
            {{ formatDate(item.date) }}
          </span>

          <span class="text-xs text-gray-500 mt-1">
            Movimiento registrado
          </span>

        </div>

      </template>

      <!-- REASON -->

      <template #reason="{ item }">

        <p class="text-gray-300 font-medium">
          {{ item.reason || 'Sin descripción' }}
        </p>

      </template>

      <!-- AMOUNT -->

      <template #amount="{ item }">

        <div class="flex justify-end">

          <span
            class="
              inline-flex
              items-center
              gap-2
              px-4
              py-2
              rounded-2xl
              bg-green-500/10
              border
              border-green-500/20
              text-green-400
              font-semibold
            "
          >

            <span class="w-2 h-2 rounded-full bg-green-400" />

            +${{ formatAmount(item.amount) }}

          </span>

        </div>

      </template>

    </DataTable>

    <!-- EMPTY -->

    <div
      v-if="!normalizedBonuses.length"
      class="
        p-12
        text-center
        border-t
        border-gray-800
      "
    >

      <div
        class="
          w-20
          h-20
          mx-auto
          rounded-3xl
          bg-black/20
          border
          border-gray-800
          flex
          items-center
          justify-center
          text-3xl
        "
      >
        🎁
      </div>

      <h3 class="text-white text-xl font-semibold mt-6">
        Sin bonos registrados
      </h3>

      <p class="text-gray-400 mt-2">
        No existen bonificaciones para este modelo.
      </p>

    </div>

  </div>

</template>

<script setup>

import { computed } from 'vue'
import DataTable from '@/components/ui/DataTable.vue'

const props = defineProps({
  bonuses: {
    type: Array,
    default: () => []
  }
})

const columns = [
  { key: 'date', label: 'Fecha' },
  { key: 'reason', label: 'Motivo' },
  { key: 'amount', label: 'Monto' }
]

const normalizedBonuses = computed(() =>
  (props.bonuses || []).map((bonus, index) => ({
    id: bonus.id ?? `bonus-${index}`,
    date: bonus.date,
    reason: bonus.reason,
    amount: bonus.amount ?? 0
  }))
)

const formatAmount = (value) => {
  const amount = Number(value || 0)

  return amount.toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

const formatDate = (value) => {
  if (!value) return '-'

  return new Date(value).toLocaleDateString('es-CO', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

</script>