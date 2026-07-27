<template>
  <div class="space-y-5">

    <div class="grid gap-3 md:grid-cols-2">

      <button
        v-for="item in actions"
        :key="item.type"
        type="button"
        :disabled="disabled"
        :class="[
          item.class,
          'rounded-xl border px-4 py-3 text-sm font-semibold transition-all duration-200',
          'disabled:cursor-not-allowed disabled:opacity-40'
        ]"
        @click="handleAction(item.type)"
      >
        {{ item.label }}
      </button>

    </div>

    <button
      type="button"
      :disabled="disabled"
      class="
        w-full
        rounded-xl
        border
        border-red-500/40
        bg-red-500/10
        py-3
        text-sm
        font-semibold
        text-red-300
        transition-all
        duration-200
        hover:border-red-400
        hover:bg-red-500/20
        hover:text-red-200
        disabled:cursor-not-allowed
        disabled:opacity-40
      "
      @click="handleAction('finish')"
    >
      Finalizar turno
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
    label: 'Registrar ganancia',
    class: `
      border-emerald-500/40
      bg-emerald-500/15
      text-emerald-300
      hover:border-emerald-400
      hover:bg-emerald-500/25
      hover:text-emerald-200
    `,
  },

  {
    type: 'bonus',
    label: 'Registrar bono',
    class: `
      border-blue-500/40
      bg-blue-500/15
      text-blue-300
      hover:border-blue-400
      hover:bg-blue-500/25
      hover:text-blue-200
    `,
  },

  {
    type: 'penalty',
    label: 'Registrar penalización',
    class: `
      border-amber-500/40
      bg-amber-500/15
      text-amber-300
      hover:border-amber-400
      hover:bg-amber-500/25
      hover:text-amber-200
    `,
  },

  {
    type: 'deduction',
    label: 'Registrar descuento',
    class: `
      border-rose-500/40
      bg-rose-500/15
      text-rose-300
      hover:border-rose-400
      hover:bg-rose-500/25
      hover:text-rose-200
    `,
  },
]

function handleAction(type) {
  if (disabled.value) {
    return
  }

  emit(type, props.performance)
}
</script>