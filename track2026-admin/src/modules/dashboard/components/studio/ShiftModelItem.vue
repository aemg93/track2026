<template>

    <button
        @click="emit('select', model)"
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
                    {{ model.name }}
                </h3>

                <p class="text-sm text-gray-400">
                    {{ model.nickname || 'Sin nickname' }}
                </p>

            </div>

            <span
                class="rounded-full px-3 py-1 text-xs font-semibold"
                :class="
                    model.active
                        ? 'bg-green-500/10 text-green-400'
                        : 'bg-red-500/10 text-red-400'
                "
            >
                {{ model.active ? 'En línea' : 'Fuera' }}
            </span>

        </div>

        <div class="mt-5 grid grid-cols-2 gap-4 text-sm">

            <div>

                <p class="text-gray-500">
                    Horas
                </p>

                <p class="font-semibold text-white">
                    {{ model.hours }}
                </p>

            </div>

            <div>

                <p class="text-gray-500">
                    Ranking
                </p>

                <p class="font-semibold text-white">
                    {{ model.ranking }}
                </p>

            </div>

        </div>

    </button>

</template>

<script setup>

import { computed } from 'vue'

const props = defineProps({

    model: {
        type: Object,
        required: true
    },

    selected: {
        type: Boolean,
        default: false
    }

})

const emit = defineEmits([
    'select'
])

const initials = computed(() => {

    return props.model.name
        ?.split(' ')
        .map(word => word.charAt(0))
        .join('')
        .substring(0, 2)
        .toUpperCase() || '?'

})

</script>