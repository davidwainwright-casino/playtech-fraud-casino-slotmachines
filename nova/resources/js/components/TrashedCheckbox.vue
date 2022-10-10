<template>
  <div>
    <CheckboxWithLabel
      v-bind="{ ...$attrs }"
      :dusk="`${resourceName}-with-trashed-checkbox`"
      :checked="withTrashed"
      @input="toggleWithTrashed"
    >
      <slot>
        {{ __('With Trashed') }}
      </slot>
    </CheckboxWithLabel>
  </div>
</template>

<script>
export default {
  emits: ['input'],

  inheritAttrs: false,

  props: {
    resourceName: String,
    withTrashed: Boolean,
  },

  methods: {
    toggleWithTrashed() {
      this.$emit('input')
    },
  },
}
</script>
