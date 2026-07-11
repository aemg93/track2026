<template>
    <div class="mx-auto max-w-4xl space-y-6">

        <!-- Header -->
        <header>

            <h1 class="text-3xl font-bold text-white">
                Editar Modelo
            </h1>

            <p class="text-sm text-gray-400">
                {{ form.first_name }}
                {{ form.last_name }}
            </p>

        </header>

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
            :loading="saving"
            @save="save"
            @cancel="goBack"
        />

        <!-- Error -->
        <p
            v-if="error"
            class="text-sm text-red-400"
        >
            {{ error }}
        </p>

    </div>
</template>

<script setup>

import { onMounted } from 'vue'

import { useRoute, useRouter } from 'vue-router'

import PerformanceActions from '../components/form/PerformanceActions.vue'
import PerformanceSection from '../components/form/PerformanceSection.vue'

import { usePerformanceForm } from '../composables/usePerformanceForm'

const router = useRouter()
const route = useRoute()

const id = Number(route.params.id)

const {

    form,
    fields,
    platforms,

    saving,

    error,
    errors,

    load,
    update

} = usePerformanceForm()

const goBack = () => {

    router.push('/performances')

}

const save = async () => {

    const performance = await update(id)

    if (!performance) {
        return
    }

    goBack()

}

onMounted(() => {

    load(id)

})

</script>