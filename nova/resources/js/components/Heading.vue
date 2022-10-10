<template>
  <component :is="component" :class="classes">
    <slot />
  </component>
</template>

<script>
const classes = {
  1: 'text-90 font-normal text-xl md:text-2xl',
  2: 'text-90 font-normal md:text-xl',
  3: 'text-90 uppercase tracking-wide font-bold md:text-sm',
  4: 'text-90 font-normal md:text-2xl',
}

export default {
  props: {
    level: {
      default: 1,
      type: Number,
    },
  },

  computed: {
    component() {
      return 'h' + this.level
    },
    classes() {
      return classes[this.level]
    },
  },
}
</script>
