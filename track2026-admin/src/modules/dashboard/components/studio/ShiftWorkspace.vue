<template>
  <ShiftEmptyState
    v-if="!selectedShift"
  />

  <div
    v-else
    class="grid gap-5 xl:grid-cols-[320px_minmax(0,1fr)]"
  >
    <!-- Panel lateral -->
    <aside class="space-y-5">

      <ShiftSummary
        :shift="selectedShift"
      />

      <ShiftHeader
        :shift="selectedShift"
        @pause="handlePause"
        @resume="handleResume"
      />

      <ShiftActions
        :performance="selectedPerformance"
        @earning="handleEarning"
        @bonus="handleBonus"
        @penalty="handlePenalty"
        @deduction="handleDeduction"
        @finish="handleFinish"
      />

    </aside>

    <!-- Actividad -->
    <section
      class="min-h-[650px] rounded-2xl border border-gray-800 bg-gray-900"
    >
      <div class="border-b border-gray-800 px-6 py-4">

        <h2 class="text-base font-semibold text-white">
          Actividad reciente
        </h2>

        <p class="mt-1 text-sm text-gray-400">
          Movimientos financieros realizados durante este turno.
        </p>

      </div>

      <div class="h-[590px] overflow-y-auto">

        <ShiftTimeline
          :timeline="timeline"
        />

      </div>

    </section>

  </div>
</template>

<script setup>
import {
  computed,
  toRefs,
} from 'vue'

import ShiftActions from './ShiftActions.vue'
import ShiftEmptyState from './ShiftEmptyState.vue'
import ShiftHeader from './ShiftHeader.vue'
import ShiftSummary from './ShiftSummary.vue'
import ShiftTimeline from './ShiftTimeline.vue'

defineOptions({
  name: 'ShiftWorkspace',
})

const props = defineProps({
  selectedShift: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits([
  'earning',
  'bonus',
  'penalty',
  'deduction',
  'pause',
  'resume',
  'finish',
])

const { selectedShift } = toRefs(props)

const selectedPerformance = computed(
  () => selectedShift.value?.performance ?? null
)

const timeline = computed(
  () => selectedShift.value?.timeline ?? []
)

function handleEarning() {
  if (!selectedPerformance.value) {
    return
  }

  emit('earning', selectedPerformance.value)
}

function handleBonus() {
  if (!selectedPerformance.value) {
    return
  }

  emit('bonus', selectedPerformance.value)
}

function handlePenalty() {
  if (!selectedPerformance.value) {
    return
  }

  emit('penalty', selectedPerformance.value)
}

function handleDeduction() {
  if (!selectedPerformance.value) {
    return
  }

  emit('deduction', selectedPerformance.value)
}

function handlePause() {
  if (!selectedShift.value) {
    return
  }

  emit('pause', selectedShift.value)
}

function handleResume() {
  if (!selectedShift.value) {
    return
  }

  emit('resume', selectedShift.value)
}

function handleFinish() {
  if (!selectedShift.value) {
    return
  }

  emit('finish', selectedShift.value)
}
</script>