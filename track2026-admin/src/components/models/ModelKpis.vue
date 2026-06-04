<template>
  <div class="grid grid-cols-3 gap-4">

    <div class="bg-gray-900 p-5 rounded-xl border border-gray-800">
      <p class="text-gray-400">Horas</p>
      <h2 class="text-2xl text-white">
        {{ model.hours_streamed }}
      </h2>
    </div>

    <div class="bg-gray-900 p-5 rounded-xl border border-gray-800">
      <p class="text-gray-400">Ranking</p>
      <h2 class="text-2xl text-blue-400">
        {{ model.ranking_score }}
      </h2>
    </div>

    <div class="bg-gray-900 p-5 rounded-xl border border-gray-800">
      <p class="text-gray-400">Estado</p>
      <h2 class="text-2xl text-green-400">
        {{ model.active ? 'Activo' : 'Inactivo' }}
      </h2>
    </div>

  </div>
</template>

<script setup>
defineProps({
  model: Object
})
</script>