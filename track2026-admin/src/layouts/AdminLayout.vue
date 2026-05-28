<template>

    <div class="flex h-screen bg-black">

        <!-- SIDEBAR -->
        <Sidebar />

        <!-- MAIN -->
        <div class="flex-1 flex flex-col">

            <!-- TOPBAR -->
            <Topbar
                title="Admin Panel"
                subtitle="Sistema SaaS v1"
            />

            <!-- CONTENT -->
            <main class="flex-1 p-6 overflow-y-auto">

                <router-view />

            </main>

        </div>

    </div>

</template>

<script setup>

import Sidebar from '../components/layout/Sidebar.vue'
import Topbar from '../components/layout/Topbar.vue'

</script>