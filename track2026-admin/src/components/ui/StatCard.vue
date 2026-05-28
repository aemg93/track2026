<template>

    <div
        class="bg-gray-900 border border-gray-800 rounded-2xl p-6"
    >

        <p class="text-gray-400 text-sm">
            {{ title }}
        </p>

        <h2 class="text-4xl font-bold text-white mt-3">
            {{ value }}
        </h2>

    </div>

</template>

<script setup>

defineProps({

    title: {
        type: String,
        required: true,
    },

    value: {
        type: [String, Number],
        required: true,
    },

})

</script>

<style scoped>
</style>