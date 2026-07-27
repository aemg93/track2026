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
            Detalle de Ganancias
          </h2>

          <p class="mt-1 text-sm text-gray-400">
            Distribución por plataforma en USD del turno actual.
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



      <!-- Resumen -->
      <div
        class="
          border-b
          border-gray-800
          p-6
        "
      >

        <div
          class="
            rounded-xl
            border
            border-emerald-500/20
            bg-emerald-500/10
            p-5
          "
        >

          <p
            class="
              text-xs
              uppercase
              tracking-wider
              text-emerald-300
            "
          >
            Total USD
          </p>


          <p
            class="
              mt-2
              text-3xl
              font-bold
              text-emerald-300
            "
          >
            {{ formatUsd(totalUsd) }}
          </p>

        </div>

      </div>




      <!-- Plataformas -->
      <div
        class="
          max-h-[420px]
          overflow-y-auto
          p-6
        "
      >

        <div
          v-if="platformEarnings.length"
          class="space-y-3"
        >

          <article
            v-for="platform in platformEarnings"
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
                USD registrados
              </p>

            </div>



            <div class="text-right">

              <p
                class="
                  text-xl
                  font-bold
                  text-emerald-300
                "
              >
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
            text-sm
            text-gray-400
          "
        >

          No hay ganancias USD registradas en este turno.

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
  useShiftEarningBreakdown,
} from '@/modules/operations/composables/useShiftEarningBreakdown'



defineOptions({
  name: 'ShiftEarningBreakdown',
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

  platformEarnings,

  totalUsd,

} = useShiftEarningBreakdown(
  shift
)




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