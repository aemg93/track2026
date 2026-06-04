<template>
  <div class="bg-gray-900 p-6 rounded-xl border border-gray-800">

    <h2 class="text-white text-lg font-semibold mb-4">
      Documentación
    </h2>

    <div class="space-y-3 text-sm">

      <div>
        <span class="text-gray-400">Tipo:</span>
        <span class="text-white ml-2">
          {{ model.document_type }}
        </span>
      </div>

      <div>
        <span class="text-gray-400">Número:</span>
        <span class="text-white ml-2">
          {{ model.document_number }}
        </span>
      </div>

      <div>
        <span class="text-gray-400">Fecha Nacimiento:</span>
        <span class="text-white ml-2">
          {{ model.birth_date }}
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