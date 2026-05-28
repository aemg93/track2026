<template>

    <div class="space-y-8">

        <!-- HEADER -->
        <PageHeader
            title="Desempeño"
            description="Performance overview of all models"
        >

            <div class="bg-blue-600 text-white px-5 py-3 rounded-2xl font-semibold shadow-lg">

                {{ performances.length }} modelos

            </div>

        </PageHeader>

        <!-- LOADING -->
        <LoadingCard
            v-if="loading"
            text="Cargando desempeño..."
        />

        <!-- EMPTY -->
        <EmptyState
            v-else-if="!performances.length"
            text="No hay registros disponibles"
        />

        <!-- TABLE -->
        <DataTable
            v-else
            title="Rendimiento"
            description="Listado general de modelos"
            :columns="columns"
            :items="performances"
        >

            <!-- MODEL -->
            <template #name="{ item }">

                <div>

                    <p class="font-semibold text-white">
                        {{ item.name }}
                    </p>

                    <p class="text-sm text-gray-500">
                        ID #{{ item.id }}
                    </p>

                </div>

            </template>

            <!-- HOURS -->
            <template #hours_streamed="{ item }">

                <span class="text-white font-medium">

                    {{ item.hours_streamed }} hrs

                </span>

            </template>

            <!-- SCORE -->
            <template #ranking_score="{ item }">

                <Badge variant="blue">

                    {{ item.ranking_score }}

                </Badge>

            </template>

            <!-- STATUS -->
            <template #active="{ item }">

                <Badge
                    :variant="item.active ? 'green' : 'red'"
                >

                    {{ item.active ? 'Activa' : 'Inactiva' }}

                </Badge>

            </template>

        </DataTable>

    </div>

</template>

<script setup>

import { ref, onMounted } from 'vue'

import api from '../services/api'

import PageHeader from '../components/ui/PageHeader.vue'
import LoadingCard from '../components/ui/LoadingCard.vue'
import EmptyState from '../components/ui/EmptyState.vue'
import Badge from '../components/ui/Badge.vue'
import DataTable from '../components/ui/DataTable.vue'

const performances = ref([])

const loading = ref(false)

const columns = [

    {
        key: 'name',
        label: 'Modelo',
    },

    {
        key: 'hours_streamed',
        label: 'Horas',
    },

    {
        key: 'ranking_score',
        label: 'Score',
    },

    {
        key: 'active',
        label: 'Estado',
    },

]

const loadPerformances = async () => {

    try {

        loading.value = true

        const response = await api.get('/performances')

        performances.value = response.data.data

    } catch (error) {

        console.error(error)

    } finally {

        loading.value = false
    }
}

onMounted(() => {

    loadPerformances()
})

</script>

<style scoped>
</style>