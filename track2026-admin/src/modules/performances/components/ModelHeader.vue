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
          :alt="fullName"
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

          <h1 class="text-4xl font-bold text-white">
            {{ fullName }}
          </h1>


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


        <p class="text-blue-400 font-medium mt-3">
          @{{ model.nickname || 'sin-nickname' }}
        </p>



        <!-- HORAS -->
        <div class="mt-8">


          <div class="flex items-end justify-between mb-3">


            <div>

              <p
                class="text-gray-400 text-xs uppercase tracking-[0.2em]"
              >
                Horas semanales
              </p>


              <p
                class="text-white text-3xl font-bold mt-1"
              >
                {{ formattedHours }}
              </p>

            </div>


            <div class="text-right">

              <p class="text-gray-500 text-xs">
                Meta semanal
              </p>

              <p class="text-gray-300 font-semibold">
                {{ formattedGoal }}
              </p>

            </div>


          </div>




          <!-- BARRA -->
          <div
            class="relative w-full h-3 bg-gray-800 rounded-full overflow-hidden border border-gray-700"
          >

            <div
              class="h-full rounded-full transition-all duration-700"
              :class="barColor"
              :style="{
                width: progress + '%'
              }"
            />


            <div
              class="absolute top-1/2 -translate-y-1/2 transition-all duration-700"
              :style="{
                left: progress + '%'
              }"
            >

              <div
                class="w-5 h-5 rounded-full border-2 border-gray-900 shadow-md"
                :class="dotColor"
              />

            </div>


          </div>



          <div
            class="flex justify-between mt-3 text-xs text-gray-500"
          >

            <span>
              {{ progress.toFixed(1) }}%
            </span>


            <span>
              Tiempo registrado:
              {{ formattedHours }}
            </span>

          </div>


        </div>


      </div>


    </div>


  </div>
</template>



<script setup>

import {
  computed
} from 'vue'


const props = defineProps({

  model: {
    type: Object,
    required: true
  }

})

const weeklyGoalSeconds = 36 * 3600

const weeklySeconds = computed(() => {

  const hours = props.model?.hours

  if (
    hours?.weekly_seconds
  ) {

    return Number(
      hours.weekly_seconds
    )

  }

  if (
    hours?.weekly
  ) {

    return Number(
      hours.weekly
    ) * 3600

  }

  return 0

})

const formattedHours = computed(() =>
  formatTime(
    weeklySeconds.value
  )
)

const formattedGoal = computed(() =>
  formatTime(
    weeklyGoalSeconds
  )
)

const progress = computed(() => {

  return Math.min(
    (
      weeklySeconds.value /
      weeklyGoalSeconds
    ) * 100,
    100
  )

})

function formatTime(seconds) {

  const hours = Math.floor(
    seconds / 3600
  )

  const minutes = Math.floor(
    (
      seconds % 3600
    ) / 60
  )

  const secs =
    seconds % 60

  return [

    hours,
    minutes,
    secs

  ]
  .map(
    value =>
      String(value)
        .padStart(2, '0')
  )
  .join(':')

}


const initials = computed(() => {

  const first =
    props.model?.first_name?.charAt(0) || ''


  const last =
    props.model?.last_name?.charAt(0) || ''


  return (
    first + last
  )
  .toUpperCase()

})


const fullName = computed(() =>

  `${props.model?.first_name || ''} ${props.model?.last_name || ''}`
    .trim()

)

const barColor = computed(() => {

  if (
    progress.value < 50
  ) {

    return 'bg-orange-500'

  }

  if (
    progress.value < 80
  ) {

    return 'bg-yellow-400'

  }

  return 'bg-green-500'

})

const dotColor = computed(() => {

  if (
    progress.value < 50
  ) {

    return 'bg-orange-500'

  }

  if (
    progress.value < 80
  ) {

    return 'bg-yellow-400'

  }

  return 'bg-green-500'

})

</script>