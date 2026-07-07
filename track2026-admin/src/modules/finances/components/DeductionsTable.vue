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
    "
  >

    <div
      class="
        flex
        items-center
        justify-between
        px-8
        py-6
        border-b
        border-gray-800
      "
    >

      <div>

        <p
          class="
            text-gray-500
            text-xs
            uppercase
            tracking-[0.2em]
          "
        >
          Finanzas
        </p>

        <h2
          class="
            text-2xl
            font-bold
            text-white
            mt-2
          "
        >
          Historial de Descuentos
        </h2>

        <p
          class="
            text-gray-400
            text-sm
            mt-2
          "
        >
          Gastos y descuentos aplicados al modelo
        </p>

      </div>

      <div
        class="
          hidden
          lg:flex
          items-center
          gap-2
          px-4
          py-2
          rounded-2xl
          bg-amber-500/10
          border
          border-amber-500/20
        "
      >

        <span
          class="w-2 h-2 rounded-full bg-amber-400"
        />

        <span
          class="
            text-amber-400
            text-sm
            font-medium
          "
        >
          Descuentos
        </span>

      </div>

    </div>

    <DataTable
      :columns="columns"
      :items="normalizedDeductions"
    >

      <template #date="{ item }">

        <div class="flex flex-col">

          <span
            class="
              text-white
              font-medium
            "
          >
            {{ formatDate(item.date) }}
          </span>

          <span
            class="
              text-xs
              text-gray-500
              mt-1
            "
          >
            Descuento registrado
          </span>

        </div>

      </template>

      <template #reason="{ item }">

        <span
          class="
            text-gray-300
            font-medium
          "
        >
          {{ item.reason || 'Sin descripción' }}
        </span>

      </template>

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
              bg-amber-500/10
              border
              border-amber-500/20
              text-amber-400
              font-semibold
            "
          >

            <span
              class="w-2 h-2 rounded-full bg-amber-400"
            />

            ${{ formatAmount(item.amount) }}

          </span>

        </div>

      </template>

    </DataTable>

    <div
      v-if="!normalizedDeductions.length"
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
          bg-gray-800
          flex
          items-center
          justify-center
          text-3xl
        "
      >
        💳
      </div>

      <h3
        class="
          text-white
          text-xl
          font-semibold
          mt-6
        "
      >
        Sin descuentos registrados
      </h3>

      <p
        class="
          text-gray-400
          mt-2
        "
      >
        No existen descuentos asociados a este modelo.
      </p>

    </div>

  </div>

</template>

<script setup>

import { computed } from 'vue'
import DataTable from '@/components/ui/DataTable.vue'

const props = defineProps({
  deductions: {
    type: Array,
    default: () => []
  }
})

const columns = [
  {
    key: 'date',
    label: 'Fecha'
  },
  {
    key: 'reason',
    label: 'Motivo'
  },
  {
    key: 'amount',
    label: 'Monto'
  }
]

const normalizedDeductions = computed(() =>
  (props.deductions || []).map((deduction, index) => ({
    id: deduction.id ?? `deduction-${index}`,
    date: deduction.date,
    reason: deduction.reason,
    amount: deduction.amount ?? 0
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

  if (!value) {
    return '-'
  }

  return new Date(value).toLocaleDateString(
    'es-CO',
    {
      day: '2-digit',
      month: 'short',
      year: 'numeric'
    }
  )
}

</script>