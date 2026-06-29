<template>

  <div>

    <h2 class="text-2xl font-bold text-white mb-6">
      Resumen Financiero
    </h2>


    <div
      class="
        grid
        grid-cols-1
        md:grid-cols-2
        xl:grid-cols-5
        gap-6
      "
    >


      <FinancialCard
        title="Ganancias"
        :value="financial.earnings"
      />


      <FinancialCard
        title="Bonos"
        :value="financial.bonuses"
        color="text-green-400"
      />


      <FinancialCard
        title="Multas"
        :value="financial.penalties"
        color="text-red-400"
      />


      <FinancialCard
        title="Descuentos"
        :value="financial.deductions"
        color="text-amber-400"
      />


      <FinancialCard
        title="Neto"
        :value="financial.net"
        color="text-yellow-400"
      />


    </div>


  </div>

</template>


<script setup>

import FinancialCard from './FinancialCard.vue'


defineProps({

  financial:{
    type:Object,
    default:()=>({

      earnings:'0.00',
      bonuses:'0.00',
      penalties:'0.00',
      deductions:'0.00',
      net:'0.00'

    })
  }

})

</script>