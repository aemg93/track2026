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
        @financial-summary="openFinancialSummary"
      />

    </div>


    <!-- Resumen financiero del turno -->
    <ShiftFinancialSummary
      v-if="showFinancialSummary"
      :shift="financialShift"
      @close="closeFinancialSummary"
    />

  </section>
</template>
<script setup>

import {
  computed,
  ref,
  watch,
} from 'vue'


import ShiftToolbar from '@/modules/dashboard/components/studio/ShiftToolbar.vue'
import ShiftPerformanceList from '@/modules/dashboard/components/studio/ShiftPerformanceList.vue'
import ShiftWorkspace from '@/modules/dashboard/components/studio/ShiftWorkspace.vue'
import ShiftFinancialSummary from '@/modules/dashboard/components/studio/financial/ShiftFinancialSummary.vue'


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


const financialShift = ref(null)


const showFinancialSummary = ref(false)



const performances = computed(
  () =>
    props.dashboard?.performances ??
    props.dashboard?.models ??
    []
)



const activeShifts = computed(
  () =>
    props.operations?.active_shifts ??
    []
)



function startShift(performance) {

  emit(
    'start-shift',
    performance
  )

}



function selectShift(shift) {

  if (
    selectedShift.value?.id === shift.id
  ) {

    selectedShift.value = null

    return

  }


  selectedShift.value = shift

}



function registerEarning(performance) {

  emit(
    'earning',
    performance
  )

}



function registerBonus(performance) {

  emit(
    'bonus',
    performance
  )

}



function registerPenalty(performance) {

  emit(
    'penalty',
    performance
  )

}



function registerDeduction(performance) {

  emit(
    'deduction',
    performance
  )

}



function pauseCurrentShift(shift) {

  emit(
    'pause',
    shift
  )

}



function resumeCurrentShift(shift) {

  emit(
    'resume',
    shift
  )

}



function finishCurrentShift(shift) {

  if (!shift?.id) {
    return
  }


  emit(
    'finish',
    shift
  )

}



/**
 * Abrir resumen financiero del turno
 */
function openFinancialSummary(shift) {

  if (!shift) {
    return
  }


  financialShift.value = shift

  showFinancialSummary.value = true

}



/**
 * Cerrar resumen financiero
 */
function closeFinancialSummary() {

  financialShift.value = null

  showFinancialSummary.value = false

}



/**
 * Mantiene sincronizado el turno seleccionado
 * cuando llega nueva información desde backend.
 */
watch(

  activeShifts,

  shifts => {

    if (!selectedShift.value) {
      return
    }


    const current = shifts.find(
      item =>
        item.id === selectedShift.value.id
    )


    selectedShift.value =
      current ?? null

  },

  {
    immediate: true,
  }

)

</script>