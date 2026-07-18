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
  v-if="item.description"
  class="
    truncate
    text-base
    font-medium
    text-cyan-300
  "
>
  {{ item.description }}
</p>



<p
  v-if="item.tokens"
  class="
    mt-1
    truncate
    text-sm
    text-gray-400
  "
>
  {{ item.tokens }} tokens
</p>



<p
  v-if="item.reason"
  class="
    mt-1
    truncate
    text-sm
    text-gray-300
  "
>
  {{ item.reason }}
</p>


</div>

</template>


<script setup>

defineProps({

  item:{
    type:Object,
    required:true,
  }

})

</script>