<template>

    <div class="flex items-center justify-between p-4 text-white">

        <div class="text-gray-400">
            Página {{ meta.page }} de {{ meta.pages }}
        </div>

        <div class="flex gap-2">

            <button
                class="px-3 py-2 bg-gray-800 rounded-xl"
                @click="$emit('prev')"
                :disabled="meta.page <= 1"
            >
                Anterior
            </button>

            <button
                class="px-3 py-2 bg-gray-800 rounded-xl"
                @click="$emit('next')"
                :disabled="meta.page >= meta.pages"
            >
                Siguiente
            </button>

        </div>

    </div>

</template>

<script setup>

defineProps({
    meta: {
        type: Object,
        default: () => ({ page: 1, pages: 1 })
    }
})

defineEmits(['next', 'prev'])

</script>