<template>

    <div class="bg-gray-900 border border-gray-800 rounded-3xl p-5">

        <div class="flex flex-col md:flex-row gap-4 relative">

            <!-- SEARCH -->
            <div class="relative flex-1">

                <input
                    v-model="localSearch"
                    @input="handleSearch"
                    type="text"
                    placeholder="Buscar modelo..."
                    class="w-full bg-gray-950 border border-gray-800 rounded-2xl px-4 py-3 text-white outline-none focus:border-blue-500"
                />

                <!-- AUTOCOMPLETE -->
                <div
                    v-if="suggestions.length > 0"
                    class="absolute left-0 right-0 z-50 mt-2 bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden shadow-lg"
                >

                    <div
                        v-for="item in suggestions"
                        :key="item.id"
                        @click="selectModel(item)"
                        class="px-4 py-3 hover:bg-gray-800 cursor-pointer text-white"
                    >

                        <div class="font-semibold">
                            {{ item.name }}
                        </div>

                        <div class="text-xs text-gray-400">
                            #{{ item.id }}
                        </div>

                    </div>

                </div>

            </div>

            <!-- STATUS -->
            <select
                v-model="localStatus"
                @change="emitChange"
                class="bg-gray-950 border border-gray-800 rounded-2xl px-4 py-3 text-white outline-none focus:border-blue-500"
            >

                <option value="">
                    Todos
                </option>

                <option value="active">
                    Activos
                </option>

                <option value="inactive">
                    Inactivos
                </option>

            </select>

            <!-- SORT -->
            <select
                v-model="localSort"
                @change="emitChange"
                class="bg-gray-950 border border-gray-800 rounded-2xl px-4 py-3 text-white outline-none focus:border-blue-500"
            >

                <option value="ranking_score">
                    Ranking
                </option>

                <option value="hours_streamed">
                    Horas
                </option>

                <option value="name">
                    Nombre
                </option>

            </select>

        </div>

    </div>

</template>

<script setup>

import { ref } from 'vue'
import api from '../../services/api'

const props = defineProps({

    search: String,
    status: String,
    sortBy: String,

})

const emit = defineEmits(['update'])

const localSearch = ref(props.search || '')
const localStatus = ref(props.status || '')
const localSort = ref(props.sortBy || 'ranking_score')

const suggestions = ref([])

let timeout = null

/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

const handleSearch = () => {

    clearTimeout(timeout)

    timeout = setTimeout(async () => {

        emitChange()

        if (localSearch.value.trim() === '') {

            suggestions.value = []

            return

        }

        try {

            console.log('SEARCH =>', localSearch.value)

            const { data } = await api.get('/models', {

                params: {
                    search: localSearch.value,
                    limit: 5,
                },

            })

            console.log('AUTOCOMPLETE =>', data)

            suggestions.value = data.data || []

            console.log('SUGGESTIONS =>', suggestions.value)

        } catch (error) {

            console.error('Autocomplete error:', error)

            suggestions.value = []

        }

    }, 300)

}

/*
|--------------------------------------------------------------------------
| SELECT MODEL
|--------------------------------------------------------------------------
*/

const selectModel = (model) => {

    localSearch.value = model.name

    suggestions.value = []

    emitChange()

}

/*
|--------------------------------------------------------------------------
| EMIT FILTERS
|--------------------------------------------------------------------------
*/

const emitChange = () => {

    emit('update', {

        search: localSearch.value,
        status: localStatus.value,
        sortBy: localSort.value,

    })

}

</script>