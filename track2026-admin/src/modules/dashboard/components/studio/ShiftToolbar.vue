<template>

    <div class="rounded-2xl border border-gray-800 bg-gray-900 p-6">

        <div class="mb-6 flex items-center justify-between">

            <div>

                <h2 class="text-lg font-semibold text-white">
                    Panel Operativo
                </h2>

                <p class="text-sm text-gray-400">
                    Selecciona una modelo para comenzar a registrar movimientos financieros.
                </p>

            </div>

        </div>

        <div class="grid gap-4 lg:grid-cols-[1fr_auto]">

            <select
                v-model="selectedModelId"
                class="rounded-xl border border-gray-700 bg-gray-950 px-4 py-3 text-white focus:border-indigo-500 focus:outline-none"
            >

                <option :value="null">
                    Seleccionar modelo...
                </option>

                <option
                    v-for="model in models"
                    :key="model.id"
                    :value="model.id"
                >
                    {{ model.name }}
                </option>

            </select>

            <button
                @click="startShift"
                :disabled="!selectedModel"
                class="rounded-xl bg-indigo-600 px-6 py-3 font-medium text-white transition hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-50"
            >
                Iniciar turno
            </button>

        </div>

    </div>

</template>

<script setup>

import { computed, ref } from 'vue'

const props = defineProps({

    models: {
        type: Array,
        default: () => [],
    },

})

const emit = defineEmits([
    'start-shift',
])

const selectedModelId = ref(null)

const selectedModel = computed(() =>

    props.models.find(
        model => model.id === selectedModelId.value
    ) ?? null

)

function startShift() {

    if (!selectedModel.value) {
        return
    }

    emit(
        'start-shift',
        selectedModel.value
    )

    selectedModelId.value = null
}

</script>