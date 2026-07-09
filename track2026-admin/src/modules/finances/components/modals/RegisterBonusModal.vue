<template>

    <Teleport to="body">

        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm"
        >

            <div
                class="w-full max-w-xl overflow-hidden rounded-3xl border border-gray-800 bg-gradient-to-br from-gray-900 to-gray-950 shadow-2xl"
            >

                <!-- HEADER -->

                <div
                    class="border-b border-gray-800 px-8 py-6"
                >

                    <p
                        class="text-xs uppercase tracking-[0.2em] text-gray-500"
                    >
                        Finanzas
                    </p>

                    <h2
                        class="mt-2 text-3xl font-bold text-white"
                    >
                        Registrar Bono
                    </h2>

                    <p
                        class="mt-2 text-gray-400"
                    >
                        Agrega una bonificación para esta modelo.
                    </p>

                </div>

                <!-- BODY -->

                <form
                    class="space-y-6 p-8"
                    @submit.prevent="save"
                >

                    <div>

                        <label
                            class="text-sm text-gray-300"
                        >
                            Fecha
                        </label>

                        <input
                            v-model="form.date"
                            type="date"
                            required
                            class="mt-2 w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-white"
                        >

                    </div>

                    <div>

                        <label
                            class="text-sm text-gray-300"
                        >
                            Valor del bono (USD)
                        </label>

                        <input
                            v-model="form.amount"
                            type="number"
                            min="0"
                            step="0.01"
                            required
                            class="mt-2 w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-white"
                        >

                    </div>

                    <div>

                        <label
                            class="text-sm text-gray-300"
                        >
                            Motivo
                        </label>

                        <textarea
                            v-model="form.reason"
                            rows="4"
                            required
                            placeholder="Ejemplo: Excelente desempeño durante la semana."
                            class="mt-2 w-full resize-none rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-white"
                        />

                    </div>

                    <div
                        class="flex justify-end gap-4 pt-4"
                    >

                        <button
                            type="button"
                            @click="close"
                            class="rounded-xl border border-gray-700 px-6 py-3 text-gray-300 hover:bg-gray-800"
                        >
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            :disabled="loading"
                            class="rounded-xl bg-green-600 px-6 py-3 font-semibold text-white hover:bg-green-500 disabled:opacity-50"
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

import api from '@/services/api'

const props = defineProps({

    performanceId: {
        type: Number,
        required: true,
    },

})

const emit = defineEmits([

    'close',
    'saved',

])

const loading = ref(false)

const today = () => {

    return new Date()
        .toISOString()
        .substring(0, 10)

}

const form = ref({

    date: today(),
    amount: '',
    reason: '',

})

function close() {

    emit('close')

}

function reset() {

    form.value = {

        date: today(),
        amount: '',
        reason: '',

    }

}

async function save() {

    loading.value = true

    try {

        await api.post(
            '/bonuses',
            {
                performance_id: props.performanceId,
                date: form.value.date,
                amount: Number(form.value.amount),
                reason: form.value.reason,
            }
        )

        emit('saved')

        reset()

        close()

    } catch (error) {

        console.error(
            'ERROR REGISTERING BONUS',
            error.response?.data ?? error
        )

    } finally {

        loading.value = false

    }

}

</script>

<style scoped>

input:focus,
textarea:focus {

    outline: none;
    border-color: #22c55e;

}

</style>