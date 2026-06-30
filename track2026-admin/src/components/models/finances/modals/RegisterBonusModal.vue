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
                Registrar Bono
            </h2>

            <p
                class="text-gray-400 mt-2"
            >
                Agrega una bonificación para esta modelo.
            </p>

        </div>

        <!-- BODY -->

        <form
            class="p-8 space-y-6"
            @submit.prevent="save"
        >

            <div>

                <label class="text-gray-300 text-sm">
                    Fecha
                </label>

                <input

                    v-model="form.date"

                    type="date"

                    class="mt-2 w-full rounded-xl bg-gray-800 border border-gray-700 px-4 py-3 text-white"

                    required

                >

            </div>

            <div>

                <label class="text-gray-300 text-sm">
                    Valor del bono (USD)
                </label>

                <input

                    v-model="form.amount"

                    type="number"

                    min="0"

                    step="0.01"

                    class="mt-2 w-full rounded-xl bg-gray-800 border border-gray-700 px-4 py-3 text-white"

                    required

                >

            </div>

            <div>

                <label class="text-gray-300 text-sm">
                    Motivo
                </label>

                <textarea

                    v-model="form.reason"

                    rows="4"

                    class="mt-2 w-full rounded-xl bg-gray-800 border border-gray-700 px-4 py-3 text-white resize-none"

                    placeholder="Ejemplo: Excelente desempeño durante la semana."

                    required

                />

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

                    {{ loading ? 'Guardando...' : 'Guardar Bono' }}

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

    date: '',

    amount: '',

    reason: ''

})

const close = () => {

    emit('close')

}

const reset = () => {

    form.value = {

        date: '',

        amount: '',

        reason: ''

    }

}

const save = async () => {

    loading.value = true

    try {

        await api.post(

            '/bonuses',

            {

                performance_id: props.performanceId,

                date: form.value.date,

                amount: form.value.amount,

                reason: form.value.reason

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

input:focus,
textarea:focus{

    outline:none;

    border-color:#22c55e;

}

</style>
