import {
  computed,
  unref,
} from 'vue'

export function useShiftEarningBreakdown(
  shift,
) {
  const currentShift = computed(
    () =>
      unref(shift) ?? null
  )

  const activity = computed(
    () =>
      currentShift.value?.activity ?? {}
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

  const platformEarnings = computed(
    () => {
      const platforms =
        financialSummary.value.platforms ?? []

      if (!Array.isArray(platforms)) {
        return []
      }

      const grouped = {}

      platforms.forEach(
        (platform) => {
          const name =
            platform.platform_name ??
            platform.platform ??
            'Sin plataforma'

          if (!grouped[name]) {
            grouped[name] = {
              name,
              usd: 0,
            }
          }

          grouped[name].usd += Number(
            platform.gross_usd ?? 0
          )
        }
      )

      return Object.values(grouped)
        .filter(
          (platform) =>
            platform.usd > 0
        )
        .sort(
          (a, b) =>
            b.usd - a.usd
        )
    }
  )

  const totalUsd = computed(
    () =>
      Number(
        financialSummary.value.gross_usd ?? 0
      )
  )

  return {
    currentShift,
    activity,
    financialSummary,
    platformEarnings,
    totalUsd,
  }
}