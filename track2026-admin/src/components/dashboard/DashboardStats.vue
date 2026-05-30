<template>

    <div
        class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6"
    >

        <!-- EARNINGS -->
        <div
            class="bg-gradient-to-br from-emerald-500/15 to-emerald-900/10 border border-emerald-500/20 rounded-3xl p-6"
        >

            <p class="text-gray-400 text-sm mb-2">
                Ganancias
            </p>

            <h2 class="text-4xl font-bold text-white">
                ${{ finance.totals.earnings }}
            </h2>

            <div class="mt-4 text-emerald-400 text-sm">
                Ingresos acumulados
            </div>

        </div>

        <!-- BONUSES -->
        <div
            class="bg-gradient-to-br from-blue-500/15 to-blue-900/10 border border-blue-500/20 rounded-3xl p-6"
        >

            <p class="text-gray-400 text-sm mb-2">
                Bonos
            </p>

            <h2 class="text-4xl font-bold text-white">
                ${{ finance.totals.bonuses }}
            </h2>

            <div class="mt-4 text-blue-400 text-sm">
                Incentivos activos
            </div>

        </div>

        <!-- PENALTIES -->
        <div
            class="bg-gradient-to-br from-red-500/15 to-red-900/10 border border-red-500/20 rounded-3xl p-6"
        >

            <p class="text-gray-400 text-sm mb-2">
                Multas
            </p>

            <h2 class="text-4xl font-bold text-white">
                ${{ finance.totals.penalties }}
            </h2>

            <div class="mt-4 text-red-400 text-sm">
                Penalizaciones
            </div>

        </div>

        <!-- DEDUCTIONS -->
        <div
            class="bg-gradient-to-br from-orange-500/15 to-orange-900/10 border border-orange-500/20 rounded-3xl p-6"
        >

            <p class="text-gray-400 text-sm mb-2">
                Descuentos
            </p>

            <h2 class="text-4xl font-bold text-white">
                ${{ finance.totals.deductions }}
            </h2>

            <div class="mt-4 text-orange-400 text-sm">
                Gastos descontados
            </div>

        </div>

    </div>

</template>

<script setup>

defineProps({
    dashboard: Object,
    finance: Object,
})

</script>
```
