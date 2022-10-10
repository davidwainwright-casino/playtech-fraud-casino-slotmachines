<template>
  <div v-bind="defaultAttributes">
    <slot></slot>
  </div>
</template>

<script>
const defaultClasses = 'px-3 py-2 text-sm leading-normal'

export default {
  props: {
    maxWidth: {
      default: 'auto',
    },
  },

  computed: {
    defaultAttributes() {
      return {
        class: this.$attrs.class || defaultClasses,
        style: {
          maxWidth:
            this.maxWidth === 'auto' ? this.maxWidth : `${this.maxWidth}px`,
        },
      }
    },
  },
}
</script>
