<template>

    <aside class="w-64 h-screen bg-gray-950 text-white flex flex-col border-r border-gray-800">

        <!-- BRAND -->
        <div class="p-6 border-b border-gray-800">

            <h1 class="text-xl font-bold tracking-wide">
                Track Model
            </h1>

            <p class="text-xs text-gray-500 mt-1">
                Gestor de  Modelos
            </p>

        </div>

        <!-- NAV -->
        <nav class="flex-1 p-3 space-y-1">

            <RouterLink
                v-for="item in menu"
                :key="item.path"
                :to="item.path"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition group"
                :class="isActive(item.path)"
            >

                <span class="text-gray-400 group-hover:text-white">

                    {{ item.icon }}

                </span>

                <span>
                    {{ item.name }}
                </span>

            </RouterLink>

        </nav>

        <div class="p-4 border-t border-gray-800">

            <div class="bg-green-900/60 rounded-2xl p-4 space-y-3">

                <!-- STATUS -->
                <div class="flex items-center gap-2 text-xs text-green-400">

                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>

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
const menu = [

    { name: 'Dashboard', path: '/dashboard', icon: '📊' },
    { name: 'Desempeño', path: '/performances', icon: '📈' },
    { name: 'Ganancias', path: '/earnings', icon: '💰' },
    { name: 'Bonos', path: '/bonuses', icon: '🎁' },
    { name: 'Penalizaciones', path: '/penalties', icon: '⚠️' },

]

const isActive = (path) => {

    return route.path.startsWith(path)
        ? 'bg-gray-800 text-white'
        : 'text-gray-400 hover:bg-gray-800 hover:text-white'
}

const initials = computed(() => {

    const name = auth.user?.name || 'U'

    return name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
})

const logout = async () => {

    await auth.logout()

    router.push('/login')
}

</script>