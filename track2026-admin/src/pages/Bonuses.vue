<template>

    <div class="space-y-8">

        <!-- HEADER -->
        <PageHeader
            title="Penalizaciones"
            description="Penalties and disciplinary actions"
        >

            <div class="bg-red-600 text-white px-5 py-3 rounded-2xl font-semibold shadow-lg">

                {{ penalties.length }} registros

            </div>

        </PageHeader>

        <!-- LOADING -->
        <LoadingCard
            v-if="loading"
            text="Cargando penalizaciones..."
        />

        <!-- EMPTY -->
        <EmptyState
            v-else-if="!penalties.length"
            text="No hay penalizaciones registradas"
        />

        <!-- TABLE -->
        <DataTable
            v-else
            title="Penalizaciones"
            description="Listado de sanciones aplicadas"
            :columns="columns"
            :items="penalties"
        >

            <!-- MODEL -->
            <template #model="{ item }">

                <div>

                    <p class="font-semibold text-white">
                        {{ item.performance?.name }}
                    </p>

                    <p class="text-sm text-gray-500">
                        User #{{ item.user_id }}
                    </p>

                </div>

            </template>

            <!-- AMOUNT -->
            <template #amount="{ item }">

                <span class="text-white font-medium">

                    - {{ item.amount }}

                </span>

            </template>

            <!-- USD -->
            <template #amount_usd="{ item }">

                <Badge variant="red">

                    $ {{ item.amount_usd }}

                </Badge>

            </template>

            <!-- DATE -->
            <template #date="{ item }">

                <span class="text-gray-400">

                    {{ item.date }}

                </span>

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

const penalties = ref([])

const loading = ref(false)

const columns = [

    {
        key: 'model',
        label: 'Modelo',
    },

    {
        key: 'amount',
        label: 'Monto',
    },

    {
        key: 'amount_usd',
        label: 'USD',
    },

    {
        key: 'date',
        label: 'Fecha',
    },

]

const loadPenalties = async () => {

    try {

        loading.value = true

        const response = await api.get('/penalties')

        penalties.value = response.data.data

    } catch (error) {

        console.error(error)

    } finally {

        loading.value = false
    }
}

onMounted(() => {

    loadPenalties()
})

</script>

<style scoped>
</style>