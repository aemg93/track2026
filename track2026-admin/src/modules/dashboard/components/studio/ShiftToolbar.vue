<template>
    <div class="rounded-2xl border border-gray-800 bg-gray-900 p-6">

        <div class="mb-6 flex items-center justify-between">

            <div>

                <h2 class="text-lg font-semibold text-white">
                    Panel Operativo
                </h2>

                <p class="text-sm text-gray-400">
                    Selecciona una modelo para comenzar un turno.
                </p>

            </div>

        </div>

        <div class="grid gap-4 lg:grid-cols-[1fr_auto]">

            <select
                v-model="selectedPerformanceId"
                class="rounded-xl border border-gray-700 bg-gray-950 px-4 py-3 text-white"
            >

                <option :value="null">
                    Seleccionar modelo...
                </option>

                <option
                    v-for="performance in performances"
                    :key="performance.id"
                    :value="performance.id"
                >
                    {{ performance.name }}
                </option>

            </select>

            <button
                @click="startShift"
                :disabled="!selectedPerformance"
                class="rounded-xl bg-indigo-600 px-6 py-3 text-white disabled:opacity-50"
            >
                Iniciar turno
            </button>

        </div>

    </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
    performances: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits([
    'start-shift',
])

const selectedPerformanceId = ref(null)

const selectedPerformance = computed(() => {

    return props.performances.find(
        performance => Number(performance.id) === Number(selectedPerformanceId.value)
    ) ?? null

})

function startShift() {

    if (!selectedPerformance.value) {
        return
    }

    emit('start-shift', selectedPerformance.value)

    selectedPerformanceId.value = null

}
</script>