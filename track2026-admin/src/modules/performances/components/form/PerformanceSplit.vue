<template>

    <div class="grid grid-cols-2 gap-4">

        <div>

            <label class="block mb-2 text-sm font-medium text-gray-300">

                Modelo (%)

            </label>

            <input
                v-model.number="form.model_percentage"
                type="number"
                min="0"
                max="100"
                class="input"
            >

        </div>

        <div>

            <label class="block mb-2 text-sm font-medium text-gray-300">

                Studio (%)

            </label>

            <input
                v-model.number="form.studio_percentage"
                type="number"
                min="0"
                max="100"
                class="input"
            >

        </div>

    </div>

</template>

<script setup>

defineProps({

    form: Object,

    field: Object

})

</script>

<style scoped>

.input {

    width: 100%;

    padding: 12px;

    border-radius: 12px;

    background: #0b0f19;

    border: 1px solid #1f2937;

    color: white;

}

.input:focus {

    border-color: #3b82f6;

    outline: none;

}

</style>