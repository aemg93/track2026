<template>

  <div class="space-y-6">

    <EarningsTable
      :earnings="earnings"
    />

    <BonusesTable
      :bonuses="bonuses"
    />

    <PenaltiesTable
      :penalties="penalties"
    />

    <DeductionsTable
      :deductions="deductions"
    />

  </div>

</template>


<script setup>

import EarningsTable from './EarningsTable.vue'
import BonusesTable from './BonusesTable.vue'
import PenaltiesTable from './PenaltiesTable.vue'
import DeductionsTable from './DeductionsTable.vue'


defineProps({

  earnings:{
    type:Array,
    default:()=>[]
  },

  bonuses:{
    type:Array,
    default:()=>[]
  },

  penalties:{
    type:Array,
    default:()=>[]
  },

  deductions:{
    type:Array,
    default:()=>[]
  }

})

</script>