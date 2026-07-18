<template>
  <section
    class="rounded-3xl border border-gray-800 bg-gradient-to-br from-gray-900 to-gray-950 p-6"
  >
    <ShiftToolbar
      :performances="performances"
      @start-shift="startShift"
    />

    <div class="mt-6">
      <ShiftPerformanceList
        :shifts="activeShifts"
        :selected-shift="selectedShift"
        @select="selectShift"
      />
    </div>

    <div class="mt-8">
      <ShiftWorkspace
        :selected-shift="selectedShift"
        @earning="registerEarning"
        @bonus="registerBonus"
        @penalty="registerPenalty"
        @deduction="registerDeduction"
        @pause="pauseCurrentShift"
        @resume="resumeCurrentShift"
        @finish="finishCurrentShift"
      />
    </div>
  </section>
</template>

<script setup>
import {
  computed,
  ref,
  watch,
} from 'vue'

import ShiftToolbar from './studio/ShiftToolbar.vue'
import ShiftPerformanceList from './studio/ShiftPerformanceList.vue'
import ShiftWorkspace from './studio/ShiftWorkspace.vue'

defineOptions({
  name: 'StudioPanel',
})

const props = defineProps({
  dashboard: {
    type: Object,
    required: true,
  },

  finance: {
    type: Object,
    required: true,
  },

  operations: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits([
  'start-shift',
  'earning',
  'bonus',
  'penalty',
  'deduction',
  'pause',
  'resume',
  'finish',
])

const selectedShift = ref(null)

const performances = computed(
  () => props.dashboard?.performances ?? props.dashboard?.models ?? []
)

const activeShifts = computed(
  () => props.operations?.active_shifts ?? []
)

function startShift(performance) {
  emit('start-shift', performance)
}

/**
 * Seleccionar / deseleccionar turno
 */
function selectShift(shift) {

  if (selectedShift.value?.id === shift.id) {
    selectedShift.value = null
    return
  }

  selectedShift.value = shift
}

function registerEarning(performance) {
  emit('earning', performance)
}

function registerBonus(performance) {
  emit('bonus', performance)
}

function registerPenalty(performance) {
  emit('penalty', performance)
}

function registerDeduction(performance) {
  emit('deduction', performance)
}

function pauseCurrentShift(shift) {
  emit('pause', shift)
}

function resumeCurrentShift(shift) {
  emit('resume', shift)
}

function finishCurrentShift(shift) {
  if (shift?.id) {
    emit('finish', shift)
  }
}

/**
 * Mantiene sincronizado el turno seleccionado
 * cuando llega nueva información desde el backend.
 */
watch(
  activeShifts,
  shifts => {

    if (!selectedShift.value) {
      return
    }

    const current = shifts.find(
      item => item.id === selectedShift.value.id
    )

    selectedShift.value = current ?? null
  },
  {
    immediate: true,
  }
)
</script>