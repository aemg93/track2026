<template>

    <div class="max-w-4xl mx-auto space-y-6">

        <!-- HEADER -->
        <div>
            <h1 class="text-3xl font-bold text-white">
                Crear Modelo
            </h1>
            <p class="text-gray-400 text-sm">
                Registro completo de modelo en el sistema
            </p>
        </div>

        <!-- FORM CARD -->
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 space-y-5">

            <!-- NOMBRE / APELLIDO -->
            <div class="grid grid-cols-2 gap-4">

                <input v-model="form.first_name" class="input" placeholder="Nombre *" />
                <input v-model="form.last_name" class="input" placeholder="Apellido *" />

            </div>

            <!-- NICKNAME -->
            <input v-model="form.nickname" class="input" placeholder="Nickname" />

            <!-- EMAIL -->
            <input v-model="form.email" type="email" class="input" placeholder="Email *" />

            <!-- TELEFONO -->
            <input v-model="form.phone" class="input" placeholder="Teléfono" />

            <!-- PAÍS / CIUDAD -->
            <div class="grid grid-cols-2 gap-4">

                <input v-model="form.country" class="input" placeholder="País" />
                <input v-model="form.city" class="input" placeholder="Ciudad" />

            </div>

            <!-- DIRECCIÓN -->
            <input v-model="form.address" class="input" placeholder="Dirección" />

            <!-- DOCUMENTO -->
            <div class="grid grid-cols-2 gap-4">

                <select v-model="form.document_type" class="input">
                    <option value="">Tipo de documento</option>
                    <option value="cc">Cédula de Ciudadanía</option>
                    <option value="ce">Cédula de Extranjería</option>
                    <option value="passport">Pasaporte</option>
                </select>

                <input v-model="form.document_number" class="input" placeholder="Número documento" />

            </div>

            <!-- FECHA NACIMIENTO -->
            <input v-model="form.birth_date" type="date" class="input" />

            <!-- PROFILE PHOTO -->
            <input v-model="form.profile_photo" class="input" placeholder="URL foto perfil" />

            <!-- STUDIO ID (REQUIRED) -->
            <input v-model="form.studio_id" type="number" class="input" placeholder="Studio ID *" />

            <!-- USER ID -->
            <input v-model="form.user_id" type="number" class="input" placeholder="User ID (opcional)" />

            <!-- HOURS / RANKING -->
            <div class="grid grid-cols-2 gap-4">

                <input v-model="form.hours_streamed" type="number" class="input" placeholder="Horas stream" />
                <input v-model="form.ranking_score" type="number" class="input" placeholder="Ranking score" />

            </div>

            <!-- ACTIVE -->
            <label class="flex items-center gap-3 text-white">
                <input type="checkbox" v-model="form.active" />
                Activa
            </label>

            <!-- ACTIONS -->
            <div class="flex gap-3 pt-2">

                <button
                    @click="createModel"
                    :disabled="loading"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 rounded-xl"
                >
                    {{ loading ? 'Creando...' : 'Crear Modelo' }}
                </button>

                <button
                    @click="router.push('/models')"
                    class="w-full bg-gray-700 hover:bg-gray-600 text-white py-3 rounded-xl"
                >
                    Cancelar
                </button>

            </div>

            <!-- ERROR -->
            <p v-if="error" class="text-red-400 text-sm">
                {{ error }}
            </p>

        </div>

    </div>

</template>

<script setup>

import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const router = useRouter()

const loading = ref(false)
const error = ref('')

const form = ref({

    // 🔴 REQUIRED
    studio_id: null,
    first_name: '',
    last_name: '',
    email: '',
    birth_date: '',

    // 🟡 OPTIONAL
    nickname: '',
    phone: '',
    country: '',
    city: '',
    address: '',
    document_type: '',
    document_number: '',
    profile_photo: '',
    user_id: null,

    // DEFAULTS
    active: true,
    hours_streamed: 0,
    ranking_score: 0

})

const createModel = async () => {

    loading.value = true
    error.value = ''

    try {

        const payload = {
            ...form.value,
            active: form.value.active ? 1 : 0
        }

        const { data } = await api.post('/models', payload)

        router.push(`/models/${data.data.id}`)

    } catch (e) {

        error.value =
            e?.response?.data?.message ||
            'Error creando modelo'

    } finally {
        loading.value = false
    }
}

</script>

<style scoped>
.input {
    width: 100%;
    padding: 12px;
    border-radius: 12px;
    background: #0b0f19;
    border: 1px solid #1f2937;
    color: white;
    outline: none;
}

.input:focus {
    border-color: #3b82f6;
}
</style>