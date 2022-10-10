<template>
  <BasicButton
    v-bind="{ ...$props, ...$attrs }"
    :component="component"
    ref="button"
    class="shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900"
  >
    <slot />
  </BasicButton>
</template>

<script>
export default {
  props: {
    size: {
      type: String,
      default: 'lg',
    },

    align: {
      type: String,
      default: 'center',
      validator: v => ['left', 'center'].includes(v),
    },

    component: {
      type: String,
      default: 'button',
    },
  },

  methods: {
    focus() {
      this.$refs.button.focus()
    },
  },
}
</script>
