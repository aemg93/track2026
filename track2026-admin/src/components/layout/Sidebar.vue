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

        <!-- NAV -->
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
                ></span>

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

        <!-- USER PANEL (DESACTIVADO PERO LISTO) -->
        <div v-if="false" class="p-4 border-t border-gray-800">

            <div class="bg-blue-500/10 rounded-2xl p-4 space-y-3">

                <!-- STATUS -->
                <div class="flex items-center gap-2 text-xs text-blue-400">
                    <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                    Sesión activa
                </div>

                <!-- USER -->
                <div class="flex items-center gap-3">

                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center font-bold">
                        {{ initials }}
                    </div>

                    <div class="min-w-0">
                        <p class="text-sm font-semibold truncate">
                            {{ auth.user?.name || 'Usuario' }}
                        </p>
                        <p class="text-xs text-gray-400 truncate">
                            {{ auth.user?.email || 'sin email' }}
                        </p>
                    </div>

                </div>

                <!-- ROLES -->
                <div class="flex flex-wrap gap-1">

                    <span
                        v-for="role in auth.roles"
                        :key="role"
                        class="text-[10px] px-2 py-1 rounded-full bg-purple-900/20 text-purple-300"
                    >
                        {{ role }}
                    </span>

                </div>

            </div>

            <!-- LOGOUT -->
            <button
                @click="logout"
                class="mt-3 w-full flex items-center justify-center gap-2 bg-red-500/10 hover:bg-red-600 text-red-400 hover:text-white py-2 rounded-xl transition"
            >
                Cerrar sesión
            </button>

        </div>

    </aside>

</template>

<script setup>

import { computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

/*
|--------------------------------------------------------------------------
| MENU
|--------------------------------------------------------------------------
*/

const menu = [
    { name: 'Dashboard', path: '/dashboard', icon: '📊' },
    { name: 'Estadísticas', path: '/performances', icon: '📈' },
    { name: 'Ganancias', path: '/earnings', icon: '💰' },
    { name: 'Bonos', path: '/bonuses', icon: '🎁' },
    { name: 'Penalizaciones', path: '/penalties', icon: '⚠️' },
]

/*
|--------------------------------------------------------------------------
| ACTIVE STATE
|--------------------------------------------------------------------------
*/

const isActive = (path) => {

    return route.path.startsWith(path)
        ? 'bg-blue-500/10 text-white shadow-[0_0_15px_rgba(59,130,246,0.15)]'
        : 'text-gray-400 hover:bg-gray-800/60 hover:text-white'
}

/*
|--------------------------------------------------------------------------
| INITIALS (FUTURO PANEL USER)
|--------------------------------------------------------------------------
*/

const initials = computed(() => {
    const name = auth.user?.name || 'U'

    return name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
})

/*
|--------------------------------------------------------------------------
| LOGOUT (FUTURO PANEL USER)
|--------------------------------------------------------------------------
*/

const logout = async () => {
    await auth.logout()
    router.push('/login')
}

</script>