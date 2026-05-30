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

                <!-- ACTIVE BAR -->
                <span
                    v-if="route.path.startsWith(item.path)"
                    class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500 rounded-r"
                />

                <!-- ICON -->
                <span
                    class="text-lg transition"
                    :class="route.path.startsWith(item.path)
                        ? 'text-blue-400'
                        : 'text-gray-500 group-hover:text-white'"
                >
                    {{ item.icon }}
                </span>

                <!-- TEXT -->
                <span class="font-medium">
                    {{ item.name }}
                </span>

            </RouterLink>

        </nav>

        <!--
        |--------------------------------------------------------------------------
        | USER PANEL (TEMPORALMENTE DESACTIVADO)
        |--------------------------------------------------------------------------
        |

        | Se reactivará cuando:
        | - auth esté estabilizado
        | - roles dinámicos funcionen
        | - refresh session esté completo
        | - logout no rompa render
        |
        -->

        <!--
        <div class="p-4 border-t border-gray-800">

            <div class="bg-gray-900 rounded-2xl p-4">

                <div class="flex items-center gap-2 text-xs text-emerald-400 mb-4">

                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>

                    Sesión activa

                </div>

                <div class="flex items-center gap-3">

                    <div
                        class="w-11 h-11 rounded-2xl bg-blue-600 flex items-center justify-center font-bold text-white"
                    >
                        {{ initials }}
                    </div>

                    <div class="min-w-0">

                        <p class="text-sm font-semibold truncate">
                            {{ auth.user?.name || 'Usuario' }}
                        </p>

                        <p class="text-xs text-gray-400 truncate">
                            {{ auth.user?.email || 'Sin email' }}
                        </p>

                    </div>

                </div>

                <div
                    v-if="auth.roles?.length"
                    class="flex flex-wrap gap-2 mt-4"
                >

                    <span
                        v-for="role in auth.roles"
                        :key="role"
                        class="text-[10px] px-2 py-1 rounded-full bg-purple-500/10 border border-purple-500/20 text-purple-300"
                    >
                        {{ role }}
                    </span>

                </div>

            </div>

            <button
                @click="logout"
                class="mt-4 w-full flex items-center justify-center gap-2 bg-red-500/10 hover:bg-red-600 text-red-400 hover:text-white py-3 rounded-2xl transition"
            >

                <span>
                    ⏻
                </span>

                <span>
                    Cerrar sesión
                </span>

            </button>

        </div>
        -->

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

    {
        name: 'Dashboard',
        path: '/dashboard',
        icon: '📊',
    },

    {
        name: 'Modelos',
        path: '/models',
        icon: '👩',
    },

    {
        name: 'Finanzas',
        path: '/finances',
        icon: '💰',
    },

    {
        name: 'Analytics',
        path: '/analytics',
        icon: '📈',
    },

    {
        name: 'Configuración',
        path: '/settings',
        icon: '⚙️',
    },

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