<template>

    <div class="space-y-8">

        <div>

            <h1 class="text-4xl font-bold text-white">
                Configuración
            </h1>

            <p class="text-gray-400 mt-2">
                Ajustes generales del sistema
            </p>

        </div>

        <div
            class="bg-gray-900 border border-gray-800 rounded-3xl p-8"
        >

            <div class="space-y-6">

                <div>

                    <label class="block text-sm text-gray-400 mb-2">
                        Nombre del estudio
                    </label>

                    <input
                        type="text"
                        class="w-full bg-gray-800 border border-gray-700 rounded-2xl px-5 py-4 text-white outline-none focus:border-blue-500"
                        placeholder="Studio Name"
                    >

                </div>

                <div>

                    <label class="block text-sm text-gray-400 mb-2">
                        Moneda base
                    </label>

                    <select
                        class="w-full bg-gray-800 border border-gray-700 rounded-2xl px-5 py-4 text-white outline-none focus:border-blue-500"
                    >
                        <option>
                            USD
                        </option>

                        <option>
                            COP
                        </option>

                        <option>
                            EUR
                        </option>

                    </select>

                </div>

                <button
                    class="px-6 py-4 rounded-2xl bg-blue-500 hover:bg-blue-600 transition text-white font-semibold"
                >
                    Guardar cambios
                </button>

            </div>

        </div>

    </div>

</template>

<script setup>

</script>