<template>

  <div
    class="
      bg-gradient-to-br
      from-gray-900
      to-gray-950
      border
      border-gray-800
      rounded-3xl
      p-6
    "
  >

    <p
      class="
        text-gray-500
        text-xs
        uppercase
        tracking-[0.2em]
      "
    >
      {{ title }}
    </p>


    <h2
      class="
        text-4xl
        font-bold
        mt-4
      "
      :class="colorClass"
    >
      ${{ formattedValue }}
    </h2>


  </div>

</template>


<script setup>

import { computed } from 'vue'


const props = defineProps({

  title:{
    type:String,
    required:true
  },


  value:{
    type:[String,Number],
    default:0
  },


  color:{
    type:String,
    default:'white'
  }

})


const colors = {

  white:'text-white',

  green:'text-green-400',

  red:'text-red-400',

  amber:'text-amber-400',

  yellow:'text-yellow-400'

}


const colorClass = computed(() =>
  colors[props.color] ?? colors.white
)


const formattedValue = computed(() => {

  return Number(props.value || 0)
    .toLocaleString('en-US',{
      minimumFractionDigits:2,
      maximumFractionDigits:2
    })

})


</script>