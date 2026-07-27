<template>
  <section
    class="
      rounded-xl
      border
      border-gray-800
      bg-gray-900
      p-6
    "
  >

    <h3
      class="
        mb-6
        text-lg
        font-semibold
        text-white
      "
    >
      Actividad reciente
    </h3>


    <div
      v-if="timeline.length"
      class="space-y-3"
    >

      <ShiftTimelineItem
        v-for="item in timeline"
        :key="item.id ?? `${item.type}-${item.date}`"
        :item="item"
      />

    </div>


    <ShiftTimelineEmpty
      v-else
    />

  </section>
</template>



<script setup>

import ShiftTimelineItem from './ShiftTimelineItem.vue'
import ShiftTimelineEmpty from './ShiftTimelineEmpty.vue'


defineOptions({

  name:'ShiftTimeline',

})


defineProps({

  timeline:{
    type:Array,
    default:()=>[],
  },

})

</script>