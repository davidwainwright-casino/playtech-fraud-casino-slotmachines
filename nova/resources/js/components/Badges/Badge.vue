<template>
  <span
    class="inline-flex items-center whitespace-nowrap h-6 px-2 rounded-full uppercase text-xs font-bold"
    :class="extraClasses"
  >
    <slot name="icon" />
    <slot>
      {{ label }}
    </slot>
  </span>
</template>

<script>
export default {
  props: {
    label: {
      type: [Boolean, String],
      required: false,
    },

    extraClasses: {
      type: [Array, String],
      required: false,
    },
  },
}
</script>
