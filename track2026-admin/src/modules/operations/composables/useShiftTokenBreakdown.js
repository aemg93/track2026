import {
    computed,
    unref,
} from 'vue'

export function useShiftTokenBreakdown(shift) {
    const currentShift = computed(
        () => unref(shift) ?? null
    )

    const activity = computed(
        () => currentShift.value?.activity ?? {}
    )

    const financialSummary = computed(() => {
        const official =
            activity.value.financial_summary

        if (
            official &&
            typeof official === 'object'
        ) {
            return official
        }

        return activity.value.summary ?? {}
    })

    const tokenPlatforms = computed(() => {
        const platforms =
            financialSummary.value.platforms

        if (!Array.isArray(platforms)) {
            return []
        }

        return platforms
            .map((platform) => ({
                id:
                    platform.platform_id ??
                    platform.id ??
                    null,

                name:
                    platform.platform_name ??
                    platform.platform ??
                    'Sin plataforma',

                slug:
                    platform.slug ??
                    null,

                type:
                    platform.type ??
                    null,

                currencies:
                    Array.isArray(platform.currencies)
                        ? platform.currencies
                        : [],

                originalAmount:
                    Number(
                        platform.original_amount ?? 0
                    ),

                tokens:
                    Number(
                        platform.real_tokens ?? 0
                    ),

                usd:
                    Number(
                        platform.gross_usd ?? 0
                    ),
            }))
            .filter(
                (platform) =>
                    platform.tokens > 0 ||
                    platform.usd > 0
            )
            .sort(
                (a, b) =>
                    b.tokens - a.tokens
            )
    })

    const totalTokens = computed(() => {
        const backendTotal =
            financialSummary.value.total_tokens

        if (
            backendTotal !== null &&
            backendTotal !== undefined
        ) {
            const value = Number(backendTotal)

            if (Number.isFinite(value)) {
                return value
            }
        }

        return tokenPlatforms.value.reduce(
            (total, platform) =>
                total + platform.tokens,
            0
        )
    })

    const totalUsd = computed(() => {
        const backendGross =
            financialSummary.value.gross_usd

        if (
            backendGross !== null &&
            backendGross !== undefined
        ) {
            const value = Number(backendGross)

            if (Number.isFinite(value)) {
                return value
            }
        }

        const backendTotal =
            financialSummary.value.total_usd

        if (
            backendTotal !== null &&
            backendTotal !== undefined
        ) {
            const value = Number(backendTotal)

            if (Number.isFinite(value)) {
                return value
            }
        }

        return tokenPlatforms.value.reduce(
            (total, platform) =>
                total + platform.usd,
            0
        )
    })

    const netUsd = computed(() =>
        Number(
            financialSummary.value.net_usd ?? 0
        )
    )

    const bonusUsd = computed(() =>
        Number(
            financialSummary.value.bonus_usd ?? 0
        )
    )

    const penaltyUsd = computed(() =>
        Number(
            financialSummary.value.penalty_usd ?? 0
        )
    )

    const deductionUsd = computed(() =>
        Number(
            financialSummary.value.deduction_usd ?? 0
        )
    )

    const modelShareUsd = computed(() =>
        Number(
            financialSummary.value.model_share_usd ?? 0
        )
    )

    const studioShareUsd = computed(() =>
        Number(
            financialSummary.value.studio_share_usd ?? 0
        )
    )

    return {
        currentShift,
        activity,
        financialSummary,
        tokenPlatforms,
        totalTokens,
        totalUsd,
        netUsd,
        bonusUsd,
        penaltyUsd,
        deductionUsd,
        modelShareUsd,
        studioShareUsd,
    }
}