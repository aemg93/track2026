<template>

    <button
        @click="selectShift"
        class="w-full rounded-2xl border p-5 text-left transition-all"
        :class="
            selected
                ? 'border-indigo-500 bg-indigo-500/10'
                : 'border-gray-800 bg-gray-900 hover:border-gray-700'
        "
    >

        <div class="flex items-center gap-4">

            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-500/10 text-lg font-bold text-indigo-400"
            >
                {{ initials }}
            </div>

            <div class="flex-1">

                <h3 class="font-semibold text-white">
                    {{ performance.name }}
                </h3>

                <p class="text-sm text-gray-400">
                    {{ performance.nickname || 'Sin nickname' }}
                </p>

            </div>

            <span
                class="rounded-full px-3 py-1 text-xs font-semibold"
                :class="
                    status === 'active'
                        ? 'bg-green-500/10 text-green-400'
                        : status === 'paused'
                            ? 'bg-yellow-500/10 text-yellow-400'
                            : 'bg-gray-500/10 text-gray-400'
                "
            >
                {{
                    status === 'active'
                        ? 'En turno'
                        : status === 'paused'
                            ? 'Pausado'
                            : 'Finalizado'
                }}
            </span>

        </div>

        <div class="mt-5 text-sm">

            <p class="text-gray-500">
                Estado del turno
            </p>

            <p class="font-semibold text-white capitalize">
                {{ status }}
            </p>

        </div>

    </button>

</template>

<script setup>

import { computed } from 'vue'


const props = defineProps({

    shiftId: {
        type: Number,
        required: true,
    },

    performance: {
        type: Object,
        required: true,
    },

    status: {
        type: String,
        default: 'finished',
    },

    selected: {
        type: Boolean,
        default: false,
    },

})


const emit = defineEmits([
    'select',
])


const initials = computed(() => {

    return props.performance.name
        ?.split(' ')
        .map(word => word.charAt(0))
        .join('')
        .substring(0, 2)
        .toUpperCase() || '?'

})


function selectShift() {

    emit(
        'select',
        {
            id: props.shiftId,
            performance: props.performance,
            status: props.status,
        }
    )

}

</script>