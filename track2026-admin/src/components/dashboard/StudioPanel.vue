<template>

    <div
        class="grid grid-cols-1 xl:grid-cols-3 gap-6"
    >

        <!-- STUDIO INFO -->
        <div
            class="xl:col-span-2 bg-gray-900 border border-gray-800 rounded-3xl p-6"
        >

            <div class="flex items-center justify-between mb-6">

                <div>

                    <h2 class="text-2xl font-bold text-white">
                        Studio Updates
                    </h2>

                    <p class="text-gray-400 text-sm mt-1">
                        Información general del estudio
                    </p>

                </div>

            </div>

            <div class="space-y-4">

                <div
                    class="bg-blue-500/10 border border-blue-500/20 rounded-2xl p-5"
                >

                    <h3 class="text-white font-semibold">
                        Weekly payouts updated
                    </h3>

                    <p class="text-gray-400 text-sm mt-2">
                        Los pagos semanales serán procesados todos los viernes.
                    </p>

                </div>

                <div
                    class="bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-5"
                >

                    <h3 class="text-white font-semibold">
                        Monthly growth
                    </h3>

                    <p class="text-gray-400 text-sm mt-2">
                        El estudio creció un 18% este mes.
                    </p>

                </div>

            </div>

        </div>

        <!-- QUICK METRICS -->
        <div
            class="bg-gray-900 border border-gray-800 rounded-3xl p-6"
        >

            <h2 class="text-2xl font-bold text-white mb-6">
                Quick Metrics
            </h2>

            <div class="space-y-5">

                <div
                    class="bg-gray-800 rounded-2xl p-5"
                >

                    <p class="text-gray-400 text-sm">
                        Active Models
                    </p>

                    <h3 class="text-3xl font-bold text-blue-400 mt-2">
                        {{ dashboard.total_models }}
                    </h3>

                </div>

                <div
                    class="bg-gray-800 rounded-2xl p-5"
                >

                    <p class="text-gray-400 text-sm">
                        Installments
                    </p>

                    <h3 class="text-3xl font-bold text-purple-400 mt-2">
                        {{ finance.installments.active }}
                    </h3>

                </div>

                <div
                    class="bg-gray-800 rounded-2xl p-5"
                >

                    <p class="text-gray-400 text-sm">
                        Pending Amount
                    </p>

                    <h3 class="text-3xl font-bold text-orange-400 mt-2">
                        ${{ finance.installments.pending_amount }}
                    </h3>

                </div>

            </div>

        </div>

    </div>

</template>

<script setup>

defineProps({

    dashboard: {
        type: Object,
        required: true,
    },

    finance: {
        type: Object,
        required: true,
    },
})

</script>