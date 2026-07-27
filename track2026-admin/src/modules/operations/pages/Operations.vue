<template>

    <div class="space-y-8">

        <PageHeader
            title="Operaciones"
            description="Control operativo de turnos y actividad del estudio"
        />


        <LoadingCard
            v-if="loading"
            text="Cargando operaciones..."
        />


        <StudioPanel
            v-else-if="dashboard && finance && operations"
            :dashboard="dashboard"
            :finance="finance"
            :operations="operations"

            @start-shift="startPerformanceShift"

            @earning="modalHandlers.revenue"
            @bonus="modalHandlers.bonus"
            @penalty="modalHandlers.penalty"
            @deduction="modalHandlers.deduction"

            @pause="pausePerformanceShift"
            @resume="resumePerformanceShift"
            @finish="finishPerformanceShift"
        />


        <EmptyState
            v-else
            text="No hay información operativa disponible"
        />



        <RegisterRevenueModal
            v-if="modals.revenue"
            :performance-id="selectedPerformance?.id"
            :platforms="selectedPerformance?.platforms ?? []"
            @close="closeModal('revenue')"
            @saved="handleFinanceSaved"
        />


        <RegisterBonusModal
            v-if="modals.bonus"
            :performance-id="selectedPerformance?.id"
            @close="closeModal('bonus')"
            @saved="handleFinanceSaved"
        />


        <RegisterPenaltyModal
            v-if="modals.penalty"
            :performance-id="selectedPerformance?.id"
            @close="closeModal('penalty')"
            @saved="handleFinanceSaved"
        />


        <RegisterDeductionModal
            v-if="modals.deduction"
            :performance-id="selectedPerformance?.id"
            @close="closeModal('deduction')"
            @saved="handleFinanceSaved"
        />


    </div>

</template>



<script setup>

import { 
    ref,
    reactive,
    onMounted
} from 'vue'


import PageHeader from '@/components/ui/PageHeader.vue'
import LoadingCard from '@/components/ui/LoadingCard.vue'
import EmptyState from '@/components/ui/EmptyState.vue'


import StudioPanel from '@/modules/operations/components/StudioPanel.vue'


import RegisterRevenueModal from '@/modules/finances/components/modals/RegisterRevenueModal.vue'
import RegisterBonusModal from '@/modules/finances/components/modals/RegisterBonusModal.vue'
import RegisterPenaltyModal from '@/modules/finances/components/modals/RegisterPenaltyModal.vue'
import RegisterDeductionModal from '@/modules/finances/components/modals/RegisterDeductionModal.vue'


import dashboardService from '@/modules/dashboard/services/dashboardService'


import { useShifts } from '@/modules/operations/composables/useShifts'



const dashboard = ref(null)

const finance = ref(null)

const operations = ref(null)

const loading = ref(false)


const selectedPerformance = ref(null)



const modals = reactive({

    revenue:false,

    bonus:false,

    penalty:false,

    deduction:false,

})



const {
    startShift,
    pauseShift,
    resumeShift,
    finishShift,

} = useShifts()





function openModal(name, performance){

    if(!performance?.id) return

    selectedPerformance.value = performance

    modals[name] = true

}



function closeModal(name){

    modals[name] = false


    if(!Object.values(modals).some(Boolean)){

        selectedPerformance.value = null

    }

}



function closeModals(){

    Object.keys(modals).forEach(name=>{

        modals[name] = false

    })


    selectedPerformance.value = null

}




const modalHandlers = {

    revenue: performance =>
        openModal('revenue', performance),


    bonus: performance =>
        openModal('bonus', performance),


    penalty: performance =>
        openModal('penalty', performance),


    deduction: performance =>
        openModal('deduction', performance),

}





async function reload(){

    try {

        const data = await dashboardService.index()


        dashboard.value = data.dashboard

        finance.value = data.finance

        operations.value = data.operations


    } catch(error){

        dashboard.value = null

        finance.value = null

        operations.value = null

        console.error(error)

    } finally {

        loading.value = false

    }

}





async function startPerformanceShift(performance){

    if(!performance?.id) return

    await startShift(performance.id)

    await reload()

}





async function pausePerformanceShift(shift){

    if(!shift?.id) return

    await pauseShift(shift.id)

    await reload()

}





async function resumePerformanceShift(shift){

    if(!shift?.id) return

    await resumeShift(shift.id)

    await reload()

}





async function finishPerformanceShift(shift){

    if(!shift?.id) return

    await finishShift(shift.id)

    await reload()

}





async function handleFinanceSaved(){

    closeModals()

    await reload()

}





onMounted(()=>{

    loading.value = true

    reload()

})


</script>