import {
  computed,
  ref,
  watch,
} from 'vue'


export function useStudioPanel(
  props,
  emit,
) {


  const selectedShift = ref(null)

  const tokenShift = ref(null)

  const showTokenBreakdown = ref(false)

  const earningShift = ref(null)

  const showEarningBreakdown = ref(false)

  const performances = computed(
    () =>
      props.dashboard?.performances ??
      props.dashboard?.models ??
      []
  )

  const activeShifts = computed(
    () =>
      props.operations?.active_shifts ?? []
  )

  function startShift(performance) {

    emit(
      'start-shift',
      performance
    )

  }

  function selectShift(shift) {

    if (!shift) {
      return
    }

    if (
      selectedShift.value?.id === shift.id
    ) {

      selectedShift.value = null

      return

    }

    selectedShift.value = shift

  }

  function registerEarning(performance) {

    emit(
      'earning',
      performance
    )

  }

  function registerBonus(performance) {

    emit(
      'bonus',
      performance
    )

  }

  function registerPenalty(performance) {

    emit(
      'penalty',
      performance
    )

  }

  function registerDeduction(performance) {

    emit(
      'deduction',
      performance
    )

  }

  function pauseCurrentShift(shift) {

    emit(
      'pause',
      shift
    )

  }

  function resumeCurrentShift(shift) {

    emit(
      'resume',
      shift
    )

  }

  function finishCurrentShift(shift) {

    if (!shift?.id) {
      return
    }

    emit(
      'finish',
      shift
    )

  }

  function openTokens(shift) {

    if (!shift) {
      return
    }


    tokenShift.value = shift

    showTokenBreakdown.value = true

  }

  function closeTokenBreakdown() {

    tokenShift.value = null

    showTokenBreakdown.value = false

  }

  function openEarnings(shift) {

    if (!shift) {
      return
    }


    earningShift.value = shift

    showEarningBreakdown.value = true

  }

  function closeEarningBreakdown() {

    earningShift.value = null

    showEarningBreakdown.value = false

  }

  watch(

    activeShifts,

    shifts => {

      if (
        !selectedShift.value ||
        !Array.isArray(shifts)
      ) {
        return
      }


      const current =
        shifts.find(
          item =>
            item.id === selectedShift.value.id
        )


      selectedShift.value =
        current ?? null

    },

    {
      immediate: true,
    }

  )



  return {

    selectedShift,


    tokenShift,

    showTokenBreakdown,


    earningShift,

    showEarningBreakdown,


    performances,

    activeShifts,


    startShift,

    selectShift,


    registerEarning,

    registerBonus,

    registerPenalty,

    registerDeduction,


    pauseCurrentShift,

    resumeCurrentShift,

    finishCurrentShift,


    openTokens,

    closeTokenBreakdown,


    openEarnings,

    closeEarningBreakdown,

  }

}