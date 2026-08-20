<template>
  <div class="space-y-8 text-white">

    <!-- HEADER -->
    <div class="flex items-end justify-between">
      <div>
        <h1 class="text-3xl font-bold">Analytics</h1>
        <p class="text-gray-400">Indicadores clave y comparaciones</p>
      </div>
    </div>

    <!-- KPI CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="p-4 rounded-2xl bg-blue-500/10 border border-blue-500/20">
        <p class="text-gray-400 text-sm">Revenue Total</p>
        <p class="text-2xl font-bold">${{ format(totalRevenue) }}</p>
      </div>

      <div class="p-4 rounded-2xl bg-green-500/10 border border-green-500/20">
        <p class="text-gray-400 text-sm">Bonuses</p>
        <p class="text-2xl font-bold">${{ format(totalBonuses) }}</p>
      </div>

      <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/20">
        <p class="text-gray-400 text-sm">Multas</p>
        <p class="text-2xl font-bold">${{ format(totalPenalties) }}</p>
      </div>

      <div class="p-4 rounded-2xl bg-yellow-500/10 border border-yellow-500/20">
        <p class="text-gray-400 text-sm">Descuentos</p>
        <p class="text-2xl font-bold">${{ format(totalDeductions) }}</p>
      </div>
    </div>

    <!-- COMPARISON SECTION -->
    <div class="p-6 rounded-3xl border border-gray-800 bg-gray-950">
      <h2 class="font-semibold mb-4">Comparación de Modelos</h2>
      <div class="space-y-4">
        <div
          v-for="m in mockModels"
          :key="m.name"
          class="flex items-center justify-between"
        >
          <div>
            <p class="font-semibold">{{ m.name }}</p>
            <p class="text-gray-500 text-sm">Score {{ m.score }}</p>
          </div>
          <div class="w-40 bg-gray-800 rounded-full h-2">
            <div
              class="h-2 rounded-full bg-blue-500"
              :style="{ width: m.percent + '%' }"
            ></div>
          </div>
        </div>
      </div>
    </div>

    <!-- RAW DATA DEBUG -->
    <div class="p-6 rounded-3xl border border-gray-800 bg-gray-950">
      <h2 class="font-semibold mb-4">Debug data</h2>
      <pre class="text-xs text-gray-400 overflow-auto">
{{ raw }}
      </pre>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'

const performances = ref([])

const load = async () => {
  try {
    const { data } = await api.get('/performances', { params: { limit: 50 } })
    performances.value = data.data.data || []
  } catch (e) {
    console.error(e)
  }
}
onMounted(load)

// Métricas básicas
const totalRevenue = computed(() =>
  performances.value.reduce((acc, p) =>
    acc + (p.earnings?.reduce((a, e) => a + parseFloat(e.net_usd || 0), 0) || 0), 0)
)

const totalBonuses = computed(() =>
  performances.value.reduce((acc, p) =>
    acc + (p.bonuses?.reduce((a, b) => a + parseFloat(b.amount || 0), 0) || 0), 0)
)

const totalPenalties = computed(() =>
  performances.value.reduce((acc, p) =>
    acc + (p.penalties?.reduce((a, x) => a + parseFloat(x.amount || 0), 0) || 0), 0)
)

const totalDeductions = computed(() =>
  performances.value.reduce((acc, p) =>
    acc + (p.deductions?.reduce((a, d) => a + parseFloat(d.amount || 0), 0) || 0), 0)
)

// Ranking visual simulado
const mockModels = computed(() =>
  performances.value.map((p, i) => ({
    name: p.nickname || `Model ${i + 1}`,
    score: p.ranking_score || 0,
    percent: Math.min(100, (p.ranking_score || 0) / 3)
  }))
)

const format = (v) => Number(v || 0).toFixed(2)
const raw = computed(() => performances.value)
</script>
