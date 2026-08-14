<template>
  <section
    class="
      overflow-hidden
      rounded-3xl
      border
      border-gray-800
      bg-gradient-to-br
      from-gray-900
      to-gray-950
      shadow-xl
      shadow-black/20
    "
  >
    <div
      class="
        border-b
        border-gray-800
        px-8
        py-6
      "
    >
      <p
        class="
          text-xs
          uppercase
          tracking-[0.25em]
          text-gray-500
        "
      >
        Operación
      </p>

      <h2
        class="
          mt-2
          text-2xl
          font-bold
          text-white
        "
      >
        Turno operativo del estudio
      </h2>

      <p class="mt-2 text-sm text-gray-400">
        Ganancias registradas por modelo durante la operación
      </p>
    </div>

    <div class="divide-y divide-gray-800">
      <div
        v-for="shift in shifts"
        :key="shift.key"
      >
        <div
          class="
            flex
            items-center
            justify-between
            border-b
            border-gray-800
            bg-gray-900/70
            px-8
            py-4
          "
        >
          <div class="flex items-center gap-3">
            <span
              class="
                h-2
                w-2
                rounded-full
                bg-gray-500
              "
            />

            <h3
              class="
                text-sm
                font-bold
                uppercase
                tracking-[0.2em]
                text-gray-300
              "
            >
              {{ shift.label }}
            </h3>
          </div>

          <span
            class="
              text-xs
              text-gray-500
            "
          >
            {{ shift.models.length }}
            {{ shift.models.length === 1 ? 'modelo' : 'modelos' }}
          </span>
        </div>

        <div
          v-if="shift.models.length"
          class="divide-y divide-gray-800/70"
        >
          <div
            v-for="model in shift.models"
            :key="model.id"
            class="
              grid
              grid-cols-[minmax(0,1fr)_auto_auto]
              items-center
              gap-6
              px-8
              py-4
              transition
              hover:bg-gray-800/30
            "
          >
            <div class="min-w-0">
              <div class="flex items-center gap-3">
                <div
                  class="
                    flex
                    h-9
                    w-9
                    shrink-0
                    items-center
                    justify-center
                    rounded-xl
                    border
                    border-gray-700
                    bg-gray-800
                    text-xs
                    font-bold
                    text-gray-300
                  "
                >
                  {{ model.initial }}
                </div>

                <div class="min-w-0">
                  <p
                    class="
                      truncate
                      font-semibold
                      text-white
                    "
                  >
                    {{ model.name }}
                  </p>

                  <p
                    v-if="model.nickname"
                    class="
                      mt-0.5
                      truncate
                      text-xs
                      text-gray-500
                    "
                  >
                    @{{ model.nickname }}
                  </p>
                </div>
              </div>
            </div>

            <div class="whitespace-nowrap text-right">
              <span
                class="
                  text-lg
                  font-bold
                  text-white
                "
              >
                {{ formatTokens(model.tokens) }}
              </span>

              <span
                class="
                  ml-2
                  text-xs
                  uppercase
                  tracking-wider
                  text-gray-500
                "
              >
                tokens
              </span>
            </div>

            <div class="w-24 text-right">
              <span
                v-if="model.online"
                class="
                  inline-flex
                  items-center
                  gap-2
                  rounded-xl
                  border
                  border-green-500/20
                  bg-green-500/10
                  px-3
                  py-1.5
                  text-xs
                  font-semibold
                  text-green-400
                "
              >
                <span
                  class="
                    h-1.5
                    w-1.5
                    rounded-full
                    bg-green-400
                  "
                />

                En línea
              </span>

              <span
                v-else
                class="
                  text-xs
                  font-medium
                  text-gray-500
                "
              >
                Finalizada
              </span>
            </div>
          </div>
        </div>

        <div
          v-else
          class="
            px-8
            py-6
            text-sm
            text-gray-500
          "
        >
          No hay modelos asignadas a este turno.
        </div>
      </div>
    </div>

    <div
      v-if="!performances.length"
      class="
        border-t
        border-gray-800
        p-10
        text-center
      "
    >
      <p class="text-sm text-gray-500">
        No hay modelos registradas.
      </p>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  performances: {
    type: Array,
    default: () => []
  }
})

const shifts = computed(() => {
  const groups = {
    morning: {
      key: 'morning',
      label: 'Mañana'
    },

    afternoon: {
      key: 'afternoon',
      label: 'Tarde'
    },

    night: {
      key: 'night',
      label: 'Noche'
    }
  }

  const result = {
    morning: [],
    afternoon: [],
    night: []
  }

  for (const performance of props.performances) {
    const shift = normalizeShift(
      performance.work_shift
    )

    if (!result[shift]) {
      continue
    }

    result[shift].push(
      normalizeModel(performance)
    )
  }

  return Object.values(groups).map(
    group => ({
      ...group,
      models: result[group.key]
    })
  )
})

const normalizeModel = (performance) => {
  const firstName =
    performance.first_name ?? ''

  const lastName =
    performance.last_name ?? ''

  const name =
    `${firstName} ${lastName}`.trim()

  const earnings =
    Array.isArray(performance.earnings)
      ? performance.earnings
      : []

  const tokens =
    earnings.reduce(
      (total, earning) => {
        return (
          total +
          Number(
            earning.real_tokens ?? 0
          )
        )
      },
      0
    )

  return {
    id: performance.id,

    name:
      name ||
      performance.nickname ||
      'Sin nombre',

    nickname:
      performance.nickname ?? null,

    initial:
      (
        firstName ||
        performance.nickname ||
        '?'
      )
        .charAt(0)
        .toUpperCase(),

    tokens,

    online:
      performance.active === true
  }
}

const normalizeShift = (value) => {
  const shift =
    String(value ?? '')
      .trim()
      .toLowerCase()

  if (
    shift === 'morning' ||
    shift === 'mañana'
  ) {
    return 'morning'
  }

  if (
    shift === 'afternoon' ||
    shift === 'tarde'
  ) {
    return 'afternoon'
  }

  if (
    shift === 'night' ||
    shift === 'noche'
  ) {
    return 'night'
  }

  return shift
}

const formatTokens = (value) => {
  return Number(value || 0).toLocaleString(
    'en-US',
    {
      maximumFractionDigits: 2
    }
  )
}
</script>