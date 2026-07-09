<template>

    <div
        class="
            bg-gradient-to-br
            from-gray-900
            to-gray-950
            border
            border-gray-800
            rounded-3xl
            overflow-hidden
        "
    >

        <div
            class="
                flex
                items-center
                justify-between
                px-8
                py-6
                border-b
                border-gray-800
            "
        >

            <div>

                <p class="text-xs uppercase tracking-[.2em] text-gray-500">
                    Finanzas
                </p>

                <h2 class="mt-2 text-2xl font-bold text-white">
                    Historial de Ganancias
                </h2>

                <p class="mt-2 text-sm text-gray-400">
                    Registro histórico de ingresos generados por el modelo
                </p>

            </div>

            <div
                class="
                    hidden
                    lg:flex
                    items-center
                    gap-2
                    rounded-2xl
                    border
                    border-green-500/20
                    bg-green-500/10
                    px-4
                    py-2
                "
            >

                <span class="w-2 h-2 rounded-full bg-green-400"/>

                <span class="text-sm font-medium text-green-400">
                    Historial Financiero
                </span>

            </div>

        </div>

        <DataTable
            :columns="columns"
            :items="earnings"
        >

            <template #date="{ item }">

                <div class="flex flex-col">

                    <span class="font-medium text-white">
                        {{ formatDate(item.date) }}
                    </span>

                    <span class="mt-1 text-xs text-gray-500">
                        Movimiento registrado
                    </span>

                </div>

            </template>

            <template #amount="{ item }">

                <div class="flex justify-end">

                    <span
                        class="
                            inline-flex
                            items-center
                            gap-2
                            rounded-2xl
                            border
                            border-green-500/20
                            bg-green-500/10
                            px-4
                            py-2
                            font-semibold
                            text-green-400
                        "
                    >

                        <span class="w-2 h-2 rounded-full bg-green-400"/>

                        {{ formatAmount(item.amount) }}

                    </span>

                </div>

            </template>

        </DataTable>

        <div
            v-if="!earnings.length"
            class="border-t border-gray-800 p-12 text-center"
        >

            <div
                class="
                    mx-auto
                    flex
                    h-20
                    w-20
                    items-center
                    justify-center
                    rounded-3xl
                    bg-gray-800
                    text-3xl
                "
            >
                💰
            </div>

            <h3 class="mt-6 text-xl font-semibold text-white">
                Sin ganancias registradas
            </h3>

            <p class="mt-2 text-gray-400">
                No existen movimientos financieros para este modelo.
            </p>

        </div>

    </div>

</template>

<script setup>

import { computed } from 'vue'
import DataTable from '@/components/ui/DataTable.vue'

const props = defineProps({
    earnings: {
        type: Array,
        default: () => []
    }
})

const columns = [
    { key: 'date', label: 'Fecha' },
    { key: 'amount', label: 'Monto' }
]

const earnings = computed(() =>
    props.earnings.map(earning => ({
        id: earning.id,
        date: earning.created_at,
        amount: Number(earning.gross_usd ?? 0)
    }))
)

const formatAmount = value =>
    new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value)

const formatDate = value =>
    value
        ? new Intl.DateTimeFormat('es-CO', {
              day: '2-digit',
              month: 'short',
              year: 'numeric'
          }).format(new Date(value))
        : '-'

</script>