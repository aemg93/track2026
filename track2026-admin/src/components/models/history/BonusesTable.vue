<template>
  <div class="bg-gray-900 p-6 rounded-xl border border-gray-800">

    <h2 class="text-white text-lg font-semibold mb-4">
      Historial de Bonos
    </h2>

    <table class="w-full text-sm">

      <thead>
        <tr class="border-b border-gray-800">
          <th class="text-left text-gray-400 py-2">Fecha</th>
          <th class="text-left text-gray-400 py-2">Motivo</th>
          <th class="text-right text-gray-400 py-2">Monto</th>
        </tr>
      </thead>

      <tbody>

        <tr
          v-for="bonus in bonuses"
          :key="bonus.id"
          class="border-b border-gray-800"
        >
          <td class="py-2 text-white">
            {{ bonus.date }}
          </td>

          <td class="py-2 text-white">
            {{ bonus.reason }}
          </td>

          <td class="py-2 text-right text-green-400">
            ${{ bonus.amount }}
          </td>
        </tr>

      </tbody>

    </table>

  </div>
</template>

<script setup>
defineProps({
  bonuses: {
    type: Array,
    default: () => []
  }
})
</script>