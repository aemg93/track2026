<template>
    <section class="relative overflow-hidden rounded-3xl border border-gray-800 bg-gradient-to-br from-gray-950 via-gray-900 to-black p-8">
        <div class="absolute top-0 right-0 h-96 w-96 rounded-full bg-blue-500/10 blur-3xl" />

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

            <div>
                <h1 class="text-4xl font-bold tracking-tight text-white">
                    Panel Financiero
                </h1>
            </div>

            <div class="flex flex-wrap gap-4">

                <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-5 py-3">
                    <p class="text-xs uppercase tracking-wider text-emerald-400">
                        Estado
                    </p>

                    <p class="mt-1 font-semibold text-white">
                        Operativo
                    </p>
                </div>
            </div>

        </div>
    </section>
</template>

<script setup>
defineProps({
    dashboard: Object,
    finance: Object
})
</script>