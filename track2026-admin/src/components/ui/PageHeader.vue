<template>

    <div class="flex items-center justify-between">

        <div>

            <h1 class="text-4xl font-bold text-white">
                {{ title }}
            </h1>

            <p class="text-gray-400 mt-2">
                {{ description }}
            </p>

        </div>

        <slot />

    </div>

</template>

<script setup>

defineProps({

    title: {
        type: String,
        required: true,
    },

    description: {
        type: String,
        default: '',
    },

})

</script>

<style scoped>
</style>