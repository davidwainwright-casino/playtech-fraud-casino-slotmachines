<template>
  <Card class="relative">
    <div
      v-if="loading"
      class="rounded-lg flex items-center justify-center absolute inset-0 z-30"
      :class="modeClass"
    >
      <Loader class="text-gray-300" width="30" />
    </div>

    <slot />
  </Card>
</template>

<script>
export default {
  props: {
    loading: {
      type: Boolean,
      default: true,
    },

    mode: {
      type: String,
      default: 'light',
      validator: function (value) {
        return ['light', 'dark'].indexOf(value) !== -1
      },
    },
  },

  computed: {
    modeClass() {
      return this.mode == 'light' ? 'bg-white dark:bg-gray-800' : 'bg-90'
    },
  },
}
</script>
