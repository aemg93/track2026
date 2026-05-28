<template>

    <div class="space-y-8">

        <!-- HEADER -->
        <PageHeader
            title="Ganancias"
            description="Revenue and earnings overview"
        >

            <div class="bg-green-600 text-white px-5 py-3 rounded-2xl font-semibold shadow-lg">

                {{ earnings.length }} registros

            </div>

        </PageHeader>

        <!-- LOADING -->
        <LoadingCard
            v-if="loading"
            text="Cargando ganancias..."
        />

        <!-- EMPTY -->
        <EmptyState
            v-else-if="!earnings.length"
            text="No hay registros disponibles"
        />

        <!-- TABLE -->
        <DataTable
            v-else
            title="Ganancias"
            description="Listado de ingresos registrados"
            :columns="columns"
            :items="earnings"
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

            <!-- PLATFORM -->
            <template #platform="{ item }">

                <Badge variant="purple">

                    Platform #{{ item.platform_id }}

                </Badge>

            </template>

            <!-- TOKENS -->
            <template #amount="{ item }">

                <span class="text-white font-medium">

                    {{ item.amount }}

                </span>

            </template>

            <!-- USD -->
            <template #amount_usd="{ item }">

                <Badge variant="green">

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

const earnings = ref([])

const loading = ref(false)

const columns = [

    {
        key: 'model',
        label: 'Modelo',
    },

    {
        key: 'platform',
        label: 'Plataforma',
    },

    {
        key: 'amount',
        label: 'Tokens',
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

const loadEarnings = async () => {

    try {

        loading.value = true

        const response = await api.get('/earnings')

        earnings.value = response.data.data

    } catch (error) {

        console.error(error)

    } finally {

        loading.value = false
    }
}

onMounted(() => {

    loadEarnings()
})

</script>

<style scoped>
</style>