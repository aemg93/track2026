<template>

    <span :class="badgeClass">

        <slot />

    </span>

</template>

<script setup>

import { computed } from 'vue'

const props = defineProps({

    variant: {
        type: String,
        default: 'blue',
    },

})

const variants = {

    blue: 'bg-blue-500/20 text-blue-400',

    green: 'bg-green-500/20 text-green-400',

    red: 'bg-red-500/20 text-red-400',

    yellow: 'bg-yellow-500/20 text-yellow-400',

    purple: 'bg-purple-500/20 text-purple-400',

}

const badgeClass = computed(() => {

    return `
        ${variants[props.variant]}
        px-3
        py-1
        rounded-full
        text-sm
        font-semibold
    `
})

</script>

<style scoped>
</style>