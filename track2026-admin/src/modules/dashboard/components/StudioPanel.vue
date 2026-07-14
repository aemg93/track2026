<template>
  <section class="rounded-3xl border border-gray-800 bg-gradient-to-br from-gray-900 to-gray-950 p-6">

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
  @finish="finishShift"
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

const performances = computed(() => {
  return (
    props.dashboard?.performances ??
    props.dashboard?.models ??
    []
  )
})

const activeShifts = computed(() => {
  return props.operations?.active_shifts ?? []
})

function startShift(performance) {
  emit('start-shift', performance)
}

function selectShift(shift) {
  console.log('SHIFT SELECCIONADO', shift)

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
  console.log('🟠 StudioPanel PAUSE', shift)

  emit('pause', shift)
}

function resumeCurrentShift(shift) {
  console.log('🟢 StudioPanel RESUME', shift)

  emit('resume', shift)
}

function finishShift(shift) {
  if (!shift?.id) {
    return
  }

  emit('finish', shift)
}

watch(
  activeShifts,
  shifts => {

    if (!selectedShift.value) {
      return
    }

    const updatedShift = shifts.find(
      item => item.id === selectedShift.value.id
    )

    if (!updatedShift) {
      selectedShift.value = null
      return
    }

    selectedShift.value = updatedShift

    console.log('🔄 Shift sincronizado')
    console.log(updatedShift)

  },
  {
    deep: true,
    immediate: true,
  }
)
</script>