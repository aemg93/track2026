<template>
  <div
    v-if="shifts.length"
    class="grid gap-4 md:grid-cols-2 xl:grid-cols-4"
  >
    <ShiftPerformanceItem
  v-for="shift in shifts"
  :key="shift.id"
  :shift="shift"
  :selected="selectedShift?.id === shift.id"
  @select="selectShift"
/>
  </div>

  <div
    v-else
    class="rounded-2xl border border-dashed border-gray-700 bg-gray-900/40 py-12 text-center"
  >
    <p class="text-gray-400">
      No hay turnos activos.
    </p>
  </div>
</template>

<script setup>
import { toRefs } from 'vue'

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

const { shifts, selectedShift } = toRefs(props)

const emit = defineEmits([
  'select',
])

const selectShift = shift => emit('select', shift)
</script>