<template>
  <IconArrow
    class="transform"
    :class="{ 'ltr:-rotate-90 rtl:rotate-90': collapsed }"
  />
</template>

<script>
export default {
  props: {
    collapsed: {
      type: Boolean,
      default: false,
    },
  },
}
</script>
