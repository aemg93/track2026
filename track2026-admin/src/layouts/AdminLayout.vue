<template>

    <div class="flex h-screen bg-black">

        <!-- SIDEBAR -->
        <aside class="w-64 bg-gray-900 border-r border-gray-800 flex flex-col">

            <!-- BRAND -->
            <div class="p-6 border-b border-gray-800">

                <h1 class="text-white text-xl font-bold">
                    TrackModel
                </h1>

                <p class="text-gray-500 text-sm mt-1">
                    SaaS Admin
                </p>

            </div>

            <!-- NAV -->
            <nav class="flex-1 p-4 space-y-2">

                <router-link
                    v-for="item in menu"
                    :key="item.path"
                    :to="item.path"
                    class="block px-4 py-3 rounded-xl text-gray-400 hover:bg-gray-800 hover:text-white transition"
                    active-class="bg-gray-800 text-white"
                >

                    {{ item.name }}

                </router-link>

            </nav>

            <!-- USER -->
            <div class="p-4 border-t border-gray-800">

                <div class="text-white font-medium">
                    {{ user?.name || 'Admin' }}
                </div>

                <div class="text-gray-500 text-sm">
                    {{ user?.email }}
                </div>

                <button
                    @click="logout"
                    class="mt-4 w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-xl transition"
                >
                    Logout
                </button>

            </div>

        </aside>

        <!-- MAIN -->
        <div class="flex-1 flex flex-col">

            <!-- TOPBAR -->
            <header class="h-16 bg-gray-900 border-b border-gray-800 flex items-center justify-between px-6">

                <h2 class="text-white font-semibold">
                    Admin Panel
                </h2>

                <div class="text-gray-400 text-sm">
                    Sistema SaaS v1
                </div>

            </header>

            <!-- CONTENT -->
            <main class="flex-1 p-6 overflow-y-auto">

                <router-view />

            </main>

        </div>

    </div>

</template>

<script setup>

import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const user = computed(() => auth.user)

const menu = [

    {
        name: 'Dashboard',
        path: '/dashboard',
    },

    {
        name: 'Desempeño',
        path: '/performances',
    },

    {
        name: 'Ganancias',
        path: '/earnings',
    },

    {
        name: 'Bonos',
        path: '/bonuses',
    },

    {
        name: 'Penalizaciones',
        path: '/penalties',
    },

]

const logout = async () => {

    await auth.logout()

    router.push('/login')
}

</script>

<style scoped>
</style>