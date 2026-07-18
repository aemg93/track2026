<template>

  <div
    class="
      flex
      h-14
      w-14
      items-center
      justify-center
      rounded-xl
      shadow-inner
    "
    :class="typeStyle.background"
  >

    <component
      :is="typeStyle.icon"
      class="
        h-9
        w-9
        drop-shadow-lg
        stroke-[2.5]
      "
      :class="typeStyle.iconColor"
    />

  </div>

</template>


<script setup>

defineProps({

  typeStyle:{
    type:Object,
    required:true,
  }

})

</script>