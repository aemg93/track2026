<template>

<Teleport to="body">

<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">

    <div class="w-full max-w-xl bg-gradient-to-br from-gray-900 to-gray-950 border border-gray-800 rounded-3xl shadow-2xl overflow-hidden">

        <div class="px-8 py-6 border-b border-gray-800">

            <p class="text-xs uppercase tracking-[0.2em] text-gray-500">
                Finanzas
            </p>

            <h2 class="text-3xl font-bold text-white mt-2">
                Registrar Ganancia
            </h2>

            <p class="text-gray-400 mt-2">
                Registrar ingreso generado por plataforma.
            </p>

        </div>


        <form
            class="p-8 space-y-6"
            @submit.prevent="save"
        >


            <div>

                <label class="text-gray-300 text-sm">
                    Plataforma
                </label>


                <select
                    v-model="form.platform_id"
                    required
                    class="mt-2 w-full rounded-xl bg-gray-800 border border-gray-700 px-4 py-3 text-white"
                >

                    <option value="">
                        Seleccionar plataforma
                    </option>


                    <option
                        v-for="platform in platforms"
                        :key="platform.id"
                        :value="platform.id"
                    >

                        {{ platform.name }}

                    </option>


                </select>

            </div>



            <div>

                <label class="text-gray-300 text-sm">
                    Fecha del ingreso
                </label>


                <input

                    v-model="form.earned_at"

                    type="date"

                    required

                    class="mt-2 w-full rounded-xl bg-gray-800 border border-gray-700 px-4 py-3 text-white"

                >

            </div>



            <div>

                <label class="text-gray-300 text-sm">
                    Tipo de ingreso
                </label>


                <select

                    v-model="form.original_currency"

                    required

                    class="mt-2 w-full rounded-xl bg-gray-800 border border-gray-700 px-4 py-3 text-white"

                >

                    <option value="usd">
                        USD
                    </option>

                    <option value="tokens">
                        Tokens
                    </option>


                </select>

            </div>




            <div>

                <label class="text-gray-300 text-sm">

                    {{
                        form.original_currency === 'tokens'
                            ? 'Cantidad de tokens'
                            : 'Cantidad USD'
                    }}

                </label>


                <input

                    v-model="form.original_amount"

                    type="number"

                    min="0"

                    step="0.01"

                    required

                    class="mt-2 w-full rounded-xl bg-gray-800 border border-gray-700 px-4 py-3 text-white"

                >

            </div>



            <div class="flex justify-end gap-4 pt-4">


                <button

                    type="button"

                    @click="close"

                    class="px-6 py-3 rounded-xl border border-gray-700 text-gray-300 hover:bg-gray-800"

                >

                    Cancelar

                </button>



                <button

                    type="submit"

                    :disabled="loading"

                    class="px-6 py-3 rounded-xl bg-green-600 hover:bg-green-500 text-white font-semibold disabled:opacity-50"

                >

                    {{ loading ? 'Guardando...' : 'Guardar' }}


                </button>


            </div>


        </form>


    </div>

</div>

</Teleport>

</template>


<script setup>

import {
    ref,
    computed,
    watch
} from 'vue'

import api from '@/services/api'


const props = defineProps({

    performanceId: {
        type: Number,
        required: true
    },

    platforms: {
        type: Array,
        default: () => []
    }

})


const emit = defineEmits([

    'close',

    'saved'

])


const loading = ref(false)



const form = ref({

    platform_id: '',

    earned_at: new Date()
        .toISOString()
        .substring(0, 10),

    original_amount: '',

    original_currency: 'usd'

})



/*
|--------------------------------------------------------------------------
| Plataforma seleccionada
|--------------------------------------------------------------------------
*/

const selectedPlatform = computed(() => {

    return props.platforms.find(
        platform =>
            platform.id == form.value.platform_id
    )

})



/*
|--------------------------------------------------------------------------
| Sincronizar moneda según plataforma
|--------------------------------------------------------------------------
*/

watch(
    selectedPlatform,
    (platform) => {

        if (!platform) {
            return
        }


        form.value.original_currency =
            platform.type === 'token'
                ? 'tokens'
                : 'usd'

    }
)



/*
|--------------------------------------------------------------------------
| Cerrar modal
|--------------------------------------------------------------------------
*/

const close = () => {

    emit('close')

}



/*
|--------------------------------------------------------------------------
| Reset formulario
|--------------------------------------------------------------------------
*/

const reset = () => {


    form.value = {

        platform_id: '',

        earned_at: new Date()
            .toISOString()
            .substring(0, 10),

        original_amount: '',

        original_currency: 'usd'

    }


}



/*
|--------------------------------------------------------------------------
| Guardar earning
|--------------------------------------------------------------------------
*/

const save = async () => {


    loading.value = true


    try {


        const payload = {


            performance_id:
                props.performanceId,


            platform_id:
                Number(form.value.platform_id),


            earned_at:
                form.value.earned_at,


            original_amount:
                Number(form.value.original_amount),


            original_currency:
                form.value.original_currency


        }



        console.log(
            'CREATING EARNING:',
            payload
        )



        await api.post(
            '/earnings',
            payload
        )



        emit('saved')


        reset()


        close()


    }


   catch(error) {

    console.error(
        'ERROR CREATING EARNING:',
        error.response?.data
    )

}

    finally {


        loading.value = false


    }


}


</script>


<style scoped>

input:focus,
select:focus{

    outline:none;

    border-color:#22c55e;

}

</style>