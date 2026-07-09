<template>

    <div
        v-if="models.length"
        class="grid gap-4 md:grid-cols-2 xl:grid-cols-4"
    >

        <ShiftModelItem

            v-for="model in models"

            :key="model.id"

            :model="model"

            :selected="selectedModel?.id === model.id"

            @select="selectModel"

        />

    </div>

    <div
        v-else
        class="rounded-2xl border border-dashed border-gray-700 bg-gray-900/40 py-12 text-center"
    >

        <p class="text-gray-400">

            No hay modelos disponibles.

        </p>

    </div>

</template>

<script setup>

import ShiftModelItem from './ShiftModelItem.vue'

const props = defineProps({

    models: {
        type: Array,
        default: () => []
    },

    selectedModel: {
        type: Object,
        default: null
    }

})

const emit = defineEmits([
    'select'
])

function selectModel(model) {

    emit(
        'select',
        model
    )

}

</script>