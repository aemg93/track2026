import {
  computed,
  unref,
} from 'vue'

export function useShiftWorkspace(
  selectedShift,
  emit,
) {
  const currentShift = computed(
    () =>
      unref(selectedShift) ?? null
  )

  const selectedPerformance = computed(
    () =>
      currentShift.value?.performance ?? null
  )

  const activity = computed(
    () =>
      currentShift.value?.activity ?? {}
  )

  const timeline = computed(
    () =>
      Array.isArray(
        activity.value.timeline
      )
        ? activity.value.timeline
        : []
  )

  const activityMetrics = computed(
    () =>
      activity.value.metrics ?? {}
  )

  const financialSummary = computed(
    () => {

      const financial =
        activity.value.financial_summary

      if (
        financial &&
        typeof financial === 'object'
      ) {
        return financial
      }

      return {}
    }
  )

  const totalUsd = computed(
    () =>
      Number(
        financialSummary.value.gross_usd ?? 0
      )
  )

  const totalTokens = computed(
    () =>
      Number(
        financialSummary.value.total_tokens ?? 0
      )
  )

  const totalMovements = computed(
    () =>
      timeline.value.length
  )

  function formatUsd(value) {
    return Number(
      value ?? 0
    ).toLocaleString(
      'es-CO',
      {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      }
    )
  }

  function formatTokens(value) {
    return Number(
      value ?? 0
    ).toLocaleString(
      'es-CO',
      {
        maximumFractionDigits: 0,
      }
    )
  }

  function handleEarnings() {
    if (!currentShift.value) {
      return
    }

    emit(
      'earnings',
      currentShift.value
    )
  }

  function handleTokens() {
    if (!currentShift.value) {
      return
    }

    emit(
      'tokens',
      currentShift.value
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
    if (!currentShift.value) {
      return
    }

    emit(
      'pause',
      currentShift.value
    )
  }

  function handleResume() {
    if (!currentShift.value) {
      return
    }

    emit(
      'resume',
      currentShift.value
    )
  }

  function handleFinish() {
    if (!currentShift.value) {
      return
    }

    emit(
      'finish',
      currentShift.value
    )
  }

  return {
    currentShift,
    selectedPerformance,
    activity,
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
