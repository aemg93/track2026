<template>
    <div
        v-if="props.shifts.length"
        class="grid gap-4 md:grid-cols-2 xl:grid-cols-4"
    >
        <ShiftPerformanceItem
            v-for="shift in props.shifts"
            :key="shift.id"
            :shift-id="shift.id"
            :performance="shift.performance"
            :status="shift.status"
            :selected="props.selectedShift?.id === shift.id"
            @select="selectShift"
        />
    </div>

    <div
        v-else
        class="rounded-2xl border border-dashed border-gray-700 bg-gray-900/40 py-12 text-center"
    >
        <p class="text-gray-400">
            No hay turnos activos.
        </p>
    </div>
</template>

<script setup>
import ShiftPerformanceItem from './ShiftPerformanceItem.vue'

const props = defineProps({
    shifts: {
        type: Array,
        default: () => [],
    },

    selectedShift: {
        type: Object,
        default: null,
    },
})

const emit = defineEmits(['select'])

function selectShift(shift) {
    emit('select', shift)
}
</script>