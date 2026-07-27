<template>
  <ShiftEmptyState
    v-if="!selectedShift"
  />

  <div
    v-else
    class="grid gap-5 xl:grid-cols-[320px_minmax(0,1fr)]"
  >
    <!-- Panel operativo -->
    <aside class="space-y-5">

      <ShiftHeader
        :shift="selectedShift"
        @pause="handlePause"
        @resume="handleResume"
      />

      <ShiftActions
        :performance="selectedPerformance"
        @earning="handleEarning"
        @bonus="handleBonus"
        @penalty="handlePenalty"
        @deduction="handleDeduction"
        @finish="handleFinish"
      />

    </aside>


    <!-- Actividad -->
    <section
      class="overflow-hidden rounded-2xl border border-gray-800 bg-gray-900"
    >

      <header class="border-b border-gray-800 px-6 py-5">

        <h2 class="text-lg font-semibold text-white">
          Actividad del turno
        </h2>

        <p class="mt-1 text-sm text-gray-400">
          Ganancias, bonos, penalizaciones y descuentos registrados durante esta sesión.
        </p>


        <div
          class="mt-5 grid gap-4 md:grid-cols-3"
        >

          <!-- Ganancias -->
<button
  type="button"
  @click="handleEarnings"
  class="
    rounded-xl
    border
    border-emerald-500/20
    bg-emerald-500/10
    p-4
    text-left
    transition
    duration-200
    hover:border-emerald-400/50
    hover:bg-emerald-500/20
  "
>

  <p class="text-xs font-medium uppercase tracking-wider text-emerald-300">
    Ganancias
  </p>

  <p class="mt-2 text-2xl font-bold text-emerald-300">
    {{ formatUsd(totalUsd) }}
  </p>

</button>


          <!-- Tokens -->
          <button
            type="button"
            @click="handleTokens"
            class="
              rounded-xl
              border
              border-cyan-500/20
              bg-cyan-500/10
              p-4
              text-left
              transition
              duration-200
              hover:border-cyan-400/50
              hover:bg-cyan-500/20
            "
          >

            <p
              class="
                text-xs
                font-medium
                uppercase
                tracking-wider
                text-cyan-300
              "
            >
              Tokens
            </p>

            <p
              class="
                mt-2
                text-2xl
                font-bold
                text-cyan-300
              "
            >
              {{ formatTokens(totalTokens) }}
            </p>

          </button>


          <!-- Movimientos -->
          <div
            class="
              rounded-xl
              border
              border-gray-700
              bg-gray-800/60
              p-4
            "
          >

            <p
              class="
                text-xs
                font-medium
                uppercase
                tracking-wider
                text-gray-400
              "
            >
              Movimientos
            </p>

            <p
              class="
                mt-2
                text-2xl
                font-bold
                text-white
              "
            >
              {{ totalMovements }}
            </p>

          </div>

        </div>

      </header>


      <div
        class="
          h-[560px]
          overflow-y-auto
          p-6
        "
      >

        <ShiftTimeline
          :timeline="timeline"
        />

      </div>

    </section>

  </div>

</template>
<script setup>

import {
  toRefs,
} from 'vue'


import ShiftActions from './ShiftActions.vue'
import ShiftEmptyState from './ShiftEmptyState.vue'
import ShiftHeader from './ShiftHeader.vue'
import ShiftTimeline from './timeline/ShiftTimeline.vue'


import {
  useShiftWorkspace,
} from '@/modules/operations/composables/useShiftWorkspace'



defineOptions({
  name: 'ShiftWorkspace',
})



const props = defineProps({

  selectedShift: {
    type: Object,
    default: null,
  },

})



const emit = defineEmits([

  // Acciones financieras
  'earning',
  'bonus',
  'penalty',
  'deduction',


  // Apertura de detalles
  'earnings',
  'tokens',


  // Control del turno
  'pause',
  'resume',
  'finish',

])



const {
  selectedShift,
} = toRefs(props)



const {

  selectedPerformance,


  timeline,


  totalUsd,

  totalTokens,

  totalMovements,


  formatUsd,

  formatTokens,


  // Abrir modales
  handleEarnings,
  handleTokens,


  // Registrar movimientos
  handleEarning,
  handleBonus,
  handlePenalty,
  handleDeduction,


  // Control turno
  handlePause,
  handleResume,
  handleFinish,


} = useShiftWorkspace(
  selectedShift,
  emit
)


</script>