<template>

  <div
    class="bg-gradient-to-br from-gray-900 to-gray-950 border border-gray-800 rounded-3xl p-8"
  >

    <div class="flex flex-col lg:flex-row lg:items-center gap-8">

      <!-- AVATAR -->
      <div
        class="w-28 h-28 rounded-3xl overflow-hidden border border-gray-700 bg-gray-800 flex items-center justify-center shrink-0"
      >

        <img
          v-if="model.profile_photo"
          :src="model.profile_photo"
          :alt="model.nickname"
          class="w-full h-full object-cover"
        />

        <span
          v-else
          class="text-4xl font-bold text-blue-400"
        >
          {{ initials }}
        </span>

      </div>

      <!-- INFO -->
      <div class="flex-1">

        <div class="flex flex-col lg:flex-row lg:items-center gap-4">

          <div>
            <h1 class="text-4xl font-bold text-white mt-1">
              {{ fullName }}
            </h1>
          </div>

          <span
            class="self-start px-4 py-2 rounded-full text-xs font-semibold border"
            :class="
              model.active
                ? 'bg-green-500/10 text-green-400 border-green-500/20'
                : 'bg-red-500/10 text-red-400 border-red-500/20'
            "
          >
            ● {{ model.active ? 'Activo' : 'Inactivo' }}
          </span>

        </div>

        <div class="mt-5 space-y-2">

          <p class="text-blue-400 font-medium">
            @{{ model.nickname || 'Sin nickname' }}
          </p>

        </div>

        <!-- ========================= -->
        <!-- WEEKLY HOURS PROGRESS -->
        <!-- ========================= -->

        <div class="mt-6">

          <div class="flex items-center justify-between mb-2">

            <p class="text-gray-400 text-xs uppercase tracking-[0.2em]">
              Horas semanales
            </p>

            <p class="text-white text-sm font-semibold">
              {{ hours }} / {{ weeklyGoal }} h
            </p>

          </div>

          <div class="relative w-full h-3 bg-gray-800 rounded-full overflow-hidden border border-gray-700">

            <div
              class="h-full transition-all duration-500 rounded-full"
              :class="barColor"
              :style="{ width: progress + '%' }"
            />

            <!-- indicador final -->
            <div
              class="absolute top-1/2 -translate-y-1/2"
              :style="{ left: progress + '%' }"
            >
              <div
                class="w-5 h-5 rounded-full border-2 border-gray-900 shadow-md"
                :class="dotColor"
              />
            </div>

          </div>

        </div>

      </div>

    </div>

  </div>

</template>

<script setup>

import { computed } from 'vue'

const props = defineProps({
  model: {
    type: Object,
    required: true
  }
})

const weeklyGoal = 36

const hours = computed(() => Number(props.model.hours_streamed || 0))

const progress = computed(() =>
  Math.min((hours.value / weeklyGoal) * 100, 100)
)

const initials = computed(() =>
  (
    (props.model.first_name?.charAt(0) || '') +
    (props.model.last_name?.charAt(0) || '')
  ).toUpperCase()
)

const fullName = computed(() =>
  `${props.model.first_name || ''} ${props.model.last_name || ''}`
)

/* =========================
   COLOR LOGIC
========================= */

const barColor = computed(() => {
  if (progress.value < 50) return 'bg-orange-500'
  if (progress.value < 80) return 'bg-yellow-400'
  return 'bg-green-500'
})

const dotColor = computed(() => {
  if (progress.value < 50) return 'bg-orange-500'
  if (progress.value < 80) return 'bg-yellow-400'
  return 'bg-green-500'
})

</script>