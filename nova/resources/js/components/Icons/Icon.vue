<template>
  <component
    class="inline-block"
    :is="iconName"
    role="presentation"
    :width="width"
    :height="height"
    :viewBox="viewBox"
  />
</template>

<script>
export default {
  props: {
    type: {
      type: String,
      default: 'delete',
    },
    solid: {
      type: Boolean,
      default: false,
    },
  },

  computed: {
    style() {
      return this.solid ? 'solid' : 'outline'
    },

    iconName() {
      return `heroicons-${this.style}-${this.type}`
    },

    viewBox() {
      return this.solid ? '0 0 20 20' : '0 0 24 24'
    },

    width() {
      return this.solid ? 20 : 24
    },

    height() {
      return this.solid ? 20 : 24
    },
  },
}
</script>
