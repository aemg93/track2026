<template>
  <section class="space-y-4">

    <header class="flex items-center justify-between">

      <div>
        <h2 class="text-lg font-semibold text-white">
          Turnos activos
        </h2>

        <p class="mt-1 text-sm text-gray-400">
          Selecciona un turno para administrar la transmisión.
        </p>
      </div>

      <span
        v-if="shifts.length"
        class="rounded-full border border-gray-700 bg-gray-800 px-3 py-1 text-xs font-semibold text-gray-300"
      >
        {{ shifts.length }}
        {{ shifts.length === 1 ? 'activo' : 'activos' }}
      </span>

    </header>

    <div
      v-if="shifts.length"
      class="grid gap-4 md:grid-cols-2 xl:grid-cols-4"
    >
      <ShiftPerformanceItem
        v-for="shift in shifts"
        :key="shift.id"
        :shift="shift"
        :selected="selectedShift?.id === shift.id"
        @select="handleSelect"
      />
    </div>

    <div
      v-else
      class="
        flex
        min-h-[180px]
        items-center
        justify-center
        rounded-2xl
        border
        border-dashed
        border-gray-700
        bg-gray-900/40
      "
    >
      <div class="text-center">

        <div
          class="
            mx-auto
            mb-4
            flex
            h-12
            w-12
            items-center
            justify-center
            rounded-full
            bg-gray-800
            text-gray-500
        "
        >
          ●
        </div>

        <h3 class="text-base font-semibold text-gray-200">
          No hay turnos activos
        </h3>

        <p class="mt-2 max-w-sm text-sm text-gray-500">
          Cuando una modelo inicie un turno aparecerá aquí.
        </p>

      </div>

    </div>

  </section>
</template>

<script setup>
import ShiftPerformanceItem from './ShiftPerformanceItem.vue'

defineOptions({
  name: 'ShiftPerformanceList',
})

const props = defineProps({
  shifts: {
    type: Array,
    default: () => [],
  },

  selectedShift: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits([
  'select',
])

function handleSelect(shift) {
  emit('select', shift)
}
</script>