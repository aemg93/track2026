<template>
  <div
    class="
      bg-gradient-to-br
      from-gray-900
      to-gray-950
      border
      border-gray-800
      rounded-3xl
      p-6
      space-y-6
    "
  >
    <h2 class="text-xl font-bold text-white">
      Registrar movimiento financiero
    </h2>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Tipo -->
      <div>
        <label class="block text-sm text-gray-400 mb-2">
          Tipo de registro
        </label>
        <select
          v-model="type"
          class="
            w-full
            bg-gray-950
            border
            border-gray-700
            rounded-xl
            px-4
            py-3
            text-white
          "
        >
          <option value="">Seleccionar...</option>
          <option value="earning">Ganancia</option>
          <option value="bonus">Bono</option>
          <option value="penalty">Multa</option>
          <option value="deduction">Descuento</option>
        </select>
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-2">
          Modelo
        </label>
        <select
          v-model="performanceId"
          class="
            w-full
            bg-gray-950
            border
            border-gray-700
            rounded-xl
            px-4
            py-3
            text-white
          "
        >
          <option value="">Seleccionar...</option>
          <option
            v-for="performance in performances.filter(Boolean)"
            :key="performance.id"
            :value="performance.id"
          >
            {{ performance.nickname }}
          </option>
        </select>
      </div>
    </div>

    <!-- Botón continuar -->
    <div class="flex justify-end">
      <button
        @click="openModal"
        :disabled="!canContinue"
        class="
          px-6
          py-3
          rounded-xl
          bg-blue-600
          hover:bg-blue-700
          disabled:opacity-40
          disabled:cursor-not-allowed
          text-white
          font-semibold
        "
      >
        Continuar
      </button>
    </div>

    <RegisterRevenueModal
      v-if="showRevenue"
      :performance-id="performanceId"
      @close="close"
      @saved="saved"
    />

    <RegisterBonusModal
      v-if="showBonus"
      :performance-id="performanceId"
      @close="close"
      @saved="saved"
    />

    <RegisterPenaltyModal
      v-if="showPenalty"
      :performance-id="performanceId"
      @close="close"
      @saved="saved"
    />

    <RegisterDeductionModal
      v-if="showDeduction"
      :performance-id="performanceId"
      @close="close"
      @saved="saved"
    />
  </div>
</template>
<script setup>

import {
    ref,
    computed,
    onMounted
} from 'vue'


import api from '../../../services/api'


import RegisterRevenueModal from './modals/RegisterRevenueModal.vue'
import RegisterBonusModal from './modals/RegisterBonusModal.vue'
import RegisterPenaltyModal from './modals/RegisterPenaltyModal.vue'
import RegisterDeductionModal from './modals/RegisterDeductionModal.vue'

const emit = defineEmits([
    'saved'
])

const performances = ref([])

const type = ref('')
const performanceId = ref(null)

const showRevenue = ref(false)
const showBonus = ref(false)
const showPenalty = ref(false)
const showDeduction = ref(false)



const canContinue = computed(() => {

    return (

        type.value !== ''

        &&

        performanceId.value !== null

        &&

        performanceId.value !== ''

    )

})

const loadPerformances = async () => {

    try {


        const response = await api.get('/performances')


        console.log(
            'PERFORMANCES RESPONSE:',
            response.data
        )


        const payload = response.data



        if (
            Array.isArray(payload.data)
        ) {


            performances.value =
                payload.data.filter(Boolean)


        }


        else if (

            payload.data

            &&

            Array.isArray(payload.data.data)

        ) {


            performances.value =
                payload.data.data.filter(Boolean)


        }


        else {


            performances.value = []


        }



        console.log(
            'PERFORMANCES CARGADAS:',
            performances.value
        )


    }


    catch (error) {


        console.error(
            'ERROR CARGANDO PERFORMANCES:',
            error
        )


        performances.value = []


    }

}

const openModal = () => {


    close()



    switch (type.value) {


        case 'earning':

            showRevenue.value = true

            break



        case 'bonus':

            showBonus.value = true

            break



        case 'penalty':

            showPenalty.value = true

            break



        case 'deduction':

            showDeduction.value = true

            break


    }

    console.log({

        revenue: showRevenue.value,

        bonus: showBonus.value,

        penalty: showPenalty.value,

        deduction: showDeduction.value

    })


}

const close = () => {


    showRevenue.value = false

    showBonus.value = false

    showPenalty.value = false

    showDeduction.value = false


}

const saved = () => {


    close()
    emit('saved')


}

onMounted(() => {


    loadPerformances()


})

</script>
