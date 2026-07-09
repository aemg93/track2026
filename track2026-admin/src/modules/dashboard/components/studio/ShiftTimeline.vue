<template>

<div class="rounded-xl border border-gray-800 bg-gray-900 p-6">

    <h3 class="mb-6 text-lg font-semibold text-white">

        Actividad reciente

    </h3>

    <div
        v-if="timeline.length"
        class="space-y-4"
    >

        <div
            v-for="item in timeline"
            :key="item.id"
            class="flex items-center justify-between border-b border-gray-800 pb-4"
        >

            <div>

                <p class="font-medium text-white">

                    {{ item.title }}

                </p>

                <p class="text-sm text-gray-500">

                    {{ item.type }}

                    <span
                        v-if="item.time"
                    >
                        • {{ item.time }}
                    </span>

                </p>

            </div>

            <div
                class="text-right"
            >

                <p
                    class="font-semibold"
                    :class="item.amount >= 0
                        ? 'text-emerald-400'
                        : 'text-red-400'"
                >

                    {{ item.amount }}

                </p>

            </div>

        </div>

    </div>

    <div
        v-else
        class="py-10 text-center text-gray-500"
    >

        No existen movimientos registrados.

    </div>

</div>

</template>

<script setup>

defineProps({

    timeline: {
        type: Array,
        default: () => [],
    },

})

</script>