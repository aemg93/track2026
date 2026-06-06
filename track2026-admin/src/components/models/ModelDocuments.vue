<template>

  <div
    class="bg-gradient-to-br from-gray-900 to-gray-950 border border-gray-800 rounded-3xl p-6"
  >

    <div class="mb-6">

      <p
        class="text-gray-500 text-xs uppercase tracking-[0.2em]"
      >
        Identificación
      </p>

      <h2
        class="text-xl font-bold text-white mt-2"
      >
        Documentación
      </h2>

    </div>

    <div class="space-y-5">

      <div
        class="flex justify-between items-center"
      >

        <span class="text-gray-500">
          Tipo de documento
        </span>

        <span class="text-white font-medium">
          {{ model.document_type || '-' }}
        </span>

      </div>

      <div
        class="flex justify-between items-center"
      >

        <span class="text-gray-500">
          Número
        </span>

        <span class="text-white font-medium">
          {{ model.document_number || '-' }}
        </span>

      </div>

      <div
        class="border-t border-gray-800 pt-4"
      >

        <p
          class="text-gray-500 mb-2"
        >
          Fecha de nacimiento
        </p>

        <p
          class="text-white"
        >
          {{ model.birth_date || '-' }}
        </p>

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