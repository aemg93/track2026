<template>
  <div
    v-if="shift"
    class="
      fixed
      inset-0
      z-50
      flex
      items-center
      justify-center
      bg-black/60
      p-5
    "
  >

    <section
      class="
        w-full
        max-w-3xl
        overflow-hidden
        rounded-3xl
        border
        border-gray-800
        bg-gray-950
        shadow-2xl
      "
    >

      <header
        class="
          flex
          items-center
          justify-between
          border-b
          border-gray-800
          px-6
          py-5
        "
      >

        <div>

          <h2 class="text-lg font-semibold text-white">
            Detalle de Tokens
          </h2>

          <p class="mt-1 text-sm text-gray-400">
            Distribución por plataforma del turno actual.
          </p>

        </div>

        <button
          type="button"
          class="
            rounded-lg
            px-3
            py-2
            text-sm
            text-gray-400
            hover:bg-gray-800
            hover:text-white
          "
          @click="close"
        >
          Cerrar
        </button>

      </header>

      <div
        class="
          grid
          gap-4
          border-b
          border-gray-800
          p-6
          md:grid-cols-2
        "
      >

        <div
          class="
            rounded-xl
            border
            border-cyan-500/20
            bg-cyan-500/10
            p-4
          "
        >

          <p class="text-xs uppercase text-cyan-300">
            Total Tokens
          </p>

          <p class="mt-2 text-3xl font-bold text-cyan-300">
            {{ formatTokens(totalTokens) }}
          </p>

        </div>

        <div
          class="
            rounded-xl
            border
            border-emerald-500/20
            bg-emerald-500/10
            p-4
          "
        >

          <p class="text-xs uppercase text-emerald-300">
            Valor USD
          </p>

          <p class="mt-2 text-3xl font-bold text-emerald-300">
            {{ formatUsd(totalUsd) }}
          </p>

        </div>

      </div>

      <div
        class="
          max-h-[420px]
          overflow-y-auto
          p-6
        "
      >

        <div
          v-if="platformTokens.length"
          class="space-y-3"
        >

          <article
            v-for="platform in platformTokens"
            :key="platform.name"
            class="
              flex
              items-center
              justify-between
              rounded-xl
              border
              border-gray-800
              bg-gray-900
              p-4
            "
          >

            <div>

              <p class="font-medium text-white">
                {{ platform.name }}
              </p>

              <p class="text-sm text-gray-400">
                Tokens registrados
              </p>

            </div>


            <div class="text-right">

              <p class="text-xl font-bold text-cyan-300">
                {{ formatTokens(platform.tokens) }}
              </p>

              <p class="text-sm text-emerald-300">
                {{ formatUsd(platform.usd) }}
              </p>

            </div>

          </article>

        </div>

        <div
          v-else
          class="
            rounded-xl
            border
            border-gray-800
            bg-gray-900
            p-6
            text-center
            text-gray-400
          "
        >

          No hay tokens registrados en este turno.

        </div>


      </div>

    </section>

  </div>
</template>

<script setup>

import {
  toRef,
} from 'vue'

import {
  useShiftTokenBreakdown,
} from '@/modules/operations/composables/useShiftTokenBreakdown'

defineOptions({
  name: 'ShiftTokenBreakdown',
})

const props = defineProps({

  shift: {
    type: Object,
    default: null,
  },

})

const emit = defineEmits([
  'close',
])

const shift = toRef(
  props,
  'shift'
)

const {

  tokenPlatforms: platformTokens,

  totalTokens,

  totalUsd,

} = useShiftTokenBreakdown(
  shift
)

function formatTokens(value) {

  return Number(value)
    .toLocaleString(
      'es-CO',
      {
        maximumFractionDigits: 0,
      }
    )

}

function formatUsd(value) {

  return Number(value)
    .toLocaleString(
      'es-CO',
      {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2,
      }
    )

}

function close() {

  emit('close')

}

</script>