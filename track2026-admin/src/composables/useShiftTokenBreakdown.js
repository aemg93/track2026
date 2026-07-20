import {
  computed,
  unref,
} from 'vue'


export function useShiftTokenBreakdown(
  shift,
) {


  const financialSummary = computed(
    () =>
      unref(shift)?.activity?.financial_summary ?? {}
  )



  const tokenPlatforms = computed(
    () => {

      const platforms =
        financialSummary.value.platforms ?? []


      if (!Array.isArray(platforms)) {
        return []
      }


      const grouped = {}


      platforms
        .filter(
          platform =>
            platform.type === 'token'
            &&
            Array.isArray(platform.currencies)
            &&
            platform.currencies.includes('TOKENS')
        )
        .forEach(
          platform => {

            const name =
              platform.platform_name


            if (!grouped[name]) {

              grouped[name] = {

                name,

                tokens: 0,

                usd: 0,

              }

            }


            grouped[name].tokens +=
              Number(
                platform.original_amount ?? 0
              )


            grouped[name].usd +=
              Number(
                platform.usd ?? 0
              )

          }
        )


      return Object.values(grouped)

    }
  )



  const totalTokens = computed(
    () =>
      tokenPlatforms.value.reduce(
        (
          total,
          platform,
        ) =>
          total + platform.tokens,
        0
      )
  )



  const totalUsd = computed(
    () =>
      tokenPlatforms.value.reduce(
        (
          total,
          platform,
        ) =>
          total + platform.usd,
        0
      )
  )



  return {

    tokenPlatforms,

    totalTokens,

    totalUsd,

  }

}