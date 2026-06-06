<template>
  <div class="space-y-6">

    <div>
      <h1 class="text-3xl font-bold text-white">
        Editar Modelo
      </h1>

      <p class="text-gray-400 text-sm">
        {{ form.first_name }} {{ form.last_name }}
      </p>
    </div>

    <div class="bg-gray-900 p-6 rounded-2xl border border-gray-800 space-y-4">

      <!-- AVATAR -->
      <div class="flex items-center gap-4">

        <img
          v-if="form.profile_photo"
          :src="form.profile_photo"
          class="w-20 h-20 rounded-full object-cover border"
          @error="onImgError"
        />

        <div v-else class="w-20 h-20 rounded-full bg-gray-800 flex items-center justify-center text-white font-bold">
          {{ initials }}
        </div>

        <input v-model="form.profile_photo" class="input" placeholder="Imagen URL" />
      </div>

      <input v-model="form.first_name" class="input" placeholder="Nombre" />
      <input v-model="form.last_name" class="input" placeholder="Apellido" />
      <input v-model="form.nickname" class="input" placeholder="Nickname" />
      <input v-model="form.email" class="input" placeholder="Email" />
      <input v-model="form.phone" class="input" placeholder="Teléfono" />

      <input v-model="form.country" class="input" placeholder="País" />
      <input v-model="form.city" class="input" placeholder="Ciudad" />
      <input v-model="form.address" class="input" placeholder="Dirección" />

      <div class="grid grid-cols-2 gap-4">
        <input v-model="form.document_type" class="input" />
        <input v-model="form.document_number" class="input" />
      </div>

      <input v-model="form.birth_date" type="date" class="input" />

      <div class="grid grid-cols-2 gap-4">
        <input v-model="form.studio_id" class="input" />
        <input v-model="form.user_id" class="input" />
      </div>

      <label class="text-white flex gap-2">
        <input type="checkbox" v-model="form.active" />
        Activa
      </label>

    </div>

    <div class="flex gap-3">

      <button @click="updateModel" class="w-full bg-blue-500 text-white py-3 rounded-xl">
        Guardar
      </button>

      <button @click="goBack" class="w-full bg-gray-700 text-white py-3 rounded-xl">
        Cancelar
      </button>

    </div>

    <p v-if="error" class="text-red-400">{{ error }}</p>
    <p v-if="success" class="text-green-400">OK</p>

  </div>
</template>

<!-- <script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api'

const route = useRoute()
const router = useRouter()

const error = ref('')
const success = ref(false)

const form = ref({
  first_name: '',
  last_name: '',
  nickname: '',
  email: '',
  phone: '',
  country: '',
  city: '',
  address: '',
  document_type: '',
  document_number: '',
  birth_date: '',
  profile_photo: '',
  studio_id: null,
  user_id: null,
  active: true
})

const initials = computed(() =>
  (form.value.first_name?.[0] || '') +
  (form.value.last_name?.[0] || '')
)

const loadModel = async () => {
  const { data } = await api.get(`/models/${route.params.id}?t=${Date.now()}`)
  const m = data.data

  form.value = { ...form.value, ...m }
}

const updateModel = async () => {
  try {
    await api.put(`/models/${route.params.id}`, {
      ...form.value,
      active: form.value.active ? 1 : 0
    })

    await loadModel()
    success.value = true

  } catch (e) {
    error.value = 'Error'
  }
}

const onImgError = (e) => {
  e.target.src = 'https://i.pravatar.cc/300'
}

const goBack = () => router.push('/models')

onMounted(loadModel)
</script> -->
<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api'

const route = useRoute()
const router = useRouter()

const loading = ref(false)
const error = ref('')
const success = ref(false)

const form = ref({
  first_name: '',
  last_name: '',
  nickname: '',
  email: '',
  phone: '',
  country: '',
  city: '',
  address: '',
  document_type: '',
  document_number: '',
  birth_date: '',
  profile_photo: '',
  studio_id: null,
  user_id: null,
  active: true
})

const initials = computed(() =>
  (form.value.first_name?.[0] || '') +
  (form.value.last_name?.[0] || '')
)

/*
|--------------------------------------------------------------------------
| DEBUG WATCHERS
|--------------------------------------------------------------------------
*/

watch(
  () => form.value.profile_photo,
  (value) => {
    console.log('🖼 PROFILE PHOTO CAMBIÓ:')
    console.log(value)
  }
)

watch(
  () => form.value.nickname,
  (value) => {
    console.log('🏷 NICKNAME CAMBIÓ:')
    console.log(value)
  }
)

watch(
  () => form.value.phone,
  (value) => {
    console.log('📞 PHONE CAMBIÓ:')
    console.log(value)
  }
)

watch(
  () => form.value.country,
  (value) => {
    console.log('🌎 COUNTRY CAMBIÓ:')
    console.log(value)
  }
)

/*
|--------------------------------------------------------------------------
| LOAD MODEL
|--------------------------------------------------------------------------
*/

const loadModel = async () => {

  try {

    console.log('========================')
    console.log('📥 CARGANDO MODELO')
    console.log('========================')

    const response = await api.get(
      `/models/${route.params.id}?t=${Date.now()}`
    )

    console.log('📦 RESPONSE COMPLETA:')
    console.log(response)

    console.log('📦 RESPONSE DATA:')
    console.log(response.data)

    const m = response.data.data

    console.log('📦 MODELO RECIBIDO:')
    console.log(m)

    form.value = {
      first_name: m.first_name ?? '',
      last_name: m.last_name ?? '',
      nickname: m.nickname ?? '',
      email: m.email ?? '',
      phone: m.phone ?? '',
      country: m.country ?? '',
      city: m.city ?? '',
      address: m.address ?? '',
      document_type: m.document_type ?? '',
      document_number: m.document_number ?? '',
      birth_date: m.birth_date ?? '',
      profile_photo: m.profile_photo ?? '',
      studio_id: m.studio_id ?? null,
      user_id: m.user_id ?? null,
      active: !!m.active
    }

    console.log('✅ FORM CARGADO:')
    console.log(JSON.parse(JSON.stringify(form.value)))

  } catch (e) {

    console.error('❌ ERROR LOAD MODEL')
    console.error(e)

    error.value = 'Error cargando modelo'
  }
}

/*
|--------------------------------------------------------------------------
| UPDATE
|--------------------------------------------------------------------------
*/

const updateModel = async () => {

  loading.value = true
  error.value = ''
  success.value = false

  try {

    console.log('========================')
    console.log('🚀 INICIANDO UPDATE')
    console.log('========================')

    console.log('📋 FORM ACTUAL:')
    console.log(JSON.parse(JSON.stringify(form.value)))

    const payload = {
      ...form.value,
      active: form.value.active ? 1 : 0
    }

    console.log('📤 PAYLOAD ENVIADO:')
    console.log(payload)

    console.log(
      '📤 PAYLOAD JSON:',
      JSON.stringify(payload, null, 2)
    )

    const response = await api.put(
      `/models/${route.params.id}`,
      payload
    )

    console.log('✅ RESPONSE UPDATE:')
    console.log(response)

    console.log('✅ RESPONSE DATA:')
    console.log(response.data)

    console.log('========================')
    console.log('🔄 RECARGANDO MODELO')
    console.log('========================')

    await loadModel()

    success.value = true

  } catch (e) {

    console.error('❌ ERROR UPDATE')
    console.error(e)

    if (e.response) {

      console.error('❌ RESPONSE ERROR')
      console.error(e.response.data)

    }

    error.value = 'Error actualizando modelo'

  } finally {

    loading.value = false
  }
}

/*
|--------------------------------------------------------------------------
| IMAGE ERROR
|--------------------------------------------------------------------------
*/

const onImgError = (e) => {

  console.log('❌ ERROR CARGANDO IMAGEN')

  e.target.src =
    'https://i.pravatar.cc/300?img=12'
}

/*
|--------------------------------------------------------------------------
| NAVIGATION
|--------------------------------------------------------------------------
*/

const goBack = () => {
  router.push('/models')
}

onMounted(loadModel)
</script>
<style scoped>
.input {
  width: 100%;
  padding: 12px;
  border-radius: 12px;
  background: #0b0f19;
  border: 1px solid #1f2937;
  color: white;
}
</style>