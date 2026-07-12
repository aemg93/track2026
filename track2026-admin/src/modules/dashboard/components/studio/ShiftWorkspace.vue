<template>

    <div v-if="!selectedPerformance">

        <ShiftEmptyState />

    </div>


    <div
        v-else
        class="grid gap-6 xl:grid-cols-[360px_1fr]"
    >

        <div class="space-y-6">

            <ShiftSummary
                :performance="selectedPerformance"
            />


            <ShiftActions

                :performance="selectedPerformance"

                @earning="handleAction('earning')"

                @bonus="handleAction('bonus')"

                @penalty="handleAction('penalty')"

                @deduction="handleAction('deduction')"

                @finish="handleAction('finish')"

            />

        </div>


        <ShiftTimeline

            :timeline="timeline"

        />


    </div>

</template>


<script setup>

import {
    computed
} from 'vue'


import ShiftEmptyState from './ShiftEmptyState.vue'
import ShiftActions from './ShiftActions.vue'
import ShiftSummary from './ShiftSummary.vue'
import ShiftTimeline from './ShiftTimeline.vue'


const props = defineProps({

    selectedPerformance: {

        type: Object,

        default: null,

    },

})


const emit = defineEmits([

    'earning',

    'bonus',

    'penalty',

    'deduction',

    'finish',

])


const timeline = computed(() => {

    return props.selectedPerformance?.timeline ?? []

})


function handleAction(action) {


    if (!props.selectedPerformance) {

        return

    }


    emit(
        action,
        props.selectedPerformance
    )


}


</script>