<template>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

        <div>

            <label class="label">
                Modelo (%)
            </label>

            <input
                v-model.number="modelPercentage"
                type="number"
                min="50"
                max="100"
                class="input"
            >

        </div>


        <div>

            <label class="label">
                Studio (%)
            </label>

            <input
                v-model.number="studioPercentage"
                type="number"
                min="0"
                max="50"
                class="input"
            >

        </div>


        <div
            class="col-span-full text-sm"
            :class="isValid
                ? 'text-green-400'
                : 'text-red-400'"
        >

            {{ validationMessage }}

        </div>

    </div>
</template>


<script setup>

import { computed } from 'vue'


const props = defineProps({

    form: {
        type: Object,
        required: true
    }

})


const ensureSplit = () => {

    if (!props.form.split) {

        props.form.split = {
            model_percentage: 60,
            studio_percentage: 40
        }

    }

}


const normalizePercentage = (
    value,
    min,
    max
) => {

    let number = Number(value)

    if (Number.isNaN(number)) {

        number = min

    }

    return Math.min(
        Math.max(number, min),
        max
    )

}


const modelPercentage = computed({

    get() {

        return props.form.split?.model_percentage ?? 60

    },


    set(value) {

        ensureSplit()


        const model =
            normalizePercentage(
                value,
                50,
                100
            )


        props.form.split.model_percentage = model

        props.form.split.studio_percentage =
            100 - model

    }

})


const studioPercentage = computed({

    get() {

        return props.form.split?.studio_percentage ?? 40

    },


    set(value) {

        ensureSplit()


        const studio =
            normalizePercentage(
                value,
                0,
                50
            )


        props.form.split.studio_percentage = studio

        props.form.split.model_percentage =
            100 - studio

    }

})


const isValid = computed(() => {

    const model =
        Number(modelPercentage.value)

    const studio =
        Number(studioPercentage.value)


    return (
        model >= 50 &&
        studio <= 50 &&
        Math.round(model + studio) === 100
    )

})


const validationMessage = computed(() => {

    if (isValid.value) {

        return 'Distribución válida'

    }


    return (
        'La modelo debe tener mínimo 50% ' +
        'y la distribución debe sumar 100%'
    )

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

</style>