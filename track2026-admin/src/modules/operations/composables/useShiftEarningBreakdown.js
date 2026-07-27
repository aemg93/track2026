import {
  computed,
} from 'vue'

export function useShiftEarningBreakdown(
  shift,
) {

  const financialSummary = computed(
    () =>
      shift.value?.activity?.financial_summary ?? {}
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
        platform => {

          const name =
            platform.platform_name ??
            'Sin plataforma'

          if (!grouped[name]) {

            grouped[name] = {

              name,

              usd: 0,

            }

          }

          grouped[name].usd += Number(
            platform.usd ?? 0
          )

        }
      )

      return Object.values(grouped)
        .filter(
          platform =>
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
      platformEarnings.value.reduce(
        (
          total,
          platform,
        ) =>
          total + platform.usd,
        0
      )
  )

  return {

    platformEarnings,

    totalUsd,

  }

}