```vue
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
      <OperationalShiftHistory
        :performances="performances"
        :shifts="activeShifts"
      />
    </div>

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

    <ShiftTokenBreakdown
      v-if="showTokenBreakdown"
      :shift="tokenShift"
      @close="closeTokenBreakdown"
    />

    <ShiftEarningBreakdown
      v-if="showEarningBreakdown"
      :shift="earningShift"
      @close="closeEarningBreakdown"
    />
  </section>
</template>

<script setup>
import ShiftToolbar from '@/modules/operations/components/studio/ShiftToolbar.vue'
import OperationalShiftHistory from '@/modules/operations/components/OperationalShiftHistory.vue'
import ShiftPerformanceList from '@/modules/operations/components/studio/ShiftPerformanceList.vue'
import ShiftWorkspace from '@/modules/operations/components/studio/ShiftWorkspace.vue'
import ShiftTokenBreakdown from '@/modules/operations/components/studio/financial/ShiftTokenBreakdown.vue'
import ShiftEarningBreakdown from '@/modules/operations/components/studio/financial/ShiftEarningBreakdown.vue'

import { useStudioPanel } from '@/modules/operations/composables/useStudioPanel'

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
```

**No cambiaría nada más en este componente.** La corrección real está en `ShiftFinancialSummaryService`, porque ahí es donde estamos construyendo:

```text
financial_summary
    total_usd
    total_tokens
    platforms
```

Y actualmente `total_tokens` está sumando `original_amount`, mientras que tú quieres:

```text
Camila
Chaturbate  → 200 real_tokens
Stripchat   → 700 real_tokens
Cam4        → 300 real_tokens
LoyalFans   → 0 real_tokens

TOTAL       → 1.200 tokens
```

Es decir, **la tarjeta del Workspace debe consumir `real_tokens`**, no `original_amount`.

Ese es el siguiente archivo que debemos corregir.
