<template>
  <div class="space-y-8">
    <!-- GANANCIAS -->
    <EarningsTable
      :earnings="earnings"
    />

    <!-- BONOS -->
    <BonusesTable
      :bonuses="bonuses"
    />

    <!-- PENALIZACIONES -->
    <PenaltiesTable
      :penalties="penalties"
    />

    <!-- DEDUCCIONES -->
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
  earnings: {
    type: Array,
    default: () => []
  },

  bonuses: {
    type: Array,
    default: () => []
  },

  penalties: {
    type: Array,
    default: () => []
  },

  deductions: {
    type: Array,
    default: () => []
  }
})
</script>