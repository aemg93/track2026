<template>
  <div class="bg-gray-900 p-6 rounded-xl border border-gray-800">

    <h2 class="text-white text-lg font-semibold mb-4">
      Historial de Multas
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
          v-for="penalty in penalties"
          :key="penalty.id"
          class="border-b border-gray-800"
        >
          <td class="py-2 text-white">
            {{ penalty.date }}
          </td>

          <td class="py-2 text-white">
            {{ penalty.reason }}
          </td>

          <td class="py-2 text-right text-red-400">
            ${{ penalty.amount }}
          </td>
        </tr>

      </tbody>

    </table>

  </div>
</template>

<script setup>
defineProps({
  penalties: {
    type: Array,
    default: () => []
  }
})
</script>