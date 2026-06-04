<template>
  <div class="bg-gray-900 p-6 rounded-xl border border-gray-800">

    <h2 class="text-white text-lg font-semibold mb-4">
      Estudio
    </h2>

    <div class="space-y-3 text-sm">

      <div>
        <span class="text-gray-400">Nombre:</span>
        <span class="text-white ml-2">
          {{ model.studio?.name }}
        </span>
      </div>

      <div>
        <span class="text-gray-400">Ubicación:</span>
        <span class="text-white ml-2">
          {{ model.studio?.location }}
        </span>
      </div>

      <div>
        <span class="text-gray-400">ID:</span>
        <span class="text-white ml-2">
          {{ model.studio_id }}
        </span>
      </div>

    </div>

  </div>
</template>

<script setup>
defineProps({
  model: Object
})
</script>