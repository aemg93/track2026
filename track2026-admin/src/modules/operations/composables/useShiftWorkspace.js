import {
  computed,
} from 'vue'


export function useShiftWorkspace(
  selectedShift,
  emit,
) {


  const selectedPerformance = computed(
    () =>
      selectedShift.value?.performance ?? null
  )



  const timeline = computed(
    () =>
      selectedShift.value
        ?.activity
        ?.timeline ?? []
  )



  const activityMetrics = computed(
    () =>
      selectedShift.value
        ?.activity
        ?.metrics ?? {}
  )



  const financialSummary = computed(
    () =>
      selectedShift.value
        ?.activity
        ?.financial_summary ?? {}
  )



  const totalUsd = computed(
    () =>
      Number(
        financialSummary.value.total_usd
        ??
        activityMetrics.value.earnings
        ??
        0
      )
  )



  const totalTokens = computed(
    () =>
      Number(
        financialSummary.value.total_tokens
        ??
        activityMetrics.value.tokens
        ??
        0
      )
  )



  const totalMovements = computed(
    () =>
      timeline.value.length
  )



  function formatUsd(value) {

    return Number(value)
      .toLocaleString(
        'es-CO',
        {
          style: 'currency',
          currency: 'USD',
          minimumFractionDigits: 2,
        }
      )

  }



  function formatTokens(value) {

    return Number(value)
      .toLocaleString(
        'es-CO',
        {
          maximumFractionDigits: 0,
        }
      )

  }



  /**
   * Abrir detalle ganancias USD
   */
  function handleEarnings() {

    if (!selectedShift.value) {
      return
    }


    console.log(
      'EARNINGS EVENT',
      selectedShift.value
    )


    emit(
      'earnings',
      selectedShift.value
    )

  }



  /**
   * Abrir detalle tokens
   */
  function handleTokens() {

    if (!selectedShift.value) {
      return
    }


    console.log(
      'TOKENS EVENT',
      selectedShift.value
    )


    emit(
      'tokens',
      selectedShift.value
    )

  }



  function handleEarning() {

    if (!selectedPerformance.value) {
      return
    }


    emit(
      'earning',
      selectedPerformance.value
    )

  }



  function handleBonus() {

    if (!selectedPerformance.value) {
      return
    }


    emit(
      'bonus',
      selectedPerformance.value
    )

  }



  function handlePenalty() {

    if (!selectedPerformance.value) {
      return
    }


    emit(
      'penalty',
      selectedPerformance.value
    )

  }



  function handleDeduction() {

    if (!selectedPerformance.value) {
      return
    }


    emit(
      'deduction',
      selectedPerformance.value
    )

  }



  function handlePause() {

    if (!selectedShift.value) {
      return
    }


    emit(
      'pause',
      selectedShift.value
    )

  }



  function handleResume() {

    if (!selectedShift.value) {
      return
    }


    emit(
      'resume',
      selectedShift.value
    )

  }



  function handleFinish() {

    if (!selectedShift.value) {
      return
    }


    emit(
      'finish',
      selectedShift.value
    )

  }



  return {

    selectedPerformance,

    timeline,

    activityMetrics,

    financialSummary,

    totalUsd,

    totalTokens,

    totalMovements,


    formatUsd,

    formatTokens,


    handleEarnings,

    handleTokens,


    handleEarning,

    handleBonus,

    handlePenalty,

    handleDeduction,


    handlePause,

    handleResume,

    handleFinish,

  }

}