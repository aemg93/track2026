<template>
    <div
        class="flex flex-col-reverse gap-4 border-t border-gray-800 pt-6 sm:flex-row sm:justify-end"
    >

        <button
            type="button"
            class="rounded-xl border border-gray-700 px-6 py-3 font-medium text-gray-300 transition hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="loading"
            aria-label="Cancelar"
            @click="emit('cancel')"
        >
            Cancelar
        </button>


        <button
            type="button"
            class="rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white transition hover:bg-blue-500 disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="loading"
            aria-label="Guardar modelo"
            @click="emit('save')"
        >

            <span v-if="loading">
                {{ loadingText }}
            </span>

            <span v-else>
                {{ buttonText }}
            </span>

        </button>

    </div>
</template>


<script setup>

const props = defineProps({

    loading: {
        type: Boolean,
        default: false
    },

    buttonText: {
        type: String,
        default: 'Guardar Modelo'
    },

    loadingText: {
        type: String,
        default: 'Guardando...'
    }

})


const emit = defineEmits([

    'save',
    'cancel'

])

</script>