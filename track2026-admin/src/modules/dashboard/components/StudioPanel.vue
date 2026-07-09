<template>

    <section
        class="rounded-3xl border border-gray-800 bg-gradient-to-br from-gray-900 to-gray-950 p-6"
    >

        <ShiftToolbar
            :models="models"
            @start-shift="handleModelSelection"
        />

        <div class="mt-6">

            <ShiftModelList
                :models="models"
                :selected-model="selectedModel"
                @select="handleModelSelection"
            />

        </div>

        <div class="mt-8">

            <ShiftWorkspace
                :selected-model="selectedModel"
                @earning="registerEarning"
                @bonus="registerBonus"
                @penalty="registerPenalty"
                @deduction="registerDeduction"
                @finish="finishShift"
            />

        </div>

    </section>

</template>

<script setup>

import {
    computed,
    ref
} from 'vue'

import ShiftToolbar from './studio/ShiftToolbar.vue'
import ShiftModelList from './studio/ShiftModelList.vue'
import ShiftWorkspace from './studio/ShiftWorkspace.vue'

const props = defineProps({

    dashboard: {
        type: Object,
        required: true,
    },

    finance: {
        type: Object,
        required: true,
    },

})

const emit = defineEmits([

    'earning',
    'bonus',
    'penalty',
    'deduction',
    'finish',

])

const models = computed(() => {

    return props.dashboard?.models ?? []

})

const selectedModel = ref(null)

const selectedPerformance = computed(() => {

    return selectedModel.value ?? null

})

function handleModelSelection(model) {

    if (!model) {
        return
    }

    selectedModel.value = model

}

function registerEarning() {

    emit(
        'earning',
        selectedModel.value,
        selectedPerformance.value
    )

}

function registerBonus() {

    emit(
        'bonus',
        selectedModel.value,
        selectedPerformance.value
    )

}

function registerPenalty() {

    emit(
        'penalty',
        selectedModel.value,
        selectedPerformance.value
    )

}

function registerDeduction() {

    emit(
        'deduction',
        selectedModel.value,
        selectedPerformance.value
    )

}

function finishShift() {

    emit(
        'finish',
        selectedModel.value,
        selectedPerformance.value
    )

}

</script>