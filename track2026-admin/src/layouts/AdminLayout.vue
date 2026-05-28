<template>

    <div class="flex h-screen bg-black">

        <!-- SIDEBAR -->
        <Sidebar />

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

import Sidebar from '../components/layout/Sidebar.vue'

</script>