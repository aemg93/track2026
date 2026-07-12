<template>

    <section
        class="rounded-3xl border border-gray-800 bg-gradient-to-br from-gray-900 to-gray-950 p-6"
    >

        <ShiftToolbar
            :performances="performances"
            @start-shift="startShift"
        />

        <div class="mt-6">

            <ShiftPerformanceList

                :shifts="activeShifts"

                :selected-shift="selectedShift"

                @select="selectShift"

            />

        </div>

        <div class="mt-8">

            <ShiftWorkspace

                :selected-performance="selectedPerformance"

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
import ShiftPerformanceList from './studio/ShiftPerformanceList.vue'
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

    operations: {
        type: Object,
        required: true,
    },

})

const emit = defineEmits([

    'start-shift',
    'earning',
    'bonus',
    'penalty',
    'deduction',
    'finish',

])

/*
|--------------------------------------------------------------------------
| Performances disponibles
|--------------------------------------------------------------------------
*/

const performances = computed(() => {

    return (
        props.dashboard?.performances ??
        props.dashboard?.models ??
        []
    )

})

/*
|--------------------------------------------------------------------------
| Turnos activos
|--------------------------------------------------------------------------
*/

const activeShifts = computed(() => {

    return (
        props.operations?.active_shifts ??
        []
    )

})

/*
|--------------------------------------------------------------------------
| Turno seleccionado
|--------------------------------------------------------------------------
*/

const selectedShift = ref(null)

const selectedPerformance = computed(() => {

    return (
        selectedShift.value?.performance ??
        null
    )

})

/*
|--------------------------------------------------------------------------
| Iniciar turno
|--------------------------------------------------------------------------
*/

function startShift(performance) {

    emit(
        'start-shift',
        performance
    )

}

/*
|--------------------------------------------------------------------------
| Seleccionar turno
|--------------------------------------------------------------------------
*/

function selectShift(shift) {

    console.log(
        '========== SELECT SHIFT =========='
    )

    console.log(
        'SHIFT:',
        shift
    )

    console.log(
        'ID:',
        shift?.id
    )

    console.log(
        'STATUS:',
        shift?.status
    )

    console.log(
        'STARTED AT:',
        shift?.started_at
    )

    console.log(
        'ENDED AT:',
        shift?.ended_at
    )

    console.log(
        'DURATION:',
        shift?.duration_minutes
    )

    console.log(
        'PERFORMANCE:',
        shift?.performance
    )

    console.log(
        'PLATFORMS:',
        shift?.performance?.platforms
    )

    console.log(
        'FINANCIAL:',
        shift?.performance?.financial
    )

    console.log(
        'TIMELINE:',
        shift?.performance?.timeline
    )

    console.log(
        '=================================='
    )

    selectedShift.value = shift

}

/*
|--------------------------------------------------------------------------
| Acciones financieras
|--------------------------------------------------------------------------
*/

function registerEarning(performance) {

    emit(
        'earning',
        performance
    )

}

function registerBonus(performance) {

    emit(
        'bonus',
        performance
    )

}

function registerPenalty(performance) {

    emit(
        'penalty',
        performance
    )

}

function registerDeduction(performance) {

    emit(
        'deduction',
        performance
    )

}

/*
|--------------------------------------------------------------------------
| Finalizar turno
|--------------------------------------------------------------------------
*/

function finishShift() {

    if (!selectedShift.value) {
        return
    }

    emit(
        'finish',
        selectedShift.value
    )

}

</script>