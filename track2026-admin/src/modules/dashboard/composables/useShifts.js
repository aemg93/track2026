import { computed, ref } from 'vue'
import shiftService from '../services/shiftService'

export function useShifts() {

    const activeShifts = ref([])
    const loading = ref(false)
    const error = ref(null)

    async function refreshActiveShifts() {

        activeShifts.value = await shiftService.active()

    }

    async function loadActiveShifts() {

        loading.value = true
        error.value = null

        try {

            await refreshActiveShifts()

        } catch (err) {

            error.value = err

            throw err

        } finally {

            loading.value = false

        }

    }

    async function startShift(performanceId) {

        error.value = null

        try {

            const shift = await shiftService.start(performanceId)

            await refreshActiveShifts()

            return shift

        } catch (err) {

            error.value = err

            throw err

        }

    }

    async function pauseShift(shiftId) {

        error.value = null

        try {

            const shift = await shiftService.pause(shiftId)

            await refreshActiveShifts()

            return shift

        } catch (err) {

            error.value = err

            throw err

        }

    }

    async function resumeShift(shiftId) {

        error.value = null

        try {

            const shift = await shiftService.resume(shiftId)

            await refreshActiveShifts()

            return shift

        } catch (err) {

            error.value = err

            throw err

        }

    }

    async function finishShift(shiftId) {

        error.value = null

        try {

            const shift = await shiftService.finish(shiftId)

            await refreshActiveShifts()

            return shift

        } catch (err) {

            error.value = err

            throw err

        }

    }

    function getShift(performanceId) {

        return activeShifts.value.find(

            shift => shift.performance_id === performanceId

        ) ?? null

    }

    const activeCount = computed(() => activeShifts.value.length)

    return {

        // state
        activeShifts,
        loading,
        error,

        // computed
        activeCount,

        // loaders
        loadActiveShifts,
        refreshActiveShifts,
        startShift,
        pauseShift,
        resumeShift,
        finishShift,
        getShift,

    }

}