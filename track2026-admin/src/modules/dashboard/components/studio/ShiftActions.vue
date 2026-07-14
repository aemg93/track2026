<template>
  <div class="space-y-6">

    <div class="grid gap-4 md:grid-cols-2">

      <button
        v-for="item in actions"
        :key="item.type"
        type="button"
        :disabled="disabled"
        :class="item.class"
        @click="handleAction(item.type)"
      >
        {{ item.label }}
      </button>

    </div>

    <button
      type="button"
      :disabled="disabled"
      class="w-full rounded-xl border border-red-600 py-3 font-semibold text-red-400 transition hover:bg-red-600 hover:text-white disabled:cursor-not-allowed disabled:opacity-50"
      @click="handleAction('finish')"
    >
      Finalizar Turno
    </button>

  </div>
</template>

<script setup>
import { computed } from 'vue'

defineOptions({
  name: 'ShiftActions',
})

const props = defineProps({
  performance: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits([
  'earning',
  'bonus',
  'penalty',
  'deduction',
  'finish',
])

const disabled = computed(() => !props.performance)

const actions = [
  {
    type: 'earning',
    label: 'Registrar Ganancia',
    class:
      'rounded-xl bg-emerald-600 px-4 py-3 font-medium text-white transition hover:bg-emerald-500 disabled:cursor-not-allowed disabled:opacity-50',
  },
  {
    type: 'bonus',
    label: 'Registrar Bono',
    class:
      'rounded-xl bg-blue-600 px-4 py-3 font-medium text-white transition hover:bg-blue-500 disabled:cursor-not-allowed disabled:opacity-50',
  },
  {
    type: 'penalty',
    label: 'Registrar Penalización',
    class:
      'rounded-xl bg-amber-600 px-4 py-3 font-medium text-white transition hover:bg-amber-500 disabled:cursor-not-allowed disabled:opacity-50',
  },
  {
    type: 'deduction',
    label: 'Registrar Descuento',
    class:
      'rounded-xl bg-rose-600 px-4 py-3 font-medium text-white transition hover:bg-rose-500 disabled:cursor-not-allowed disabled:opacity-50',
  },
]

function handleAction(type) {
  if (disabled.value) return

  emit(type, props.performance)
}
</script>