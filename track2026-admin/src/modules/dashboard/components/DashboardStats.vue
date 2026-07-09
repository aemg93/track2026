<template>

    <div
        class="
            grid
            grid-cols-1
            sm:grid-cols-2
            xl:grid-cols-4
            gap-6
        "
    >

        <KpiCard
            v-for="card in cards"
            :key="card.title"
            :title="card.title"
            :value="card.value"
            :description="card.description"
            :colorClass="card.colorClass"
            :textColorClass="card.textColorClass"
        />

    </div>

</template>

<script setup>

import { computed } from 'vue'
import KpiCard from './KpiCard.vue'

const props = defineProps({

    dashboard: {
        type: Object,
        required: true,
    },

    finance: {
        type: Object,
        required: true,
    },

})

const formatCurrency = (value) => {

    return `$${Number(value ?? 0).toLocaleString()}`

}

const cards = computed(() => [

    {
        title: 'Ganancias',
        value: formatCurrency(props.finance?.totals?.earnings),
        description: 'Ingresos acumulados',
        colorClass:
            'from-emerald-500/15 to-emerald-900/10 border-emerald-500/20',
        textColorClass: 'text-emerald-400',
    },

    {
        title: 'Bonos',
        value: formatCurrency(props.finance?.totals?.bonuses),
        description: 'Bonificaciones registradas',
        colorClass:
            'from-blue-500/15 to-blue-900/10 border-blue-500/20',
        textColorClass: 'text-blue-400',
    },

    {
        title: 'Multas',
        value: formatCurrency(props.finance?.totals?.penalties),
        description: 'Penalizaciones aplicadas',
        colorClass:
            'from-red-500/15 to-red-900/10 border-red-500/20',
        textColorClass: 'text-red-400',
    },

    {
        title: 'Descuentos',
        value: formatCurrency(props.finance?.totals?.deductions),
        description: 'Descuentos realizados',
        colorClass:
            'from-orange-500/15 to-orange-900/10 border-orange-500/20',
        textColorClass: 'text-orange-400',
    },

    {
        title: 'Balance Neto',
        value: formatCurrency(props.finance?.totals?.net_balance),
        description: 'Resultado financiero',
        colorClass:
            'from-violet-500/15 to-violet-900/10 border-violet-500/20',
        textColorClass: 'text-violet-400',
    },

    {
        title: 'Modelos',
        value: props.dashboard?.total_models ?? 0,
        description: 'Modelos registrados',
        colorClass:
            'from-cyan-500/15 to-cyan-900/10 border-cyan-500/20',
        textColorClass: 'text-cyan-400',
    },

])

</script>