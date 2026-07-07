<template>
  <div class="space-y-8 text-white">

    <!-- HEADER -->
    <div>
      <h1 class="text-3xl font-bold">Settings</h1>
      <p class="text-gray-400">Configura tu perfil y preferencias</p>
    </div>

    <!-- PROFILE CARD -->
    <div class="p-6 rounded-3xl border border-gray-800 bg-gray-950">
      <h2 class="font-semibold mb-4">Perfil</h2>
      <div class="flex items-center space-x-4">
        <img
          class="w-20 h-20 rounded-full border border-gray-700"
          :src="user.profile_photo || '/default-avatar.png'"
          alt="Profile"
        />
        <div>
          <p class="font-semibold">{{ user.name }}</p>
          <p class="text-gray-400 text-sm">{{ user.email }}</p>
        </div>
      </div>
    </div>

    <!-- PREFERENCES -->
    <div class="p-6 rounded-3xl border border-gray-800 bg-gray-950">
      <h2 class="font-semibold mb-4">Preferencias</h2>
      <form class="space-y-4" @submit.prevent="savePreferences">
        <div>
          <label class="block text-sm text-gray-400">Tema</label>
          <select v-model="preferences.theme" class="w-full p-2 rounded bg-gray-800 border border-gray-700">
            <option value="dark">Dark</option>
            <option value="light">Light</option>
          </select>
        </div>

        <div>
          <label class="block text-sm text-gray-400">Idioma</label>
          <select v-model="preferences.language" class="w-full p-2 rounded bg-gray-800 border border-gray-700">
            <option value="es">Español</option>
            <option value="en">English</option>
          </select>
        </div>

        <button
          type="submit"
          class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 transition"
        >
          Guardar cambios
        </button>
      </form>
    </div>

    <!-- SECURITY -->
    <div class="p-6 rounded-3xl border border-gray-800 bg-gray-950">
      <h2 class="font-semibold mb-4">Seguridad</h2>
      <form class="space-y-4" @submit.prevent="updatePassword">
        <div>
          <label class="block text-sm text-gray-400">Nueva contraseña</label>
          <input
            type="password"
            v-model="security.newPassword"
            class="w-full p-2 rounded bg-gray-800 border border-gray-700"
          />
        </div>
        <button
          type="submit"
          class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 transition"
        >
          Actualizar contraseña
        </button>
      </form>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'

const user = ref({
  name: 'Camila Moreno',
  email: 'camila@example.com',
  profile_photo: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSUNHw2fxpjZ2Ap_BLZZHALi6asJEDoSTe3ioQvnwWzkpkWie3PbmybcRY&s=10'
})

const preferences = ref({
  theme: 'dark',
  language: 'es'
})

const security = ref({
  newPassword: ''
})

const savePreferences = () => {
  console.log('Preferencias guardadas:', preferences.value)
}

const updatePassword = () => {
  console.log('Nueva contraseña:', security.value.newPassword)
}
</script>
