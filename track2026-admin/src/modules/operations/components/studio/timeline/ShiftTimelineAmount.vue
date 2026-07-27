<template>

<div
  class="
    flex
    min-w-0
    justify-center
    border-l
    border-gray-800
    pl-4
  "
>


<p
  class="
    whitespace-nowrap
    text-xl
    font-bold
    xl:text-2xl
  "
  :class="typeStyle.amount"
>
  {{ formatAmount(item.amount) }}
</p>


</div>

</template>


<script setup>


defineProps({

  item:{
    type:Object,
    required:true,
  },


  typeStyle:{
    type:Object,
    required:true,
  },


  formatAmount:{
    type:Function,
    required:true,
  }


})


</script>