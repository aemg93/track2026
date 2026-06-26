<template>

  <div>

    <div class="mb-6">

      <h2
        class="text-2xl font-bold text-white mt-2"
      >
        Resumen Financiero
      </h2>

    </div>


    <div
      class="
        grid
        grid-cols-1
        md:grid-cols-2
        xl:grid-cols-5
        gap-6
      "
    >


      <div
        class="
          bg-gradient-to-br
          from-gray-900
          to-gray-950
          border
          border-gray-800
          rounded-3xl
          p-6
        "
      >

        <p
          class="
            text-gray-500
            text-xs
            uppercase
            tracking-[0.2em]
          "
        >
          Ganancias
        </p>

        <h2
          class="
            text-4xl
            font-bold
            text-white
            mt-4
          "
        >
          ${{ earnings }}
        </h2>

      </div>



      <div
        class="
          bg-gradient-to-br
          from-gray-900
          to-gray-950
          border
          border-gray-800
          rounded-3xl
          p-6
        "
      >

        <p
          class="
            text-gray-500
            text-xs
            uppercase
            tracking-[0.2em]
          "
        >
          Bonos
        </p>

        <h2
          class="
            text-4xl
            font-bold
            text-green-400
            mt-4
          "
        >
          ${{ bonuses }}
        </h2>

      </div>



      <div
        class="
          bg-gradient-to-br
          from-gray-900
          to-gray-950
          border
          border-gray-800
          rounded-3xl
          p-6
        "
      >

        <p
          class="
            text-gray-500
            text-xs
            uppercase
            tracking-[0.2em]
          "
        >
          Multas
        </p>

        <h2
          class="
            text-4xl
            font-bold
            text-red-400
            mt-4
          "
        >
          ${{ penalties }}
        </h2>

      </div>



      <div
        class="
          bg-gradient-to-br
          from-gray-900
          to-gray-950
          border
          border-gray-800
          rounded-3xl
          p-6
        "
      >

        <p
          class="
            text-gray-500
            text-xs
            uppercase
            tracking-[0.2em]
          "
        >
          Descuentos
        </p>

        <h2
          class="
            text-4xl
            font-bold
            text-amber-400
            mt-4
          "
        >
          ${{ deductions }}
        </h2>

      </div>



      <div
        class="
          bg-gradient-to-br
          from-gray-900
          to-gray-950
          border
          border-gray-800
          rounded-3xl
          p-6
        "
      >

        <p
          class="
            text-gray-500
            text-xs
            uppercase
            tracking-[0.2em]
          "
        >
          Neto
        </p>

        <h2
          class="
            text-4xl
            font-bold
            text-yellow-400
            mt-4
          "
        >
          ${{ net }}
        </h2>

      </div>


    </div>


  </div>


</template>


<script setup>

defineProps({

  earnings: {
    type: String,
    default: '0.00'
  },

  bonuses: {
    type: String,
    default: '0.00'
  },

  penalties: {
    type: String,
    default: '0.00'
  },

  deductions: {
    type: String,
    default: '0.00'
  },

  net: {
    type: String,
    default: '0.00'
  }

})

</script>