<template>
  <div
    class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-gray-700 bg-gray-900/50 py-20 text-center"
  >
    <div
      class="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-gray-800"
    >
      <svg
        class="h-10 w-10 text-gray-500"
        fill="none"
        stroke="currentColor"
        stroke-width="1.5"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.964 0a9 9 0 10-11.964 0"
        />
      </svg>
    </div>

    <h3 class="text-xl font-semibold text-white">
      Ningún turno seleccionado
    </h3>

    <p class="mt-2 max-w-md text-gray-400">
      Selecciona un turno activo desde la lista para visualizar su
      información y registrar operaciones financieras.
    </p>
  </div>
</template>

<script setup>
defineOptions({
  name: 'ShiftEmptyState',
})
</script>