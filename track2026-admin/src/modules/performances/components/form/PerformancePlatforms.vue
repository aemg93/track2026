<template>
    <div class="space-y-3">

        <label class="label">
            {{ field.label ?? 'Plataformas' }}
        </label>


        <select
            v-model="selectedPlatforms"
            multiple
            class="input min-h-36"
            :disabled="!platforms.length"
        >

            <option
                v-for="platform in platforms"
                :key="platform.id"
                :value="platform.id"
            >
                {{ platform.name }}
            </option>

        </select>


        <p class="text-xs text-gray-500">
            Mantén presionada la tecla Ctrl (o Cmd) para seleccionar varias plataformas.
        </p>


        <p
            v-if="!platforms.length"
            class="text-sm text-yellow-400"
        >
            No hay plataformas disponibles.
        </p>


        <p
            v-else-if="!selectedPlatforms.length"
            class="text-sm text-yellow-400"
        >
            Debes seleccionar al menos una plataforma.
        </p>

    </div>
</template>


<script setup>

import { computed } from 'vue'


const props = defineProps({

    field: {
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


const modelKey = computed(() =>
    props.field?.model ?? 'platforms'
)


const selectedPlatforms = computed({

    get() {

        return props.form[modelKey.value] ?? []

    },


    set(value) {

        props.form[modelKey.value] = [
            ...new Set(
                value.map(Number)
            )
        ]

    }

})

</script>


<style scoped>

.label {

    display: block;
    margin-bottom: .5rem;
    font-size: .875rem;
    font-weight: 500;
    color: #d1d5db;

}


.input {

    width: 100%;
    padding: 12px;
    border-radius: 12px;
    background: #0b0f19;
    border: 1px solid #1f2937;
    color: white;

}


.input:focus {

    outline: none;
    border-color: #3b82f6;

}


.input:disabled {

    opacity: .5;
    cursor: not-allowed;

}

</style>