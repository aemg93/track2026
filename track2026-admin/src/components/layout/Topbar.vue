<template>
    <header class="h-16 bg-gray-900 border-b border-gray-800 flex items-center justify-between px-6">

        <!-- LEFT -->
        <div class="flex items-center gap-3">

            <h2 class="text-white font-semibold">
                {{ title }}
            </h2>

            <span class="text-xs text-gray-500">
                v1.0
            </span>

        </div>

        <!-- RIGHT -->
        <div class="flex items-center gap-4">

            <!-- STATUS -->
            <div class="flex items-center gap-2 text-xs text-green-400">
                <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                Online
            </div>

            <!-- USER DROPDOWN -->
            <div class="relative" @click="toggle">

                <button class="flex items-center gap-3 bg-gray-800 px-3 py-2 rounded-xl hover:bg-gray-700 transition">

                    <!-- AVATAR -->
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center font-bold text-sm">
                        {{ initials }}
                    </div>

                    <!-- INFO -->
                    <div class="text-left hidden sm:block">
                        <div class="text-white text-sm leading-4">
                            {{ user?.name || 'Usuario' }}
                        </div>
                        <div class="text-gray-400 text-xs">
                            {{ role }}
                        </div>
                    </div>

                </button>

                <!-- DROPDOWN -->
                <div
                    v-if="open"
                    class="absolute right-0 mt-2 w-48 bg-gray-900 border border-gray-800 rounded-xl shadow-lg overflow-hidden"
                >

                    <div class="p-3 border-b border-gray-800">
                        <p class="text-white text-sm">{{ user?.email }}</p>
                        <p class="text-gray-500 text-xs">{{ role }}</p>
                    </div>

                    <button
                        @click="logout"
                        class="w-full text-left px-4 py-3 text-red-400 hover:bg-gray-800"
                    >
                        Cerrar sesión
                    </button>

                </div>

            </div>

        </div>

    </header>
</template>

<script setup>

import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const props = defineProps({
    title: {
        type: String,
        default: 'Admin Panel'
    }
})

const auth = useAuthStore()
const router = useRouter()

const open = ref(false)

const user = computed(() => auth.user)
const role = computed(() => auth.roles?.[0] || 'user')

const initials = computed(() => {
    return auth.user?.name?.charAt(0)?.toUpperCase() || 'U'
})

const toggle = () => {
    open.value = !open.value
}

const logout = async () => {
    await auth.logout()
    router.push('/login')
}
</script>