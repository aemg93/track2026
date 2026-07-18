<template>

<div
  class="
    min-w-0
    border-l
    border-gray-800
    pl-4
  "
>


<p
  v-if="item.performed_by"
  class="
    truncate
    text-sm
    text-gray-300
  "
>
  {{ item.performed_by }}
</p>


<p
  class="
    mt-1
    text-xs
    text-gray-500
  "
>
  {{ formatTime(item) }}
</p>


</div>

</template>


<script setup>


defineProps({

  item:{
    type:Object,
    required:true,
  },


  formatTime:{
    type:Function,
    required:true,
  }

})


</script>