<template>
  <div class="flex flex-col" :class="{ 'md:flex-row': !stacked }">
    <slot />
  </div>
</template>
<script>
export default {
  props: {
    stacked: { type: Boolean, default: false },
  },
}
</script>
