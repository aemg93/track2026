<template>
    <div class="rounded-xl border border-gray-800 bg-gray-900 p-6">
        <h3 class="mb-6 text-lg font-semibold text-white">
            Resumen del turno
        </h3>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <p class="text-sm text-gray-400">Ganancias</p>
                <h2 class="mt-1 text-2xl font-bold text-emerald-400">
                    ${{ financial.earnings.toFixed(2) }}
                </h2>
            </div>

            <div>
                <p class="text-sm text-gray-400">Bonos</p>
                <h2 class="mt-1 text-2xl font-bold text-blue-400">
                    ${{ financial.bonus.toFixed(2) }}
                </h2>
            </div>

            <div>
                <p class="text-sm text-gray-400">Penalizaciones</p>
                <h2 class="mt-1 text-2xl font-bold text-yellow-400">
                    ${{ financial.penalty.toFixed(2) }}
                </h2>
            </div>

            <div>
                <p class="text-sm text-gray-400">Descuentos</p>
                <h2 class="mt-1 text-2xl font-bold text-red-400">
                    ${{ financial.deduction.toFixed(2) }}
                </h2>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    performance: {
        type: Object,
        default: null
    }
})

const financial = computed(() => ({
    earnings: Number(props.performance?.financial?.gross_usd ?? 0),
    bonus: Number(props.performance?.financial?.bonus_usd ?? 0),
    penalty: Number(props.performance?.financial?.penalty_usd ?? 0),
    deduction: Number(props.performance?.financial?.deduction_usd ?? 0),
}))
</script>
