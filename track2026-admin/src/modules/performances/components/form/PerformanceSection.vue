<template>
    <section
        class="rounded-2xl border border-gray-800 bg-gray-900 p-6"
    >

        <header
            v-if="section.title || section.description"
            class="mb-6"
        >

            <h2
                v-if="section.title"
                class="text-lg font-semibold text-white"
            >
                {{ section.title }}
            </h2>

            <p
                v-if="section.description"
                class="mt-1 text-sm text-gray-400"
            >
                {{ section.description }}
            </p>

        </header>

        <div :class="gridClass">

            <PerformanceField
                v-for="field in section.fields"
                :key="field.model"
                :field="field"
                :form="form"
                :platforms="platforms"
                :errors="errors"
            />

        </div>

    </section>
</template>

<script setup>

import { computed } from 'vue'

import PerformanceField from './PerformanceField.vue'

const props = defineProps({

    section: {
        type: Object,
        required: true
    },

    form: {
        type: Object,
        required: true
    },

    platforms: {
        type: Array,
        default: () => []
    },

    errors: {
        type: Object,
        default: () => ({})
    }

})

const layouts = Object.freeze({

    1: 'grid grid-cols-1 gap-4',

    2: 'grid grid-cols-1 md:grid-cols-2 gap-4',

    3: 'grid grid-cols-1 md:grid-cols-3 gap-4'

})

const gridClass = computed(() =>

    layouts[
        props.section.columns ?? 2
    ] ?? layouts[2]

)

</script>