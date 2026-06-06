<template>

  <div
    class="flex items-center gap-2"
  >

    <!-- VIEW -->
    <button
      @click="$emit('view')"
      class="
        px-4 py-2
        rounded-xl
        bg-gray-800
        hover:bg-gray-700
        border border-gray-700
        text-gray-300
        text-sm
        font-medium
        transition
      "
    >
      Ver
    </button>

    <!-- EDIT -->
    <button
      @click="$emit('edit')"
      class="
        px-4 py-2
        rounded-xl
        bg-blue-500/10
        hover:bg-blue-500/20
        border border-blue-500/20
        text-blue-400
        text-sm
        font-medium
        transition
      "
    >
      Editar
    </button>

    <!-- DELETE -->
    <button
      @click="$emit('delete')"
      class="
        px-4 py-2
        rounded-xl
        bg-red-500/10
        hover:bg-red-500/20
        border border-red-500/20
        text-red-400
        text-sm
        font-medium
        transition
      "
    >
      Eliminar
    </button>

  </div>

</template>

<script setup>

defineEmits([
  'view',
  'edit',
  'delete'
])

</script>