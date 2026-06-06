<template>

  <div
    class="bg-gradient-to-br from-gray-900 to-gray-950 border border-gray-800 rounded-3xl p-6"
  >

    <div class="mb-6">

      <p
        class="text-gray-500 text-xs uppercase tracking-[0.2em]"
      >
        Organización
      </p>

      <h2
        class="text-xl font-bold text-white mt-2"
      >
        Estudio
      </h2>

    </div>

    <div class="space-y-5">

      <div>

        <p
          class="text-gray-500 text-sm"
        >
          Nombre del estudio
        </p>

        <h3
          class="text-white text-lg font-semibold mt-1"
        >
          {{ model.studio?.name || 'Sin estudio asignado' }}
        </h3>

      </div>

      <div
        class="border-t border-gray-800 pt-4"
      >

        <p
          class="text-gray-500 text-sm"
        >
          Ubicación
        </p>

        <p
          class="text-white mt-1"
        >
          {{ model.studio?.location || 'Ubicación no registrada' }}
        </p>

      </div>

      <div
        class="border-t border-gray-800 pt-4 flex justify-between items-center"
      >

        <span
          class="text-gray-500"
        >
          ID del estudio
        </span>

        <span
          class="text-blue-400 font-semibold"
        >
          #{{ model.studio_id || '-' }}
        </span>

      </div>

    </div>

  </div>

</template>

<script setup>

defineProps({
  model: {
    type: Object,
    required: true
  }
})

</script>