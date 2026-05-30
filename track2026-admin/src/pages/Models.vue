<template>

    <div class="space-y-6 h-[calc(100vh-120px)] flex flex-col">

        <!-- HEADER -->
        <div class="flex items-center justify-between shrink-0">

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

        <!-- FILTROS -->
        <div class="shrink-0">

            <ModelsFilters
                :search="search"
                :status="status"
                :sort-by="sortBy"
                @update="handleFilters"
            />

        </div>

        <!-- ZONA SCROLL -->
        <div
            class="flex-1 overflow-y-auto pr-2"
            ref="scrollContainer"
        >

            <ModelsTable
                :models="models"
                @view="handleView"
                @edit="handleEdit"
                @delete="handleDelete"
            />

            <!-- LOADING -->
            <div
                v-if="loading"
                class="text-center py-6 text-gray-400"
            >
                Cargando modelos...
            </div>

            <!-- OBSERVER -->
            <div
                ref="observerTarget"
                class="h-10"
            ></div>

            <!-- FIN -->
            <div
                v-if="meta.page >= meta.pages && models.length"
                class="text-center py-6 text-gray-500 text-sm"
            >
                No hay más modelos para mostrar
            </div>

        </div>

    </div>

</template>

<script setup>

import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import api from '../services/api'

import ModelsTable from '../components/models/ModelsTable.vue'
import ModelsFilters from '../components/models/ModelsFilters.vue'

const models = ref([])

const search = ref('')
const status = ref('')
const sortBy = ref('ranking_score')
const order = ref('desc')

const page = ref(1)
const limit = ref(10)

const loading = ref(false)

const meta = ref({
    page: 1,
    pages: 1,
    total: 0
})

const observerTarget = ref(null)
const scrollContainer = ref(null)

let observer = null

const loadModels = async (append = false) => {

    if (loading.value) return

    loading.value = true

    try {

        const { data } = await api.get('/models', {

            params: {
                page: page.value,
                limit: limit.value,
                search: search.value,
                status: status.value,
                sortBy: sortBy.value,
                order: order.value,
            }

        })

        if (append) {

            models.value.push(...(data.data || []))

        } else {

            models.value = data.data || []

        }

        meta.value = data.meta

    } catch (error) {

        console.error(error)

    } finally {

        loading.value = false

    }

}

const handleFilters = async (filters) => {

    search.value = filters.search || ''

    status.value = filters.status || ''

    sortBy.value = filters.sortBy || 'ranking_score'

    page.value = 1

    models.value = []

    await loadModels(false)

}

const loadMore = async () => {

    if (loading.value) return

    if (page.value >= meta.value.pages) return

    page.value++

    await loadModels(true)

}

const createObserver = () => {

    observer = new IntersectionObserver(

        async (entries) => {

            const entry = entries[0]

            if (entry.isIntersecting) {

                await loadMore()

            }

        },

        {
            root: scrollContainer.value,
            threshold: 0.1
        }

    )

    if (observerTarget.value) {

        observer.observe(observerTarget.value)

    }

}

const handleView = (model) => {

    console.log(model)

}

const handleEdit = (model) => {

    console.log(model)

}

const handleDelete = (model) => {

    console.log(model)

}

onMounted(async () => {

    await loadModels()

    await nextTick()

    createObserver()

})

onUnmounted(() => {

    if (observer) {

        observer.disconnect()

    }

})

</script>