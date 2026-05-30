<template>

    <div class="flex items-center gap-2">

        <button
            class="px-3 py-2 rounded-xl bg-gray-800 hover:bg-gray-700 text-white text-sm"
            @click="$emit('view')"
        >
            Ver
        </button>

        <button
            class="px-3 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white text-sm"
            @click="$emit('edit')"
        >
            Editar
        </button>

        <button
            class="px-3 py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm"
            @click="$emit('delete')"
        >
            Borrar
        </button>

    </div>

</template>

<script setup>

defineEmits(['view', 'edit', 'delete'])

</script>