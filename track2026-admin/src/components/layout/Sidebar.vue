<template>

  <aside class="w-64 h-screen bg-gray-950 text-white flex flex-col border-r border-gray-800">
    <div class="p-6 border-b border-gray-800">
      <h1 class="text-xl font-bold tracking-wide">
        Track Model
      </h1>
      <p class="text-xs text-gray-500 mt-1">
        Gestor de Modelos
      </p>

    </div>
    <nav class="flex-1 p-3 space-y-1">

      <RouterLink
        v-for="item in menu"
        :key="item.path"
        :to="item.path"
        class="
          group
          relative
          flex
          items-center
          gap-3
          overflow-hidden
          rounded-xl
          px-4
          py-3
          transition-all
          duration-300
          ease-out
        "
        :class="isActive(item.path)"
      >

        <span
          class="
            absolute
            left-0
            top-0
            bottom-0
            w-1
            origin-top
            scale-y-0
            rounded-r
            transition-transform
            duration-300
            ease-out
          "
          :class="[
            route.path.startsWith(item.path)
              ? 'scale-y-100'
              : '',
            item.barColor
          ]"
        />

        <component
          :is="item.icon"
          class="
            h-5
            w-5
            shrink-0
            transition-all
            duration-300
            group-hover:scale-110
          "
          :class="[
            item.color,
            item.hoverColor,

            route.path.startsWith(item.path)
              ? 'drop-shadow-lg scale-110'
              : ''
          ]"
          :stroke-width="
            route.path.startsWith(item.path)
              ? 2.5
              : 2
          "
        />

        <span
          class="
            font-medium
            transition-transform
            duration-300
            group-hover:translate-x-0.5
          "
        >
          {{ item.name }}
        </span>
      </RouterLink>
    </nav>
  </aside>
</template>

<script setup>

import { useRoute } from 'vue-router'


import {
  LayoutDashboard,
  SlidersHorizontal,
  UsersRound,
  WalletCards,
  ChartLine,
  Settings
} from 'lucide-vue-next'

const route = useRoute()

const menu = [


  {
    name: 'Dashboard',
    path: '/dashboard',
    icon: LayoutDashboard,
    color: 'text-blue-400',
    hoverColor: 'group-hover:text-blue-300',
    barColor: 'bg-blue-500'
  },


  {
    name: 'Operaciones',
    path: '/operations',
    icon: SlidersHorizontal,
    color: 'text-cyan-400',
    hoverColor: 'group-hover:text-cyan-300',
    barColor: 'bg-cyan-500'
  },


  {
    name: 'Modelos',
    path: '/performances',
    icon: UsersRound,
    color: 'text-pink-400',
    hoverColor: 'group-hover:text-pink-300',
    barColor: 'bg-pink-500'
  },


  {
    name: 'Finanzas',
    path: '/finances',
    icon: WalletCards,
    color: 'text-emerald-400',
    hoverColor: 'group-hover:text-emerald-300',
    barColor: 'bg-emerald-500'
  },


  {
    name: 'Analytics',
    path: '/analytics',
    icon: ChartLine,
    color: 'text-orange-400',
    hoverColor: 'group-hover:text-orange-300',
    barColor: 'bg-orange-500'
  },


  {
    name: 'Configuración',
    path: '/settings',
    icon: Settings,
    color: 'text-indigo-400',
    hoverColor: 'group-hover:text-indigo-300',
    barColor: 'bg-indigo-500'
  },


]

const isActive = (path) => {

  return route.path.startsWith(path)

    ? `
        bg-white/5
        text-white
        shadow-[0_0_20px_rgba(255,255,255,0.05)]
      `

    : `
        text-gray-400
        hover:bg-gray-800/60
        hover:text-white
      `

}

</script>