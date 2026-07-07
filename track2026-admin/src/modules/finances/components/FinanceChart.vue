<template>

    <div
        class="bg-gray-900 border border-gray-800 rounded-3xl p-6 min-h-[500px]"
    >

        <!-- HEADER -->
        <div class="flex items-center justify-between mb-8">

            <div>

                <h2 class="text-2xl font-bold text-white">
                    Financial Overview
                </h2>

                <p class="text-gray-400 text-sm mt-1">
                    Earnings vs expenses
                </p>

            </div>

        </div>

        <!-- CHART -->
        <div class="w-full h-[380px]">

            <v-chart
                v-if="finance"
                class="w-full h-full"
                :option="chartOptions"
                autoresize
            />

        </div>

    </div>

</template>

<script setup>

import { computed } from 'vue'

const props = defineProps({
    finance: {
        type: Object,
        default: null,
    },
})

const chartOptions = computed(() => ({

    backgroundColor: 'transparent',

    tooltip: {
        trigger: 'axis',
    },

    grid: {
        top: 40,
        left: 20,
        right: 20,
        bottom: 20,
        containLabel: true,
    },

    xAxis: {
        type: 'category',

        data: [
            'Ganancias',
            'Bonos',
            'Multas',
            'Descuentos',
        ],

        axisLabel: {
            color: '#9ca3af',
        },

        axisLine: {
            lineStyle: {
                color: '#374151',
            },
        },
    },

    yAxis: {
        type: 'value',

        axisLabel: {
            color: '#9ca3af',
        },

        splitLine: {
            lineStyle: {
                color: '#1f2937',
            },
        },
    },

    series: [
        {
            data: [
                props.finance?.totals?.earnings || 0,
                props.finance?.totals?.bonuses || 0,
                props.finance?.totals?.penalties || 0,
                props.finance?.totals?.deductions || 0,
            ],

            type: 'bar',

            barWidth: 60,

            itemStyle: {
                borderRadius: [12, 12, 0, 0],
            },
        },
    ],

}))

</script>