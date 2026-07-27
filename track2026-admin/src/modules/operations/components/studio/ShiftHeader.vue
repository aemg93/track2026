<template>
  <div
    v-if="shift"
    class="rounded-2xl border border-gray-800 bg-gray-900/80 backdrop-blur-sm p-5"
  >
    <!-- Header -->
    <div class="flex items-start justify-between">

      <div>

        <h3 class="text-lg font-semibold text-white">
          {{ nickname }}
        </h3>

        <div
          class="mt-3 inline-flex items-center gap-2 rounded-full border px-3 py-1"
          :class="status.badge"
        >
          <span
            class="h-2 w-2 rounded-full"
            :class="status.dot"
          />

          <span
            class="text-xs font-semibold"
            :class="status.text"
          >
            {{ status.label }}
          </span>
        </div>

      </div>

      <!-- Relojes -->
      <div class="space-y-3 text-right">

        <template v-if="currentStatus === 'active'">

          <div>

            <p class="text-[11px] uppercase tracking-widest text-gray-500">
              Tiempo en estudio
            </p>

            <div class="mt-1 rounded-lg border border-cyan-500/20 bg-cyan-500/5 px-3 py-2">

              <span class="font-mono text-xl font-bold text-cyan-300">
                {{ studioTime }}
              </span>

            </div>

          </div>

        </template>

        <template v-else-if="currentStatus === 'paused'">

          <div>

            <p class="text-[11px] uppercase tracking-widest text-gray-500">
              Horas transmitidas
            </p>

            <div class="mt-1 rounded-lg border border-emerald-500/20 bg-emerald-500/5 px-3 py-2">

              <span class="font-mono text-xl font-bold text-emerald-300">
                {{ workedTime }}
              </span>

            </div>

          </div>

        </template>

        <template v-else>

          <div>

            <p class="text-[11px] uppercase tracking-widest text-gray-500">
              Tiempo en estudio
            </p>

            <div class="mt-1 rounded-lg border border-cyan-500/20 bg-cyan-500/5 px-3 py-2">

              <span class="font-mono text-lg font-bold text-cyan-300">
                {{ studioTime }}
              </span>

            </div>

          </div>

          <div>

            <p class="text-[11px] uppercase tracking-widest text-gray-500">
              Horas transmitidas
            </p>

            <div class="mt-1 rounded-lg border border-emerald-500/20 bg-emerald-500/5 px-3 py-2">

              <span class="font-mono text-lg font-bold text-emerald-300">
                {{ workedTime }}
              </span>

            </div>

          </div>

        </template>

      </div>

    </div>

    <!-- Botones -->
    <div class="mt-6">

      <button
        v-if="shift.actions?.can_pause"
        type="button"
        class="w-full rounded-xl border border-amber-500/30 bg-amber-500/10 py-3 text-sm font-semibold text-amber-300 transition duration-200 hover:border-amber-400 hover:bg-amber-500/20"
        @click="handlePause"
      >
        ⏸ Pausar turno
      </button>

      <button
        v-else-if="shift.actions?.can_resume"
        type="button"
        class="w-full rounded-xl border border-emerald-500/30 bg-emerald-500/10 py-3 text-sm font-semibold text-emerald-300 transition duration-200 hover:border-emerald-400 hover:bg-emerald-500/20"
        @click="handleResume"
      >
        ▶ Reanudar turno
      </button>

    </div>

    <!-- Footer -->
    <div
      class="mt-6 grid grid-cols-2 gap-5 border-t border-gray-800 pt-5"
    >

      <div>

        <p class="text-[11px] uppercase tracking-widest text-gray-500">
          Inicio
        </p>

        <p class="mt-2 text-sm font-semibold text-white">
          {{ startedAt }}
        </p>

      </div>

      <div>

        <p class="text-[11px] uppercase tracking-widest text-gray-500">
          Estudio
        </p>

        <p class="mt-2 truncate text-sm font-semibold text-white">
          {{ studio }}
        </p>

      </div>

    </div>

  </div>
</template>

<script setup>
import {
  computed,
  onUnmounted,
  ref,
  watch,
} from 'vue'

defineOptions({
  name: 'ShiftHeader',
})

const props = defineProps({
  shift: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits([
  'pause',
  'resume',
])

const now = ref(Date.now())

let timerId = null

const nickname = computed(() =>
  props.shift?.performance?.nickname ?? 'Sin nickname'
)

const studio = computed(() =>
  props.shift?.studio?.name ?? 'No asignado'
)

const currentStatus = computed(() => {

  const status = props.shift?.status

  if (!status) {
    return null
  }

  if (typeof status === 'string') {
    return status.toLowerCase()
  }

  if (typeof status === 'object') {
    return (status.value ?? '').toLowerCase()
  }

  return String(status).toLowerCase()

})

const STATUS = {

  active: {
    label: 'En turno',
    dot: 'bg-emerald-400',
    text: 'text-emerald-400',
  },

  paused: {
    label: 'Pausado',
    dot: 'bg-yellow-400',
    text: 'text-yellow-400',
  },

  finished: {
    label: 'Finalizado',
    dot: 'bg-gray-400',
    text: 'text-gray-400',
  },

}

const status = computed(() =>
  STATUS[currentStatus.value] ?? {
    label: 'Desconocido',
    dot: 'bg-gray-500',
    text: 'text-gray-500',
  }
)

const startedAt = computed(() => {

  const started = parseDate(props.shift?.started_at)

  return started
    ? started.toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit',
      })
    : '--:--'

})

function parseDate(value) {

  if (!value) {
    return null
  }

  const date = new Date(value)

  return Number.isNaN(date.getTime())
    ? null
    : date

}

function timestamp(value) {

  return parseDate(value)?.getTime() ?? null

}

function formatDuration(seconds = 0) {

  const hours = String(
    Math.floor(seconds / 3600)
  ).padStart(2, '0')

  const minutes = String(
    Math.floor((seconds % 3600) / 60)
  ).padStart(2, '0')

  const secs = String(
    seconds % 60
  ).padStart(2, '0')

  return `${hours}:${minutes}:${secs}`

}

function secondsBetween(from, to = Date.now()) {

  return from === null
    ? 0
    : Math.max(
        0,
        Math.floor((to - from) / 1000)
      )

}

function stopTimer() {

  if (timerId) {
    clearInterval(timerId)
    timerId = null
  }

}

function startTimer() {

  stopTimer()

  const running =
    props.shift &&
    currentStatus.value !== 'finished'

  if (!running) {
    return
  }

  now.value = Date.now()

  timerId = setInterval(() => {
    now.value = Date.now()
  }, 1000)

}

const workedTime = computed(() => {

  const accumulated = Number(
    props.shift?.worked_seconds ?? 0
  )

  const currentSession =
    currentStatus.value === 'active'
      ? secondsBetween(
          timestamp(
            props.shift?.last_resumed_at
          ),
          now.value
        )
      : 0

  return formatDuration(
    accumulated + currentSession
  )

})

const studioTime = computed(() => {

  return formatDuration(

    secondsBetween(

      timestamp(
        props.shift?.started_at
      ),

      timestamp(
        props.shift?.ended_at
      ) ?? now.value

    )

  )

})

function handlePause() {
  emit('pause', props.shift)
}

function handleResume() {
  emit('resume', props.shift)
}

watch(
  () => [
    props.shift?.id,
    currentStatus.value,
    props.shift?.worked_seconds,
    props.shift?.last_resumed_at,
    props.shift?.ended_at,
  ],
  startTimer,
  {
    immediate: true,
  }
)

onUnmounted(() => {
  stopTimer()
})
</script>