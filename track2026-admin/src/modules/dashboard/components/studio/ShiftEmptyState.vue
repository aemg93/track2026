<template>

    <div
        v-if="!selectedPerformance"
        class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-gray-700 bg-gray-900/50 py-20 text-center"
    >

        <div
            class="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-gray-800"
        >
            <svg
                class="h-10 w-10 text-gray-500"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.964 0a9 9 0 10-11.964 0"
                />
            </svg>
        </div>

        <h3 class="text-xl font-semibold text-white">
            Ninguna modelo seleccionada
        </h3>

        <p class="mt-2 max-w-md text-gray-400">
            Selecciona una modelo desde el panel superior para comenzar
            a registrar ganancias, bonos, penalizaciones o descuentos.
        </p>

    </div>

    <div
        v-else
        class="rounded-2xl border border-gray-800 bg-gray-900 p-6"
    >

        <div class="flex items-center gap-4">

            <div
                class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-500/10 border border-blue-500/20 text-lg font-bold text-blue-400"
            >
                {{
                    (
                        (selectedPerformance.first_name?.charAt(0) || '') +
                        (selectedPerformance.last_name?.charAt(0) || '')
                    ).toUpperCase()
                }}
            </div>

            <div>

                <h2 class="text-xl font-bold text-white">
                    {{ selectedPerformance.name }}
                </h2>

                <p class="text-sm text-emerald-400">
                    ● En turno
                </p>

            </div>

        </div>

        <div class="mt-8 grid grid-cols-2 gap-4">

            <div class="rounded-xl bg-gray-800 p-4">
                <p class="text-sm text-gray-400">Ganancias</p>
                <p class="mt-2 text-2xl font-bold text-green-400">
                    $0
                </p>
            </div>

            <div class="rounded-xl bg-gray-800 p-4">
                <p class="text-sm text-gray-400">Bonos</p>
                <p class="mt-2 text-2xl font-bold text-cyan-400">
                    $0
                </p>
            </div>

            <div class="rounded-xl bg-gray-800 p-4">
                <p class="text-sm text-gray-400">Penalizaciones</p>
                <p class="mt-2 text-2xl font-bold text-red-400">
                    $0
                </p>
            </div>

            <div class="rounded-xl bg-gray-800 p-4">
                <p class="text-sm text-gray-400">Descuentos</p>
                <p class="mt-2 text-2xl font-bold text-orange-400">
                    $0
                </p>
            </div>

        </div>

        <div class="mt-8 grid gap-3">

            <button class="rounded-xl bg-green-600 py-3 font-semibold text-white hover:bg-green-500">
                Registrar Ganancia
            </button>

            <button class="rounded-xl bg-cyan-600 py-3 font-semibold text-white hover:bg-cyan-500">
                Registrar Bono
            </button>

            <button class="rounded-xl bg-red-600 py-3 font-semibold text-white hover:bg-red-500">
                Registrar Penalización
            </button>

            <button class="rounded-xl bg-orange-600 py-3 font-semibold text-white hover:bg-orange-500">
                Registrar Descuento
            </button>

            <button class="rounded-xl border border-gray-700 py-3 font-semibold text-gray-300 hover:bg-gray-800">
                Finalizar Turno
            </button>

        </div>

    </div>

</template>

<script setup>

defineProps({

    selectedPerformance: {
        type: Object,
        default: null
    }

})

</script>