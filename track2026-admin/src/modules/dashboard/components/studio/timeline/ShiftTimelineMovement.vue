<template>

  <div
    class="
      min-w-0
      border-l
      border-gray-800
      pl-4
    "
  >

    <p
      class="
        text-base
        font-bold
      "
      :class="typeStyle.titleColor"
    >
      {{ item.title }}
    </p>

    <p
      v-if="item.description"
      class="
        truncate
        text-sm
        text-gray-300
      "
    >
      {{ item.description }}
    </p>

    <template v-if="item.type === 'earning'">

      <p
        v-if="item.original_currency === 'tokens'"
        class="
          mt-2
          text-sm
          font-semibold
          text-cyan-300
        "
      >
        {{ Number(item.tokens).toLocaleString('es-CO') }}
        Tokens
      </p>

      <p
        v-if="item.original_currency === 'tokens'"
        class="
          text-xs
          text-gray-500
        "
      >
        ≈ {{ formatAmount(item.amount) }}
      </p>

    </template>

    <p
      v-if="item.reason"
      class="
        mt-1
        truncate
        text-sm
        text-gray-400
      "
    >
      {{ item.reason }}
    </p>

  </div>

</template>

<script setup>

defineProps({

  item: {
    type: Object,
    required: true,
  },

  typeStyle: {
    type: Object,
    required: true,
  },

  formatAmount: {
    type: Function,
    required: true,
  },

})

</script>