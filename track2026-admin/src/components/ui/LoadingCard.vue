<template>

    <div
        class="bg-gray-900 border border-gray-800 rounded-2xl p-10 text-center text-gray-400"
    >

        {{ text }}

    </div>

</template>

<script setup>

defineProps({

    text: {
        type: String,
        default: 'Cargando...',
    },

})

</script>

<style scoped>
</style>