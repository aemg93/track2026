<template>

    <div class="space-y-8">

        <div>

            <h1 class="text-4xl font-bold text-white">
                Analytics
            </h1>

            <p class="text-gray-400 mt-2">
                Estadísticas avanzadas del estudio
            </p>

        </div>

        <RankingTable
            :ranking="ranking"
        />

    </div>

</template>

<script setup>

import { ref, onMounted } from 'vue'

import api from '../services/api'

import RankingTable from '../components/dashboard/RankingTable.vue'

const ranking = ref([])

const loadAnalytics = async () => {

    try {

        const response = await api.get('/dashboard')

        ranking.value = response.data.data.dashboard.ranking

    } catch (error) {

        console.error(error)
    }
}

onMounted(() => {

    loadAnalytics()
})

</script>