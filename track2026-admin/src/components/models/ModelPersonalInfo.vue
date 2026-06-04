<template>
  <div class="bg-gray-900 p-6 rounded-xl border border-gray-800">

    <h2 class="text-white text-lg font-semibold mb-4">
      Información Personal
    </h2>

    <div class="space-y-3 text-sm">

      <div>
        <span class="text-gray-400">Nickname:</span>
        <span class="text-white ml-2">
          {{ model.nickname }}
        </span>
      </div>

      <div>
        <span class="text-gray-400">Email:</span>
        <span class="text-white ml-2">
          {{ model.email }}
        </span>
      </div>

      <div>
        <span class="text-gray-400">Teléfono:</span>
        <span class="text-white ml-2">
          {{ model.phone }}
        </span>
      </div>

      <div>
        <span class="text-gray-400">País:</span>
        <span class="text-white ml-2">
          {{ model.country }}
        </span>
      </div>

      <div>
        <span class="text-gray-400">Ciudad:</span>
        <span class="text-white ml-2">
          {{ model.city }}
        </span>
      </div>

      <div>
        <span class="text-gray-400">Dirección:</span>
        <span class="text-white ml-2">
          {{ model.address }}
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