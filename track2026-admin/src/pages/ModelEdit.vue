```vue
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

      <div class="flex items-center gap-4">

        <img
          v-if="form.profile_photo"
          :src="form.profile_photo"
          class="w-20 h-20 rounded-full object-cover border"
          @error="onImgError"
        />

        <div
          v-else
          class="w-20 h-20 rounded-full bg-gray-800 flex items-center justify-center text-white font-bold"
        >
          {{ initials }}
        </div>

        <input
          v-model="form.profile_photo"
          class="input"
          placeholder="Imagen URL"
        />

      </div>

      <input
        v-model="form.first_name"
        class="input"
        placeholder="Nombre"
      />

      <input
        v-model="form.last_name"
        class="input"
        placeholder="Apellido"
      />

      <input
        v-model="form.nickname"
        class="input"
        placeholder="Nickname"
      />

      <input
        v-model="form.email"
        class="input"
        placeholder="Email"
      />

      <input
        v-model="form.phone"
        class="input"
        placeholder="Teléfono"
      />

      <input
        v-model="form.country"
        class="input"
        placeholder="País"
      />

      <input
        v-model="form.city"
        class="input"
        placeholder="Ciudad"
      />

      <input
        v-model="form.address"
        class="input"
        placeholder="Dirección"
      />

      <div class="grid grid-cols-2 gap-4">

        <input
          v-model="form.document_type"
          class="input"
          placeholder="Tipo documento"
        />

        <input
          v-model="form.document_number"
          class="input"
          placeholder="Número documento"
        />

      </div>

      <input
        v-model="form.birth_date"
        type="date"
        class="input"
      />

      <div class="grid grid-cols-2 gap-4">

        <input
          v-model="form.studio_id"
          class="input"
          placeholder="Studio ID"
        />

        <input
          v-model="form.user_id"
          class="input"
          placeholder="User ID"
        />

      </div>

      <label class="text-white flex gap-2">

        <input
          type="checkbox"
          v-model="form.active"
        />

        Activa

      </label>

    </div>

    <div class="flex gap-3">

      <button
        @click="updateModel"
        :disabled="loading"
        class="w-full bg-blue-500 text-white py-3 rounded-xl"
      >
        {{ loading ? 'Guardando...' : 'Guardar' }}
      </button>

      <button
        @click="goBack"
        class="w-full bg-gray-700 text-white py-3 rounded-xl"
      >
        Cancelar
      </button>

    </div>

    <p
      v-if="error"
      class="text-red-400"
    >
      {{ error }}
    </p>

    <p
      v-if="success"
      class="text-green-400"
    >
      Cambios guardados correctamente
    </p>

  </div>
</template>

<script setup>

import {
  ref,
  computed,
  onMounted
} from 'vue'

import {
  useRoute,
  useRouter
} from 'vue-router'

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

const loadModel = async () => {

  try {

    const { data } = await api.get(
      `/performances/${route.params.id}?t=${Date.now()}`
    )

    const model = data.data

    form.value = {
      first_name: model.first_name ?? '',
      last_name: model.last_name ?? '',
      nickname: model.nickname ?? '',
      email: model.email ?? '',
      phone: model.phone ?? '',
      country: model.country ?? '',
      city: model.city ?? '',
      address: model.address ?? '',
      document_type: model.document_type ?? '',
      document_number: model.document_number ?? '',
      birth_date: model.birth_date ?? '',
      profile_photo: model.profile_photo ?? '',
      studio_id: model.studio_id ?? null,
      user_id: model.user_id ?? null,
      active: !!model.active
    }

  } catch (e) {

    console.error(e)

    error.value = 'Error cargando modelo'

  }

}

const updateModel = async () => {

  loading.value = true
  error.value = ''
  success.value = false

  try {

    const payload = {
      ...form.value,
      active: form.value.active ? 1 : 0
    }

    await api.put(
      `/performances/${route.params.id}`,
      payload
    )

    await loadModel()

    success.value = true

  } catch (e) {

    console.error(e)

    error.value = 'Error actualizando modelo'

  } finally {

    loading.value = false

  }

}

const onImgError = (e) => {
  e.target.src = 'https://i.pravatar.cc/300?img=12'
}

const goBack = () => {
  router.push('/performances')
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
  outline: none;
}

.input:focus {
  border-color: #3b82f6;
}

</style>
