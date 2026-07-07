<template>

  <div
    class="bg-gradient-to-br from-gray-900 to-gray-950 border border-gray-800 rounded-3xl p-6"
  >

    <div class="mb-6">

      <p
        class="text-gray-500 text-xs uppercase tracking-[0.2em]"
      >
        Perfil
      </p>

      <h2
        class="text-xl font-bold text-white mt-2"
      >
        Información Personal
      </h2>

    </div>

    <div class="space-y-5">

      <div class="flex justify-between items-center">

        <span class="text-gray-500">
          Nickname
        </span>

        <span class="text-blue-400 font-medium">
          @{{ model.nickname || '-' }}
        </span>

      </div>

      <div class="flex justify-between items-center">

        <span class="text-gray-500">
          Email
        </span>

        <span class="text-white">
          {{ model.email || '-' }}
        </span>

      </div>

      <div class="flex justify-between items-center">

        <span class="text-gray-500">
          Teléfono
        </span>

        <span class="text-white">
          {{ model.phone || '-' }}
        </span>

      </div>

      <div class="flex justify-between items-center">

        <span class="text-gray-500">
          País
        </span>

        <span class="text-white">
          {{ model.country || '-' }}
        </span>

      </div>

      <div class="flex justify-between items-center">

        <span class="text-gray-500">
          Ciudad
        </span>

        <span class="text-white">
          {{ model.city || '-' }}
        </span>

      </div>

      <div
        class="border-t border-gray-800 pt-4"
      >

        <p
          class="text-gray-500 mb-2"
        >
          Dirección
        </p>

        <p
          class="text-white"
        >
          {{ model.address || '-' }}
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