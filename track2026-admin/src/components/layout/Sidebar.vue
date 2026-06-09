<template>

    <aside class="w-64 h-screen bg-gray-950 text-white flex flex-col border-r border-gray-800">

        <!-- BRAND -->
        <div class="p-6 border-b border-gray-800">

            <h1 class="text-xl font-bold tracking-wide">
                Track Model
            </h1>

            <p class="text-xs text-gray-500 mt-1">
                Gestor de Modelos
            </p>

        </div>

        <!-- NAVIGATION -->
        <nav class="flex-1 p-3 space-y-1">

            <RouterLink
                v-for="item in menu"
                :key="item.path"
                :to="item.path"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition relative overflow-hidden group"
                :class="isActive(item.path)"
            >

                <span
                    v-if="route.path.startsWith(item.path)"
                    class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500 rounded-r"
                />

                <span
                    class="text-lg transition"
                    :class="route.path.startsWith(item.path)
                        ? 'text-blue-400'
                        : 'text-gray-500 group-hover:text-white'"
                >
                    {{ item.icon }}
                </span>

                <span class="font-medium">
                    {{ item.name }}
                </span>

            </RouterLink>

        </nav>

    </aside>

</template>

<script setup>

import { useRoute } from 'vue-router'

const route = useRoute()

/*
|--------------------------------------------------------------------------
| MENU
|--------------------------------------------------------------------------
*/

const menu = [

    { name: 'Dashboard', path: '/dashboard', icon: '📊' },
    { name: 'Modelos', path: '/performances', icon: '👩' },
    { name: 'Finanzas', path: '/finances', icon: '💰' },
    { name: 'Analytics', path: '/analytics', icon: '📈' },
    { name: 'Configuración', path: '/settings', icon: '⚙️' },

]

/*
|--------------------------------------------------------------------------
| ACTIVE STATE
|--------------------------------------------------------------------------
*/

const isActive = (path) => {

    return route.path.startsWith(path)
        ? 'bg-blue-500/10 text-white shadow-[0_0_20px_rgba(59,130,246,0.15)]'
        : 'text-gray-400 hover:bg-gray-800/60 hover:text-white'
}

</script>