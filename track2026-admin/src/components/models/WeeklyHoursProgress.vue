<template>
  <div
    class="
      bg-gradient-to-br from-gray-900 to-gray-950
      border border-gray-800
      rounded-3xl
      p-6
    "
  >

    <!-- HEADER -->
    <div class="flex items-center justify-between">
      <p class="text-gray-500 text-xs uppercase tracking-[0.2em]">
        Horas Semanales
      </p>

      <div class="text-sm text-gray-400">
        Meta: 36h
      </div>
    </div>

    <!-- VALUE -->
    <div class="mt-4 flex items-end justify-between">
      <h2 class="text-4xl font-bold text-white">
        {{ hours }}
      </h2>

      <span class="text-gray-500 text-sm">
        horas
      </span>
    </div>

    <!-- PROGRESS BAR -->
    <div class="mt-5 w-full bg-gray-800 rounded-full h-3 overflow-hidden">
      <div
        class="h-3 rounded-full transition-all duration-500"
        :style="{ width: progress + '%', backgroundColor: color }"
      />
    </div>

    <!-- FOOTER BADGE -->
    <div class="mt-4 flex items-center justify-between">

      <p class="text-xs text-gray-500">
        Progreso semanal
      </p>

      <div
        class="
          flex items-center gap-2
          px-3 py-1
          rounded-full
          bg-gray-800
          border border-gray-700
        "
      >
        <span class="text-white text-sm font-medium">
          {{ progress.toFixed(0) }}%
        </span>
      </div>

    </div>

  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  hours: {
    type: Number,
    default: 0
  }
})

const MAX = 36

const progress = computed(() =>
  Math.min((props.hours / MAX) * 100, 100)
)

const color = computed(() => {
  if (progress.value < 50) return '#f97316' // naranja
  if (progress.value < 80) return '#facc15' // amarillo
  return '#22c55e' // verde
})
</script>