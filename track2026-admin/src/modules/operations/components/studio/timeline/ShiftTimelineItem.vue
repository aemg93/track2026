<template>
  <div
    class="
      relative
      overflow-hidden
      rounded-2xl
      border
      border-gray-800
      bg-gradient-to-r
      from-gray-900
      to-gray-950
      p-4
      transition
      duration-300
      hover:border-gray-700
      hover:shadow-lg
    "
  >

    <div
      class="
        absolute
        left-0
        top-0
        h-full
        w-1
      "
      :class="typeStyle.border"
    />

    <div
      class="
        grid
        grid-cols-1
        items-center
        gap-4
        lg:grid-cols-[60px_170px_minmax(140px,1fr)_130px_170px]
      "
    >

      <ShiftTimelineIcon
        :type-style="typeStyle"
      />

      <ShiftTimelineMovement
        :item="item"
        :type-style="typeStyle"
        :format-amount="formatAmount"
      />

      <ShiftTimelineDetails
        :item="item"
      />

      <ShiftTimelineUser
        :item="item"
        :format-time="formatTime"
      />

      <ShiftTimelineAmount
        :item="item"
        :type-style="typeStyle"
        :format-amount="formatAmount"
      />

    </div>

  </div>
</template>

<script setup>

import { computed } from 'vue'

import { useFinancialMovement } from '@/composables/useFinancialMovement'

import ShiftTimelineIcon from './ShiftTimelineIcon.vue'
import ShiftTimelineMovement from './ShiftTimelineMovement.vue'
import ShiftTimelineDetails from './ShiftTimelineDetails.vue'
import ShiftTimelineUser from './ShiftTimelineUser.vue'
import ShiftTimelineAmount from './ShiftTimelineAmount.vue'

defineOptions({
  name: 'ShiftTimelineItem',
})

const props = defineProps({

  item: {
    type: Object,
    required: true,
  },

})

const item = computed(() => props.item)

const {
  typeStyle,
  formatAmount,
  formatTime,
} = useFinancialMovement(item)

</script>