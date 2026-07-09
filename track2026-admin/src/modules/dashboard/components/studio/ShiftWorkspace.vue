<template>

    <div v-if="!selectedModel">

        <ShiftEmptyState />

    </div>


    <div
        v-else
        class="grid gap-6 xl:grid-cols-[360px_1fr]"
    >

        <div class="space-y-6">


            <ShiftSummary
                :model="selectedModel"
            />



            <ShiftActions

                :model="selectedModel"

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

    selectedModel: {

        type:Object,

        default:null,

    },

})



const emit = defineEmits([

    'earning',

    'bonus',

    'penalty',

    'deduction',

    'finish',

])



const timeline = computed(() =>

    props.selectedModel?.timeline ?? []

)



const performance = computed(() =>

    props.selectedModel?.current_performance ?? null

)



function handleAction(action){


    if(!props.selectedModel){

        return

    }



    emit(

        action,

        {

            model:
                props.selectedModel,


            performance:
                performance.value

        }

    )


}


</script>