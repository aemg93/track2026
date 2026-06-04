<template>
  <div class="bg-gray-900 p-6 rounded-xl border border-gray-800">

    <h2 class="text-xl text-white mb-4">
      Finanzas
    </h2>

    <div class="grid grid-cols-4 gap-4 text-center">

      <div>
        <p class="text-gray-400">Ganancias</p>
        <p class="text-white font-bold">
          ${{ earnings }}
        </p>
      </div>

      <div>
        <p class="text-gray-400">Bonos</p>
        <p class="text-green-400 font-bold">
          ${{ bonuses }}
        </p>
      </div>

      <div>
        <p class="text-gray-400">Multas</p>
        <p class="text-red-400 font-bold">
          ${{ penalties }}
        </p>
      </div>

      <div>
        <p class="text-gray-400">Neto</p>
        <p class="text-yellow-400 font-bold">
          ${{ net }}
        </p>
      </div>

    </div>

  </div>
</template>

<script setup>
defineProps({
  earnings: String,
  bonuses: String,
  penalties: String,
  net: String
})
</script>