<template>
  <div class="toolbar-button flex items-center cursor-pointer select-none">
    <slot />

    <IconArrow v-if="showArrow" class="ml-2" :class="arrowClass" />
  </div>
</template>

<script>
export default {
  props: {
    showArrow: {
      type: Boolean,
      default: true,
    },

    arrowClass: {
      type: String,
      default: null,
    },
  },
}
</script>
