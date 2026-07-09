<template>

    <div class="space-y-6">

        <div class="grid gap-4 md:grid-cols-2">

            <button

                @click="action('earning')"

                :disabled="!model"

                class="rounded-xl bg-emerald-600 px-4 py-3 font-medium text-white transition hover:bg-emerald-500 disabled:opacity-50"

            >

                Registrar Ganancia

            </button>

            <button

                @click="action('bonus')"

                :disabled="!model"

                class="rounded-xl bg-blue-600 px-4 py-3 font-medium text-white transition hover:bg-blue-500 disabled:opacity-50"

            >

                Registrar Bono

            </button>

            <button

                @click="action('penalty')"

                :disabled="!model"

                class="rounded-xl bg-amber-600 px-4 py-3 font-medium text-white transition hover:bg-amber-500 disabled:opacity-50"

            >

                Registrar Penalización

            </button>



            <button

                @click="action('deduction')"

                :disabled="!model"

                class="rounded-xl bg-rose-600 px-4 py-3 font-medium text-white transition hover:bg-rose-500 disabled:opacity-50"

            >

                Registrar Descuento

            </button>


        </div>

        <button

            @click="action('finish')"

            :disabled="!model"

            class="w-full rounded-xl border border-red-600 py-3 font-semibold text-red-400 transition hover:bg-red-600 hover:text-white disabled:opacity-50"

        >

            Finalizar Turno

        </button>


    </div>

</template>

<script setup>

const props = defineProps({

    model: {

        type:Object,

        default:null,

    },

})

const emit = defineEmits([

    'earning',

    'bonus',

    'penalty',

    'deduction',

    'finish',

])

function action(type){


    if(!props.model){

        return

    }

    emit(

        type,

        props.model

    )


}

</script>