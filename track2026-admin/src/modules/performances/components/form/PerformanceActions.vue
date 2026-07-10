<template>

    <div
        class="flex flex-col-reverse gap-4 border-t border-gray-800 pt-6 sm:flex-row sm:justify-end"
    >

        <button
            type="button"
            class="rounded-xl border border-gray-700 px-6 py-3 font-medium text-gray-300 transition hover:bg-gray-800"
            @click="$emit('cancel')"
        >
            Cancelar
        </button>

        <button
            type="button"
            :disabled="loading"
            class="rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white transition hover:bg-blue-500 disabled:cursor-not-allowed disabled:opacity-50"
            @click="$emit('save')"
        >

            <span
                v-if="loading"
            >
                Guardando...
            </span>

            <span
                v-else
            >
                Guardar Modelo
            </span>

        </button>

    </div>

</template>

<script setup>

defineProps({

    loading: {

        type: Boolean,

        default: false

    }

})

defineEmits([

    'save',

    'cancel'

])

</script>