<template>

    <div class="space-y-3">

        <label class="block text-sm font-medium text-gray-300">

            {{ field.label }}

        </label>

        <select
            v-model="form[field.model]"
            multiple
            class="input min-h-36"
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

    </div>

</template>

<script setup>

defineProps({

    field: Object,

    form: Object,

    platforms: {

        type: Array,

        default: () => []

    }

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