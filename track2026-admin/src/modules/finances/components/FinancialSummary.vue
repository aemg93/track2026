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
        :value="financial?.gross_usd"
      />

      <FinancialCard
        title="Bonos"
        :value="financial?.bonus_usd"
        color="text-green-400"
      />

      <FinancialCard
        title="Multas"
        :value="financial?.penalty_usd"
        color="text-red-400"
      />

      <FinancialCard
        title="Descuentos"
        :value="financial?.deduction_usd"
        color="text-amber-400"
      />

      <FinancialCard
        title="Neto"
        :value="financial?.net_usd"
        color="text-yellow-400"
      />

    </div>

  </div>

</template>

<script setup>

import FinancialCard from './FinancialCard.vue'

defineProps({

  financial: {
    type: Object,

    default: () => ({

      gross_usd: '0.00',

      bonus_usd: '0.00',

      penalty_usd: '0.00',

      deduction_usd: '0.00',

      net_usd: '0.00',

    })
  }

})

</script>