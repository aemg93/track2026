<template>
  <div
    class="
      overflow-hidden
      rounded-3xl
      border
      border-gray-800
      bg-gradient-to-br
      from-gray-900
      to-gray-950
      shadow-xl
      shadow-black/20
    "
  >
    <div
      class="
        flex
        items-center
        justify-between
        border-b
        border-gray-800
        px-8
        py-6
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

        <h2
          class="
            mt-2
            text-2xl
            font-bold
            text-white
          "
        >
          Historial de Ganancias
        </h2>

        <p class="mt-2 text-sm text-gray-400">
          Registro histórico de ingresos generados por el modelo
        </p>
      </div>

      <div
        class="
          hidden
          items-center
          gap-2
          rounded-2xl
          border
          border-green-500/20
          bg-green-500/10
          px-4
          py-2
          lg:flex
        "
      >
        <span
          class="
            h-2
            w-2
            rounded-full
            bg-green-400
          "
        />

        <span
          class="
            text-sm
            font-medium
            text-green-400
          "
        >
          Historial Financiero
        </span>
      </div>
    </div>

    <DataTable
      :columns="columns"
      :items="normalizedEarnings"
    >
      <template #date="{ item }">
        <span
          class="
            whitespace-nowrap
            font-medium
            text-white
          "
        >
          {{ formatDate(item.date) }}
        </span>
      </template>

      <template #platform="{ item }">
        <div class="flex flex-col">
          <span
            class="
              font-semibold
              text-white
            "
          >
            {{ item.platform }}
          </span>

          <span
            class="
              mt-1
              text-xs
              uppercase
              tracking-wider
              text-gray-500
            "
          >
            {{ item.currency }}
          </span>
        </div>
      </template>

      <template #tokens="{ item }">
        <span
          v-if="item.tokens !== null"
          class="
            whitespace-nowrap
            font-semibold
            text-gray-200
          "
        >
          {{ formatTokens(item.tokens) }}
        </span>

        <span
          v-else
          class="text-gray-500"
        >
          —
        </span>
      </template>

      <template #usd="{ item }">
        <span
          class="
            whitespace-nowrap
            font-semibold
            text-green-400
          "
        >
          ${{ formatAmount(item.usd) }}
        </span>
      </template>
    </DataTable>

    <div
      v-if="!normalizedEarnings.length"
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
          bg-gray-800
          text-3xl
        "
      >
        💰
      </div>

      <h3
        class="
          mt-6
          text-xl
          font-semibold
          text-white
        "
      >
        Sin ganancias registradas
      </h3>

      <p class="mt-2 text-gray-400">
        No existen movimientos de ganancias para este modelo.
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

import DataTable from '@/components/ui/DataTable.vue'

const props = defineProps({
  earnings: {
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
    key: 'platform',
    label: 'Plataforma'
  },
  {
    key: 'tokens',
    label: 'Tokens',
    align: 'right'
  },
  {
    key: 'usd',
    label: 'USD',
    align: 'right'
  }
]

const normalizedEarnings = computed(() => {
  return (props.earnings || []).map(
    (earning, index) => ({
      id:
        earning.id ??
        `earning-${index}`,

      date:
        earning.earned_at,

      platform:
        earning.platform?.name ??
        'Sin plataforma',

      currency:
        earning.original_currency?.toUpperCase() ??
        'USD',

      tokens:
        earning.real_tokens !== null &&
        earning.real_tokens !== undefined
          ? Number(earning.real_tokens)
          : null,

      usd:
        Number(earning.gross_usd ?? 0)
    })
  )
})

const formatAmount = (value) => {
  return Number(value || 0).toLocaleString(
    'en-US',
    {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }
  )
}

const formatTokens = (value) => {
  return Number(value || 0).toLocaleString(
    'en-US',
    {
      maximumFractionDigits: 2
    }
  )
}

const formatDate = (value) => {
  if (!value) {
    return '-'
  }

  return new Intl.DateTimeFormat(
    'es-CO',
    {
      day: '2-digit',
      month: 'short',
      year: 'numeric'
    }
  ).format(new Date(value))
}
</script>