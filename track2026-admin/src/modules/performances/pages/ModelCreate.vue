<template>
    <div class="mx-auto max-w-4xl space-y-6">

        <!-- Header -->
        <div>

            <h1 class="text-3xl font-bold text-white">
                Crear Modelo
            </h1>

            <p class="text-sm text-gray-400">
                Registro completo de modelo en el sistema
            </p>

        </div>

        <!-- Formulario -->
        <PerformanceSection
            v-for="section in fields"
            :key="section.id"
            :section="section"
            :form="form"
            :platforms="platforms"
            :errors="errors"
        />

        <!-- Acciones -->
        <PerformanceActions
            :loading="loading"
            @save="save"
            @cancel="goBack"
        />

        <!-- Error general -->
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

import PerformanceActions from '@/modules/performances/components/form/PerformanceActions.vue'
import PerformanceSection from '@/modules/performances/components/form/PerformanceSection.vue'

import { usePerformanceForm } from '@/modules/performances/composables/usePerformanceForm'

const router = useRouter()

const {

    form,
    fields,
    platforms,

    loading,

    error,
    errors,

    create

} = usePerformanceForm()

const goBack = () => {

    router.push('/performances')

}

const save = async () => {

    const performance = await create()

    if (!performance) {
        return
    }

    goBack()

}

</script>