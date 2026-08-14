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
    <!-- HEADER -->

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
            text-xs
            uppercase
            tracking-[0.25em]
            text-gray-500
          "
        >
          Finanzas
        </p>

        <h2 class="mt-2 text-3xl font-bold text-white">
          Historial de Bonos
        </h2>

        <p class="mt-2 text-sm text-gray-400">
          Bonificaciones registradas para el modelo
        </p>
      </div>

      <div
        class="
          hidden
          lg:flex
          items-center
          gap-2
          rounded-2xl
          border
          border-green-500/20
          bg-green-500/10
          px-4
          py-2
        "
      >
        <span class="h-2 w-2 rounded-full bg-green-400" />

        <span class="text-sm font-medium text-green-400">
          Bonificaciones
        </span>
      </div>
    </div>

    <!-- TABLE -->

    <DataTable
      :columns="columns"
      :items="normalizedBonuses"
    >
      <template #date="{ item }">
        <div class="flex flex-col">
          <span class="font-medium text-white">
            {{ formatDate(item.date) }}
          </span>

          <span class="mt-1 text-xs text-gray-500">
            Movimiento registrado
          </span>
        </div>
      </template>

      <template #reason="{ item }">
        <p class="font-medium text-gray-300">
          {{ item.reason || 'Sin descripción' }}
        </p>
      </template>

      <template #amount="{ item }">
        <div class="flex justify-end">
          <span
            class="
              inline-flex
              items-center
              gap-2
              rounded-2xl
              border
              border-green-500/20
              bg-green-500/10
              px-4
              py-2
              font-semibold
              text-green-400
            "
          >
            <span class="h-2 w-2 rounded-full bg-green-400" />

            +${{ formatAmount(item.amount) }}
          </span>
        </div>
      </template>
    </DataTable>

    <!-- EMPTY -->

    <div
      v-if="!normalizedBonuses.length"
      class="
        border-t
        border-gray-800
        p-12
        text-center
      "
    >
      <div
        class="
          mx-auto
          flex
          h-20
          w-20
          items-center
          justify-center
          rounded-3xl
          border
          border-gray-800
          bg-black/20
          text-3xl
        "
      >
        🎁
      </div>

      <h3 class="mt-6 text-xl font-semibold text-white">
        Sin bonos registrados
      </h3>

      <p class="mt-2 text-gray-400">
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

const normalizedBonuses = computed(() =>
  (props.bonuses || []).map((bonus, index) => ({
    id: bonus.id ?? `bonus-${index}`,
    date: bonus.date,
    reason: bonus.reason,
    amount: Number(bonus.amount ?? 0)
  }))
)

const formatAmount = (value) => {
  return Number(value || 0).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

const formatDate = (value) => {
  if (!value) {
    return '-'
  }

  return new Intl.DateTimeFormat('es-CO', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  }).format(new Date(value))
}
</script>