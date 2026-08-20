import {
  computed,
} from 'vue'

export function useOperationalShiftHistory(props) {
  const performances = computed(() => {
    return Array.isArray(props.performances)
      ? props.performances
      : []
  })

  const shifts = computed(() => {
    return Array.isArray(props.shifts)
      ? props.shifts
      : []
  })

  const groupedShifts = computed(() => {
    const groups = [
      {
        key: 'morning',
        label: 'Mañana',
      },
      {
        key: 'afternoon',
        label: 'Tarde',
      },
      {
        key: 'night',
        label: 'Noche',
      },
    ]

    const result = {
      morning: [],
      afternoon: [],
      night: [],
    }

    for (const performance of performances.value) {
      if (!performance?.id) {
        continue
      }

      const workShift = normalizeShift(
        performance.work_shift,
      )

      if (!result[workShift]) {
        continue
      }

      const modelShift = findModelShift(
        performance.id,
      )

      result[workShift].push(
        normalizeModel(
          performance,
          modelShift,
        ),
      )
    }

    return groups.map((group) => ({
      ...group,
      models: result[group.key],
    }))
  })

  function findModelShift(performanceId) {
    const matches = shifts.value.filter(
      (shift) =>
        Number(
          shift?.performance_id ??
          shift?.performance?.id,
        ) === Number(performanceId),
    )

    if (!matches.length) {
      return null
    }

    return [...matches].sort(
      (a, b) =>
        new Date(
          b?.started_at ?? 0,
        ) -
        new Date(
          a?.started_at ?? 0,
        ),
    )[0]
  }

  function normalizeModel(
    performance,
    shift,
  ) {
    const firstName =
      performance.first_name ??
      extractFirstName(
        performance.name,
      )

    const name =
      performance.name ??
      `${firstName} ${
        performance.last_name ?? ''
      }`.trim()

    const activity =
      shift?.activity ?? {}

    const financialSummary =
      activity.financial_summary ?? {}

    const tokens = Number(
      financialSummary.total_tokens ?? 0,
    )

    const usd = Number(
      financialSummary.gross_usd ?? 0,
    )

    const status =
      shift?.status ?? null

    const attended =
      Boolean(shift)

    return {
      id: performance.id,

      name:
        name ||
        performance.nickname ||
        'Sin nombre',

      nickname:
        performance.nickname ?? null,

      initial: (
        firstName ||
        performance.nickname ||
        name ||
        '?'
      )
        .trim()
        .charAt(0)
        .toUpperCase(),

      tokens,

      usd,

      active:
        status === 'active',

      paused:
        status === 'paused',

      attended,

      status,
    }
  }

  function normalizeShift(value) {
    const shift = String(
      value ?? '',
    )
      .trim()
      .toLowerCase()

    switch (shift) {
      case 'morning':
      case 'mañana':
        return 'morning'

      case 'afternoon':
      case 'tarde':
        return 'afternoon'

      case 'night':
      case 'noche':
        return 'night'

      default:
        return null
    }
  }

  function extractFirstName(name) {
    const value = String(
      name ?? '',
    ).trim()

    if (!value) {
      return ''
    }

    return value.split(/\s+/)[0] ?? ''
  }

  function formatTokens(value) {
    return Number(
      value ?? 0,
    ).toLocaleString(
      'es-CO',
      {
        maximumFractionDigits: 0,
      },
    )
  }

  return {
    groupedShifts,
    formatTokens,
  }
}