<template>

    <div class="bg-gray-900 border border-gray-800 rounded-3xl overflow-hidden">

        <table class="w-full">

            <thead class="bg-gray-950 border-b border-gray-800">

                <tr>

                    <th class="text-left px-6 py-4 text-gray-400">Modelo</th>
                    <th class="text-left px-6 py-4 text-gray-400">Correo</th>
                    <th class="text-left px-6 py-4 text-gray-400">Estado</th>
                    <th class="text-left px-6 py-4 text-gray-400">Acciones</th>

                </tr>

            </thead>

            <tbody>

                <tr
                    v-for="model in models"
                    :key="model.id"
                    class="border-b border-gray-800 hover:bg-gray-800/40 transition"
                >

                    <td class="px-6 py-4">

                        <div class="flex items-center gap-4">

                            <div
                                class="w-11 h-11 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 font-bold"
                            >
                                {{
                                    (
                                        (model.first_name?.charAt(0) || '') +
                                        (model.last_name?.charAt(0) || '')
                                    ).toUpperCase() || model.nickname?.charAt(0)?.toUpperCase() || '?'
                                }}
                            </div>

                            <div>

                                <h2 class="text-white font-semibold">
                                    {{
                                        model.first_name && model.last_name
                                            ? `${model.first_name} ${model.last_name}`
                                            : model.nickname || 'Sin nombre'
                                    }}
                                </h2>

                            </div>

                        </div>

                    </td>

                    <td class="px-6 py-4 text-gray-300">
                        {{ model.email || 'Sin correo' }}
                    </td>

                    <td class="px-6 py-4">

                        <span
                            class="px-3 py-1 rounded-full text-xs font-semibold"
                            :class="model.active
                                ? 'bg-green-500/10 text-green-400 border border-green-500/20'
                                : 'bg-red-500/10 text-red-400 border border-red-500/20'"
                        >
                            {{ model.active ? 'Activo' : 'Inactivo' }}
                        </span>

                    </td>

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