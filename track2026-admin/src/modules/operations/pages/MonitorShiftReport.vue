<template>
  <div class="p-6">
    <h2 class="text-xl font-bold mb-4">Reporte Operativo de Turno</h2>

    <!-- Resumen -->
    <div class="mb-6">
      <p><strong>Total USD:</strong> {{ report?.summary.total_usd }}</p>
      <p><strong>Modelos:</strong> {{ report?.summary.models_count }}</p>
      <p><strong>Movimientos:</strong> {{ report?.summary.movements_count }}</p>
    </div>

    <!-- Tabla de modelos -->
    <table class="table-auto w-full border">
      <thead>
        <tr>
          <th class="border px-2 py-1">Modelo</th>
          <th class="border px-2 py-1">USD</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="model in report?.models" :key="model.model">
          <td class="border px-2 py-1">{{ model.model }}</td>
          <td class="border px-2 py-1">{{ model.usd }}</td>
        </tr>
      </tbody>
    </table>

    <!-- Totales por plataforma -->
    <h3 class="mt-6 font-semibold">Totales por Plataforma</h3>
    <ul>
      <li v-for="platform in report?.summary.platforms" :key="platform.platform">
        {{ platform.platform }}: {{ platform.usd }}
      </li>
    </ul>

    <!-- Movimientos -->
    <h3 class="mt-6 font-semibold">Movimientos</h3>
    <ul>
      <li v-for="movement in report?.movements" :key="movement.id">
        {{ movement.date }} - {{ movement.model }} en {{ movement.platform }}: {{ movement.amount }}
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '@/services/api' // usa tu wrapper de axios

const report = ref(null)

onMounted(async () => {
  const { data } = await axios.get('/monitor-shifts/1/report')
  report.value = data
})
</script>
