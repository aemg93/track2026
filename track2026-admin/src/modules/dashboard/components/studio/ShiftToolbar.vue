<template>
  <div class="rounded-2xl border border-gray-800 bg-gray-900 p-6">

    <div class="mb-6 flex items-center justify-between">
      <div>
        <h2 class="text-lg font-semibold text-white">
          Panel Operativo
        </h2>

        <p class="text-sm text-gray-400">
          Selecciona una modelo para comenzar un turno.
        </p>
      </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-[1fr_auto]">

      <select
        v-model="selectedPerformanceId"
        :disabled="!performances.length"
        class="rounded-xl border border-gray-700 bg-gray-950 px-4 py-3 text-white"
      >

        <option
          v-if="performances.length"
          value=""
        >
          Seleccionar modelo...
        </option>

        <option
          v-else
          disabled
          value=""
        >
          No hay modelos disponibles
        </option>

        <option
          v-for="performance in performances"
          :key="performance.id"
          :value="performance.id"
        >
          {{ performance.name }}
        </option>

      </select>

      <button
        type="button"
        :disabled="!selectedPerformance"
        @click="handleStartShift"
        class="rounded-xl bg-indigo-600 px-6 py-3 text-white transition-colors hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-50"
      >
        Iniciar turno
      </button>

    </div>

  </div>
</template>

<script setup>
import { computed, ref, toRefs } from 'vue'

defineOptions({
  name: 'ShiftToolbar',
})

const props = defineProps({
  performances: {
    type: Array,
    default: () => [],
  },
})

const { performances } = toRefs(props)

const emit = defineEmits([
  'start-shift',
])

const selectedPerformanceId = ref('')

const selectedPerformance = computed(() =>
  performances.value.find(
    ({ id }) => Number(id) === Number(selectedPerformanceId.value)
  ) ?? null
)

function handleStartShift() {
  if (!selectedPerformance.value) {
    return
  }

  emit('start-shift', selectedPerformance.value)

  selectedPerformanceId.value = ''
}
</script>