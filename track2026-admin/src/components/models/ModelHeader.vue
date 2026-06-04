<template>
  <div>
    <h1 class="text-4xl font-bold text-white">
      {{ model.first_name }} {{ model.last_name }}
    </h1>

    <p class="text-gray-400">
       {{ model.email }}
    </p>
  </div>
</template>

<script setup>
defineProps({
  model: {
    type: Object,
    required: true
  }
})
</script>