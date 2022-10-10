<template>
  <BasicButton
    v-bind="{ ...$props, ...$attrs }"
    :component="component"
    ref="button"
    class="shadow relative bg-red-500 hover:bg-red-400 text-white"
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
