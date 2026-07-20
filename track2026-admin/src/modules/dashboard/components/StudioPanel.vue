<template>
  <section
    class="
      rounded-3xl
      border
      border-gray-800
      bg-gradient-to-br
      from-gray-900
      to-gray-950
      p-6
    "
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
        @earnings="openEarnings"
        @bonus="registerBonus"
        @penalty="registerPenalty"
        @deduction="registerDeduction"
        @tokens="openTokens"
        @pause="pauseCurrentShift"
        @resume="resumeCurrentShift"
        @finish="finishCurrentShift"
      />

    </div>



    <!-- Modal tokens -->
    <ShiftTokenBreakdown
      v-if="showTokenBreakdown"
      :shift="tokenShift"
      @close="closeTokenBreakdown"
    />



    <!-- Modal ganancias USD -->
    <ShiftEarningBreakdown
      v-if="showEarningBreakdown"
      :shift="earningShift"
      @close="closeEarningBreakdown"
    />


  </section>
</template>


<script setup>

import ShiftToolbar from '@/modules/dashboard/components/studio/ShiftToolbar.vue'
import ShiftPerformanceList from '@/modules/dashboard/components/studio/ShiftPerformanceList.vue'
import ShiftWorkspace from '@/modules/dashboard/components/studio/ShiftWorkspace.vue'

import ShiftTokenBreakdown from '@/modules/dashboard/components/studio/financial/ShiftTokenBreakdown.vue'
import ShiftEarningBreakdown from '@/modules/dashboard/components/studio/financial/ShiftEarningBreakdown.vue'


import {
  useStudioPanel,
} from '@/composables/useStudioPanel'



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



const {

  selectedShift,


  tokenShift,
  showTokenBreakdown,


  earningShift,
  showEarningBreakdown,


  performances,

  activeShifts,


  startShift,

  selectShift,


  registerEarning,

  registerBonus,

  registerPenalty,

  registerDeduction,


  pauseCurrentShift,

  resumeCurrentShift,

  finishCurrentShift,


  openTokens,

  closeTokenBreakdown,


  openEarnings,

  closeEarningBreakdown,


} = useStudioPanel(
  props,
  emit
)


</script>