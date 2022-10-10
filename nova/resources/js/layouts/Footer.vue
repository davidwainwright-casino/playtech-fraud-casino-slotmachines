<template>
  <div
    class="mt-8 leading-normal text-xs text-gray-500 space-y-1"
    v-html="footer"
  />
</template>

<script>
export default {
  computed: {
    footer() {
      return window.Nova.config('footer')
    },
  },
}
</script>
