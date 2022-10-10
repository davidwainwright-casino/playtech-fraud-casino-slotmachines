<template>
  <div
    ref="menu"
    :style="styles"
    class="select-none overflow-hidden bg-white dark:bg-gray-900 shadow-lg rounded-lg border border-gray-200 dark:border-gray-700"
    :class="{ 'max-w-sm lg:max-w-lg': width == 'auto' }"
  >
    <slot />
  </div>
</template>

<script>
export default {
  props: {
    width: {
      default: 120,
    },
  },

  computed: {
    styles() {
      return {
        width: this.width === 'auto' ? 'auto' : `${this.width}px`,
      }
    },
  },
}
</script>
