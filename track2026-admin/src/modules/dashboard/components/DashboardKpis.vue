<template>

    <div
        class="
            grid
            grid-cols-1
            sm:grid-cols-2
            xl:grid-cols-3
            gap-6
        "
    >

        <KpiCard
            v-for="kpi in kpis"
            :key="kpi.title"
            :title="kpi.title"
            :value="kpi.value"
            :description="kpi.description"
            :color-class="kpi.colorClass"
            :text-color-class="kpi.textColorClass"
        />

    </div>

</template>


<script setup>

import { computed } from 'vue'
import KpiCard from './KpiCard.vue'


const props = defineProps({

    dashboard: {
        type: Object,
        required: true
    },

    finance: {
        type: Object,
        required: true
    }

})


const currency = (value) => {

    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0
    }).format(Number(value ?? 0))

}



const kpis = computed(() => [

    {
        title: 'Balance Neto',

        value: currency(
            props.finance?.net_balance
        ),

        description:
            'Resultado financiero actual',

        colorClass:
            'bg-gradient-to-br from-violet-500/15 to-violet-900/10 border-violet-500/20',

        textColorClass:
            'text-violet-400'
    },

    {
        title: 'Ganancias',

        value: currency(
            props.finance?.totals?.earnings
        ),

        description:
            'Ingresos acumulados',

        colorClass:
            'bg-gradient-to-br from-emerald-500/15 to-emerald-900/10 border-emerald-500/20',

        textColorClass:
            'text-emerald-400'
    },


    {
        title: 'Bonos',

        value: currency(
            props.finance?.totals?.bonuses
        ),

        description:
            'Bonificaciones registradas',

        colorClass:
            'bg-gradient-to-br from-blue-500/15 to-blue-900/10 border-blue-500/20',

        textColorClass:
            'text-blue-400'
    },

    {
        title: 'Descuentos Modelos',

        value: currency(
            props.finance?.totals?.deductions
        ),

        description:
            'Descuentos aplicados a pagos',

        colorClass:
            'bg-gradient-to-br from-orange-500/15 to-orange-900/10 border-orange-500/20',

        textColorClass:
            'text-orange-400'
    },

    {
        title: 'Penalizaciones',

        value: currency(
            props.finance?.totals?.penalties
        ),

        description:
            'Ajustes negativos aplicados',

        colorClass:
            'bg-gradient-to-br from-red-500/15 to-red-900/10 border-red-500/20',

        textColorClass:
            'text-red-400'
    },
    
    {
        title: 'Modelos',

        value:
            props.dashboard?.total_models ?? 0,

        description:
            'Modelos registrados',

        colorClass:
            'bg-gradient-to-br from-cyan-500/15 to-cyan-900/10 border-cyan-500/20',

        textColorClass:
            'text-cyan-400'
    }

])


</script>