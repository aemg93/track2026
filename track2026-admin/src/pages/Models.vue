<template>

    <div class="space-y-8">

        <!-- HEADER -->
        <div class="flex items-center justify-between">

            <div>

                <h1 class="text-4xl font-bold text-white">
                    Modelos
                </h1>

                <p class="text-gray-400 mt-2">
                    Gestión de modelos del estudio
                </p>

            </div>

            <button
                class="px-6 py-3 rounded-2xl bg-blue-500 hover:bg-blue-600 transition text-white font-semibold"
            >
                Nueva Modelo
            </button>

        </div>

        <!-- FILTERS -->
        <ModelsFilters
            :search="search"
            :status="status"
            :sort-by="sortBy"
            @update="handleFilters"
        />

        <!-- TABLE -->
        <ModelsTable
            :models="models"
            @view="handleView"
            @edit="handleEdit"
            @delete="handleDelete"
        />

        <!-- PAGINATION -->
        <ModelsPagination
            :meta="meta"
            @next="nextPage"
            @prev="prevPage"
        />

    </div>

</template>

<script setup>

import { ref, onMounted } from 'vue'
import api from '../services/api'

import ModelsTable from '../components/models/ModelsTable.vue'
import ModelsFilters from '../components/models/ModelsFilters.vue'
import ModelsPagination from '../components/models/ModelsPagination.vue'

/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const models = ref([])

const search = ref('')
const status = ref('')
const sortBy = ref('ranking_score')
const order = ref('desc')

const page = ref(1)
const limit = ref(5)

const meta = ref({
    page: 1,
    pages: 1,
    total: 0
})

/*
|--------------------------------------------------------------------------
| LOAD MODELS
|--------------------------------------------------------------------------
*/

const loadModels = async () => {

    try {

        console.log('SEARCH VALUE =>', search.value)

        const { data } = await api.get('/models', {
            params: {
                page: page.value,
                limit: limit.value,
                search: search.value,
                status: status.value,
                sortBy: sortBy.value,
                order: order.value,
            },
        })

        console.log('MODELS RESPONSE =>', data)

        models.value = data.data || []

        meta.value = data.meta || {
            page: 1,
            pages: 1,
            total: 0
        }

    } catch (error) {

        console.error('Error loading models:', error)

        models.value = []

    }

}

/*
|--------------------------------------------------------------------------
| FILTERS
|--------------------------------------------------------------------------
*/

const handleFilters = (filters) => {

    console.log('FILTERS =>', filters)

    search.value = filters.search || ''
    status.value = filters.status || ''
    sortBy.value = filters.sortBy || 'ranking_score'

    page.value = 1

    loadModels()

}

/*
|--------------------------------------------------------------------------
| PAGINATION
|--------------------------------------------------------------------------
*/

const nextPage = () => {

    if (page.value < meta.value.pages) {

        page.value++

        loadModels()

    }

}

const prevPage = () => {

    if (page.value > 1) {

        page.value--

        loadModels()

    }

}

/*
|--------------------------------------------------------------------------
| ACTIONS
|--------------------------------------------------------------------------
*/

const handleView = (model) => {

    console.log('VIEW =>', model)

}

const handleEdit = (model) => {

    console.log('EDIT =>', model)

}

const handleDelete = (model) => {

    console.log('DELETE =>', model)

}

/*
|--------------------------------------------------------------------------
| INIT
|--------------------------------------------------------------------------
*/

onMounted(() => {

    loadModels()

})

</script>