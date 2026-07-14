<template>
  <div
    v-if="shift"
    class="rounded-2xl border border-gray-800 bg-gray-900 p-4"
  >
    <div class="flex items-start justify-between gap-6">
      <div class="flex-1">
        <h3 class="text-lg font-semibold text-white">
          {{ nickname }}
        </h3>

        <div class="mt-2 flex items-center gap-2">
          <span class="h-2 w-2 rounded-full" :class="status.dot" />
          <span class="text-sm font-medium" :class="status.text">
            {{ status.label }}
          </span>
        </div>
      </div>

      <div class="text-right">
        <p class="text-xs uppercase tracking-wide text-gray-500">
          {{ elapsedLabel }}
        </p>
        <p class="mt-1 font-mono text-xl font-bold text-emerald-400">
          {{ elapsed }}
        </p>
      </div>
    </div>

    <div class="mt-5">
      <button
        v-if="shift.actions?.can_pause"
        type="button"
        class="w-full rounded-xl bg-amber-600 py-2.5 font-medium text-white transition hover:bg-amber-500"
        @click="handlePause"
      >
        ⏸ Pausar turno
      </button>

      <button
        v-else-if="shift.actions?.can_resume"
        type="button"
        class="w-full rounded-xl bg-emerald-600 py-2.5 font-medium text-white transition hover:bg-emerald-500"
        @click="handleResume"
      >
        ▶ Reanudar turno
      </button>
    </div>

    <div class="mt-5 grid grid-cols-2 gap-4 border-t border-gray-800 pt-4">
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500">
          Inicio
        </p>
        <p class="mt-1 text-sm font-semibold text-white">
          {{ startedAt }}
        </p>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500">
          Estudio
        </p>
        <p class="mt-1 truncate text-sm font-semibold text-white">
          {{ studio }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>

import { computed, onUnmounted, ref, watch } from 'vue'

defineOptions({ name: 'ShiftHeader' })

const props = defineProps({
  shift: { type: Object, default: null },
})

const emit = defineEmits(['pause', 'resume'])

const now = ref(Date.now())
let timerId = null

const nickname = computed(() => props.shift?.performance?.nickname ?? 'Sin nickname')
const studio = computed(() => props.shift?.studio?.name ?? 'No asignado')

const STATUS = {
  active: { label: 'En turno', dot: 'bg-emerald-400', text: 'text-emerald-400' },
  paused: { label: 'Pausado', dot: 'bg-yellow-400', text: 'text-yellow-400' },
  finished: { label: 'Finalizado', dot: 'bg-gray-400', text: 'text-gray-400' },
}

const status = computed(() =>
  STATUS[props.shift?.status] ?? { label: 'Desconocido', dot: 'bg-gray-500', text: 'text-gray-500' }
)

const startedAt = computed(() => {
  if (!props.shift?.started_at) return '--:--'
  const date = new Date(props.shift.started_at)
  return Number.isNaN(date.getTime())
    ? '--:--'
    : date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
})

function stopTimer() {
  if (timerId) { clearInterval(timerId); timerId = null }
}
function startTimer() {
  stopTimer()
  if (!props.shift || props.shift.status !== 'active') return
  now.value = Date.now()
  timerId = setInterval(() => { now.value = Date.now() }, 1000)
}

function formatDuration(seconds) {
  const h = String(Math.floor(seconds / 3600)).padStart(2, '0')
  const m = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0')
  const s = String(seconds % 60).padStart(2, '0')
  return `${h}:${m}:${s}`
}

const elapsed = computed(() => {
  if (!props.shift) return '00:00:00'
  let worked = Number(props.shift.worked_seconds ?? 0)
  if (props.shift.status !== 'active') return formatDuration(worked)

  const reference = props.shift.paused_at
    ? new Date(props.shift.paused_at).getTime()
    : new Date(props.shift.started_at).getTime()

  if (Number.isNaN(reference)) return formatDuration(worked)

  const liveSeconds = Math.max(0, Math.floor((now.value - reference) / 1000))
  return formatDuration(worked + liveSeconds)
})

const elapsedLabel = computed(() => {
  switch (props.shift?.status) {
    case 'active': return 'Tiempo en turno'
    case 'paused':
    case 'finished': return 'Horas trabajadas'
    default: return 'Tiempo en línea'
  }
})

function handlePause() { emit('pause', props.shift) }
function handleResume() { emit('resume', props.shift) }

watch(
  () => [props.shift?.id, props.shift?.status, props.shift?.worked_seconds, props.shift?.paused_at],
  () => { startTimer() },
  { immediate: true }
)

onUnmounted(() => { stopTimer() })
</script>
