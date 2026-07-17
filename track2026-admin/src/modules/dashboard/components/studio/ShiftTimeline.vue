<template>
  <div class="rounded-xl border border-gray-800 bg-gray-900 p-6">

    <h3 class="mb-6 text-lg font-semibold text-white">
      Actividad reciente
    </h3>

    <div
      v-if="timeline.length"
      class="space-y-4"
    >

      <div
        v-for="item in timeline"
        :key="item.id ?? `${item.type}-${item.date}`"
        class="flex items-center justify-between border-b border-gray-800 pb-4 last:border-b-0 last:pb-0"
      >

        <div class="min-w-0 flex-1">

          <p class="font-medium text-white">
            {{ item.title }}
          </p>

          <p
            v-if="item.description"
            class="mt-1 text-sm font-medium text-cyan-400"
          >
            {{ item.description }}
          </p>

          <p
            v-if="item.performed_by"
            class="mt-1 text-xs text-gray-500"
          >
            Registrado por {{ item.performed_by }}
          </p>

          <p class="mt-1 text-sm text-gray-500">

            {{ formatType(item.type) }}

            <span v-if="formatTime(item)">
              • {{ formatTime(item) }}
            </span>

          </p>

        </div>

        <div class="ml-6 text-right">

          <p
            class="font-semibold"
            :class="amountClass(item)"
          >
            {{ formatAmount(item.amount) }}
          </p>

        </div>

      </div>

    </div>

    <div
      v-else
      class="py-10 text-center text-gray-500"
    >
      No existen movimientos registrados.
    </div>

  </div>
</template>

<script setup>
import { toRefs } from 'vue'

defineOptions({
  name: 'ShiftTimeline',
})

const props = defineProps({

  timeline: {
    type: Array,
    default: () => [],
  },

})

const { timeline } = toRefs(props)

function formatAmount(value) {

  const amount = Number(value ?? 0)

  return amount.toLocaleString('es-CO', {

    style: 'currency',

    currency: 'USD',

    minimumFractionDigits: 2,

  })

}

function formatType(type) {

  const types = {

    earning: 'Ganancia',

    bonus: 'Bono',

    penalty: 'Penalización',

    deduction: 'Descuento',

  }

  return types[type] ?? type

}

function formatTime(item) {

  if (item.time) {
    return item.time
  }

  if (!item.date) {
    return null
  }

  return new Date(item.date)
    .toLocaleTimeString('es-CO', {
      hour: '2-digit',
      minute: '2-digit',
    })

}

function amountClass(item) {

  return item.type === 'penalty' ||
         item.type === 'deduction'
    ? 'text-red-400'
    : 'text-emerald-400'

}
</script>