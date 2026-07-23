<template>

  <div
    class="
      grid grid-cols-1 md:grid-cols-3
      gap-6
    "
  >

    <!-- HORAS -->
    <div
      class="
        bg-gradient-to-br from-gray-900 to-gray-950
        border border-gray-800
        rounded-3xl
        p-6
        flex flex-col justify-between
        min-h-[140px]
      "
    >

      <p class="text-gray-500 text-xs uppercase tracking-[0.2em]">
        Horas Trabajadas
      </p>


      <div class="flex items-end justify-between mt-4">

        <h2 class="text-5xl font-bold text-white">
          {{ totalHours }}
        </h2>


        <span class="text-blue-400 text-sm">
          hrs
        </span>

      </div>

    </div>



    <!-- RANKING -->
    <div
      class="
        bg-gradient-to-br from-gray-900 to-gray-950
        border border-gray-800
        rounded-3xl
        p-6
        flex flex-col justify-between
        min-h-[140px]
      "
    >

      <p class="text-gray-500 text-xs uppercase tracking-[0.2em]">
        Ranking
      </p>


      <div class="flex items-end justify-between mt-4">

        <h2 class="text-5xl font-bold text-blue-400">
          {{ model.ranking_score || 0 }}
        </h2>


        <span class="text-gray-500 text-sm">
          score
        </span>

      </div>

    </div>



    <!-- ESTUDIO -->
    <div
      class="
        bg-gradient-to-br from-gray-900 to-gray-950
        border border-gray-800
        rounded-3xl
        p-6
        flex flex-col justify-between
        min-h-[140px]
      "
    >

      <p class="text-gray-500 text-xs uppercase tracking-[0.2em]">
        Estudio
      </p>


      <div class="mt-4">

        <h2 class="text-xl font-semibold text-white truncate">
          {{ model.studio?.name || 'Sin estudio' }}
        </h2>


        <p class="text-gray-500 text-sm mt-2">
          {{ model.studio?.location || 'Sin ubicación' }}
        </p>

      </div>

    </div>


  </div>

</template>


<script setup>

import {
  computed
} from 'vue'


const props = defineProps({

  model: {
    type: Object,
    required: true
  }

})


const totalHours = computed(() => {

  return (
    props.model?.hours?.total ?? 0
  )

})


</script>