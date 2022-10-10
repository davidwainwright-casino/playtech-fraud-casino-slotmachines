<template>
  <LinkButton
    v-bind="{ size, align, ...$props, ...$attrs }"
    type="button"
    :component="component"
  >
    <slot>
      {{ __('Cancel') }}
    </slot>
  </LinkButton>
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
}
</script>
