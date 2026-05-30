<template>

    <div class="space-y-8">

        <div>

            <h1 class="text-4xl font-bold text-white">
                Finanzas
            </h1>

            <p class="text-gray-400 mt-2">
                Resumen financiero global
            </p>

        </div>

        <FinanceChart
            :finance="finance"
        />

    </div>

</template>

<script setup>

import { ref, onMounted } from 'vue'

import api from '../services/api'

import FinanceChart from '../components/dashboard/FinanceChart.vue'

const finance = ref(null)

const loadFinance = async () => {

    try {

        const response = await api.get('/dashboard')

        finance.value = response.data.data.finance

    } catch (error) {

        console.error(error)
    }
}

onMounted(() => {

    loadFinance()
})

</script>