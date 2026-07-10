<template>
    <div class="mx-auto max-w-4xl space-y-6">

        <!-- HEADER -->

        <div>

            <h1 class="text-3xl font-bold text-white">
                Crear Modelo
            </h1>

            <p class="text-sm text-gray-400">
                Registro completo de modelo en el sistema
            </p>

        </div>

        <!-- FORM -->

        <PerformanceSection
            v-for="section in fields"
            :key="section.id"
            :section="section"
            :form="form"
            :platforms="platforms"
        />

        <!-- ACTIONS -->

        <PerformanceActions
            :loading="loading"
            @save="create"
            @cancel="router.push('/performances')"
        />

        <!-- ERROR -->

        <p
            v-if="error"
            class="text-sm text-red-400"
        >
            {{ error }}
        </p>

    </div>
</template>

<script setup>

import { useRouter } from 'vue-router'

import PerformanceSection
    from '../components/form/PerformanceSection.vue'

import PerformanceActions
    from '../components/form/PerformanceActions.vue'

import { usePerformanceForm }
    from '../composables/usePerformanceForm'

const router = useRouter()

const {

    form,

    fields,

    platforms,

    loading,

    error,

    create,

} = usePerformanceForm()

</script>