```vue
<template>

<Teleport to="body">

<div
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm"
>

    <div
        class="w-full max-w-xl bg-gradient-to-br from-gray-900 to-gray-950 border border-gray-800 rounded-3xl shadow-2xl overflow-hidden"
    >

        <!-- HEADER -->

        <div
            class="px-8 py-6 border-b border-gray-800"
        >

            <p
                class="text-xs uppercase tracking-[0.2em] text-gray-500"
            >
                Finanzas
            </p>

            <h2
                class="text-3xl font-bold text-white mt-2"
            >
                Registrar Ganancia
            </h2>

            <p
                class="text-gray-400 mt-2"
            >
                Registrar una nueva ganancia para el período seleccionado.
            </p>

        </div>

        <!-- BODY -->

        <form
            class="p-8 space-y-6"
            @submit.prevent="save"
        >

            <div>

                <label class="text-gray-300 text-sm">
                    Inicio del período
                </label>

                <input

                    v-model="form.period_start"

                    type="date"

                    class="mt-2 w-full rounded-xl bg-gray-800 border border-gray-700 px-4 py-3 text-white"

                    required

                >

            </div>

            <div>

                <label class="text-gray-300 text-sm">
                    Fin del período
                </label>

                <input

                    v-model="form.period_end"

                    type="date"

                    class="mt-2 w-full rounded-xl bg-gray-800 border border-gray-700 px-4 py-3 text-white"

                    required

                >

            </div>

            <div>

                <label class="text-gray-300 text-sm">
                    Ganancia (USD)
                </label>

                <input

                    v-model="form.gross_usd"

                    type="number"

                    min="0"

                    step="0.01"

                    class="mt-2 w-full rounded-xl bg-gray-800 border border-gray-700 px-4 py-3 text-white"

                    required

                >

            </div>

            <!-- BOTONES -->

            <div
                class="flex justify-end gap-4 pt-4"
            >

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

import { ref } from 'vue'

import api from '../../../../services/api'

const props = defineProps({

    performanceId: Number

})

const emit = defineEmits([

    'close',

    'saved'

])

const loading = ref(false)

const form = ref({

    period_start: '',

    period_end: '',

    gross_usd: ''

})

const close = () => {

    emit('close')

}

const reset = () => {

    form.value = {

        period_start: '',

        period_end: '',

        gross_usd: ''

    }

}

const save = async () => {

    loading.value = true

    try {

        await api.post(

            '/earnings',

            {

                performance_id: props.performanceId,

                period_start: form.value.period_start,

                period_end: form.value.period_end,

                gross_usd: form.value.gross_usd

            }

        )

        emit('saved')

        reset()

        close()

    }

    catch (error) {

        console.error(error)

    }

    finally {

        loading.value = false

    }

}

</script>

<style scoped>

input:focus{

    outline:none;

    border-color:#22c55e;

}

</style>

