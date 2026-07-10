<template>

    <section
        class="rounded-2xl border border-gray-800 bg-gray-900 p-6"
    >

        <!-- HEADER -->

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

        <!-- CAMPOS -->

        <div
            :class="gridClass"
        >

            <PerformanceField

                v-for="field in section.fields"

                :key="field.model"

                :field="field"

                :form="form"

                :platforms="platforms"

            />

        </div>

    </section>

</template>

<script setup>

import {

    computed

} from 'vue'

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

    }

})

const gridClass = computed(() => {

    switch (props.section.columns) {

        case 1:

            return 'grid grid-cols-1 gap-4'

        case 3:

            return 'grid grid-cols-1 md:grid-cols-3 gap-4'

        default:

            return 'grid grid-cols-1 md:grid-cols-2 gap-4'

    }

})

</script>