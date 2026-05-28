<template>

    <div class="space-y-8">

        <!-- HEADER -->
        <PageHeader
            title="Bonos"
            description="Bonuses and rewards earned by models"
        >

            <div class="bg-green-600/15 text-green-400 px-5 py-3 rounded-2xl font-semibold border border-green-500/30">
                {{ bonuses.length }} registros
            </div>

        </PageHeader>

        <!-- LOADING -->
        <LoadingCard
            v-if="loading"
            text="Cargando bonos..."
        />

        <!-- EMPTY -->
        <EmptyState
            v-else-if="!bonuses.length"
            text="No hay bonos registrados"
        />

        <!-- TABLE -->
        <DataTable
            v-else
            title="Bonos"
            description="Listado de recompensas generadas"
            :columns="columns"
            :items="bonuses"
        >

            <!-- MODEL -->
            <template #model="{ item }">

                <div>

                    <p class="font-semibold text-white">
                        {{ item.performance?.name }}
                    </p>

                    <p class="text-xs text-gray-500">
                        User #{{ item.user_id }}
                    </p>

                </div>

            </template>

            <!-- AMOUNT (CENTERED + CLEAN MONEY STYLE) -->
            <template #amount="{ item }">

                <div class="flex flex-col items-start">

                    <!-- MAIN AMOUNT -->
                    <div class="flex items-center gap-1">

                        <span class="text-green-400 font-semibold text-base">
                            +
                        </span>

                        <span class="text-white font-semibold text-base">
                            {{ Number(item.amount).toFixed(2) }}
                        </span>

                    </div>

                    <!-- CURRENCY (SECONDARY CLEAN LINE) -->
                    <span class="text-xs text-gray-500 tracking-wide">
                        USD ${{ Number(item.amount_usd).toFixed(2) }}
                    </span>

                </div>

            </template>

            <!-- DATE -->
            <template #date="{ item }">

                <span class="text-gray-400 text-sm">
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
import DataTable from '../components/ui/DataTable.vue'

const bonuses = ref([])
const loading = ref(false)

/*
|--------------------------------------------------------------------------
| TABLE STRUCTURE
|--------------------------------------------------------------------------
*/

const columns = [

    { key: 'model', label: 'Modelo' },
    { key: 'amount', label: 'Monto' },
    { key: 'date', label: 'Fecha' },

]

/*
|--------------------------------------------------------------------------
| FETCH DATA
|--------------------------------------------------------------------------
*/

const loadBonuses = async () => {

    try {

        loading.value = true

        const response = await api.get('/bonuses')

        bonuses.value = response.data.data

    } catch (error) {

        console.error(error)

    } finally {

        loading.value = false
    }
}

onMounted(() => {
    loadBonuses()
})

</script>

<style scoped>
</style>