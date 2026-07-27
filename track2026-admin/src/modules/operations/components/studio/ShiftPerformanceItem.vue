<template>
  <button
    type="button"
    @click="selectShift"
    class="relative w-full overflow-hidden rounded-2xl border p-5 text-left transition-all duration-200"
    :class="cardClass"
  >
    <div
      v-if="selected"
      class="absolute inset-y-0 left-0 w-1 rounded-l-2xl bg-emerald-400"
    />

    <div class="flex items-center gap-4">
      <div
        class="flex h-12 w-12 shrink-0 overflow-hidden rounded-xl transition-all duration-200"
        :class="avatarClass"
      >
        <img
          v-if="avatar"
          :src="avatar"
          :alt="nickname"
          class="h-full w-full object-cover"
        >
        <div
          v-else
          class="flex h-full w-full items-center justify-center text-lg font-bold"
        >
          {{ initials }}
        </div>
      </div>

      <div class="min-w-0 flex-1">
        <h3
          class="truncate font-semibold transition-colors"
          :class="selected ? 'text-emerald-300' : 'text-white'"
        >
          {{ displayName }}
        </h3>
        <p
          class="truncate text-sm transition-colors"
          :class="selected ? 'text-emerald-400/80' : 'text-gray-400'"
        >
          {{ nickname }}
        </p>
      </div>

      <span
        class="rounded-full border px-3 py-1 text-xs font-semibold"
        :class="statusBadgeClass"
      >
        {{ statusLabel }}
      </span>
    </div>

    <!-- Pie -->
    <div class="mt-5 border-t border-gray-800 pt-4">
      <p class="text-xs uppercase tracking-wide text-gray-500">
        Estado del turno
      </p>
      <div class="mt-2 flex items-center justify-between">
        <span class="font-medium capitalize" :class="statusTextClass">
          {{ shift.status }}
        </span>
        <svg
          class="h-5 w-5 transition-all duration-200"
          :class="[
            selected ? 'rotate-90 text-emerald-300' : 'text-gray-600'
          ]"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M9 5l7 7-7 7"
          />
        </svg>
      </div>
    </div>
  </button>
</template>

<script setup>
import { computed, toRefs } from 'vue'

defineOptions({
  name: 'ShiftPerformanceItem',
})

const props = defineProps({
  shift: {
    type: Object,
    required: true,
  },
  selected: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['select'])

const { shift, selected } = toRefs(props)

const performance = computed(() => shift.value.performance ?? {})
const user = computed(() => performance.value.user ?? {})

const displayName = computed(() => {
  if (performance.value.name) {
    return performance.value.name
  }
  const fullName = [
    performance.value.first_name,
    performance.value.last_name,
  ]
    .filter(Boolean)
    .join(' ')
  if (fullName) {
    return fullName
  }
  return user.value.name || 'Modelo'
})

const avatar = computed(() => {
  return (
    performance.value.profile_photo ||
    user.value.profile_photo ||
    performance.value.avatar_url ||
    user.value.avatar_url ||
    performance.value.avatar ||
    user.value.avatar ||
    performance.value.photo ||
    user.value.photo ||
    performance.value.image ||
    user.value.image ||
    null
  )
})

const nickname = computed(() => {
  return (
    performance.value.nickname ||
    user.value.nickname ||
    'Sin nickname'
  )
})

const initials = computed(() => {
  const first =
    performance.value.first_name?.charAt(0) ||
    user.value.name?.charAt(0) ||
    ''
  const last = performance.value.last_name?.charAt(0) || ''
  if (first || last) {
    return `${first}${last}`.toUpperCase()
  }
  return displayName.value
    .trim()
    .split(/\s+/)
    .map(word => word.charAt(0))
    .join('')
    .slice(0, 2)
    .toUpperCase()
})

const cardClass = computed(() =>
  selected.value
    ? `
      border-emerald-500/60
      bg-emerald-500/10
      shadow-lg
      shadow-emerald-500/10
      ring-1
      ring-emerald-500/20
    `
    : `
      border-gray-800
      bg-gray-900
      hover:border-gray-700
      hover:bg-gray-800/60
      hover:-translate-y-0.5
      hover:shadow-lg
      hover:shadow-black/20
    `
)

const avatarClass = computed(() =>
  selected.value
    ? `
      bg-emerald-500/10
      ring-2
      ring-emerald-400/30
    `
    : `
      bg-gray-800
    `
)

const statusLabel = computed(() => {
  switch (shift.value.status) {
    case 'active':
      return 'En turno'
    case 'paused':
      return 'Pausado'
    default:
      return 'Finalizado'
  }
})

const statusBadgeClass = computed(() => {
  switch (shift.value.status) {
    case 'active':
      return `
        border-emerald-500/30
        bg-emerald-500/10
        text-emerald-300
      `
    case 'paused':
      return `
        border-amber-500/30
        bg-amber-500/10
        text-amber-300
      `
    default:
      return `
        border-gray-700
        bg-gray-800
        text-gray-400
      `
  }
})

const statusTextClass = computed(() => {
  switch (shift.value.status) {
    case 'active':
      return 'text-emerald-300'
    case 'paused':
      return 'text-amber-300'
    default:
      return 'text-gray-400'
  }
})

function selectShift() {
  emit('select', shift.value)
}
</script>
