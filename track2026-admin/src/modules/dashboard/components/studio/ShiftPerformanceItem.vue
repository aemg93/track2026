<template>
  <button
    type="button"
    @click="selectShift"
    class="w-full rounded-2xl border p-5 text-left transition-all"
    :class="[
      selected
        ? 'border-indigo-500 bg-indigo-500/10'
        : 'border-gray-800 bg-gray-900 hover:border-gray-700'
    ]"
  >
    <div class="flex items-center gap-4">

      <div
        class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-500/10 text-lg font-bold text-indigo-400"
      >
        {{ initials }}
      </div>

      <div class="flex-1">

        <h3 class="font-semibold text-white">
          {{ performance.name }}
        </h3>

        <p class="text-sm text-gray-400">
          {{ performance.nickname || 'Sin nickname' }}
        </p>

      </div>

      <span
        class="rounded-full px-3 py-1 text-xs font-semibold"
        :class="statusClass"
      >
        {{ statusLabel }}
      </span>

    </div>

    <div class="mt-5 text-sm">

      <p class="text-gray-500">
        Estado del turno
      </p>

      <p class="font-semibold text-white capitalize">
        {{ shift.status }}
      </p>

    </div>

  </button>
</template>

<script setup>
import { computed, toRefs } from 'vue'

defineOptions({
  name: 'ShiftPerformanceItem',
})

const props = defineProps({
  shift: {
    type: Object,
    required: true,
  },

  selected: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits([
  'select',
])

const {
  shift,
  selected,
} = toRefs(props)

const performance = computed(() => shift.value.performance)

const initials = computed(() =>
  performance.value?.name
    ?.trim()
    .split(/\s+/)
    .map(word => word[0])
    .join('')
    .slice(0, 2)
    .toUpperCase() || '?'
)

const statusLabel = computed(() => {
  switch (shift.value.status) {
    case 'active':
      return 'En turno'

    case 'paused':
      return 'Pausado'

    default:
      return 'Finalizado'
  }
})

const statusClass = computed(() => {
  switch (shift.value.status) {
    case 'active':
      return 'bg-green-500/10 text-green-400'

    case 'paused':
      return 'bg-yellow-500/10 text-yellow-400'

    default:
      return 'bg-gray-500/10 text-gray-400'
  }
})

function selectShift() {
  emit('select', shift.value)
}
</script>