<template>

    <div class="bg-gray-900 border border-gray-800 rounded-3xl overflow-hidden">

        <table class="w-full">

            <thead class="bg-gray-950 border-b border-gray-800">

                <tr>

                    <th class="text-left px-6 py-4 text-gray-400">Modelo</th>
                    <th class="text-left px-6 py-4 text-gray-400">Horas</th>
                    <th class="text-left px-6 py-4 text-gray-400">Ranking</th>
                    <th class="text-left px-6 py-4 text-gray-400">Acciones</th>

                </tr>

            </thead>

            <tbody>

                <tr
                    v-for="model in models"
                    :key="model.id"
                    class="border-b border-gray-800 hover:bg-gray-800/40 transition"
                >

                    <!-- MODEL -->
                    <td class="px-6 py-4">

                        <div class="flex items-center gap-4">

                            <div
                                class="w-12 h-12 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 font-bold"
                            >
                                {{ model.name?.charAt(0) || '?' }}
                            </div>

                            <div>

                                <h2 class="text-white font-semibold">
                                    {{ model.name }}
                                </h2>

                                <p class="text-gray-500 text-sm">
                                    #{{ model.id }}
                                </p>

                            </div>

                        </div>

                    </td>

                    <!-- HOURS -->
                    <td class="px-6 py-4 text-white">
                        {{ model.hours_streamed || 0 }} hrs
                    </td>

                    <!-- RANK -->
                    <td class="px-6 py-4 text-blue-400 font-semibold">
                        {{ model.ranking_score || 0 }}
                    </td>

                    <!-- ACTIONS -->
                    <td class="px-6 py-4">

                        <ModelActions
                            :model="model"
                            @view="$emit('view', model)"
                            @edit="$emit('edit', model)"
                            @delete="$emit('delete', model)"
                        />

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</template>

<script setup>

import ModelActions from './ModelActions.vue'

defineProps({
    models: {
        type: Array,
        default: () => []
    }
})

defineEmits(['view', 'edit', 'delete'])

</script>