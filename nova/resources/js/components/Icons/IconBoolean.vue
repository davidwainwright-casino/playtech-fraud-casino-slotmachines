<template>
  <Icon
    v-if="value"
    :viewBox="viewBox"
    :width="width"
    :height="height"
    type="check-circle"
    class="text-green-500"
  />
  <Icon
    v-else-if="nullable && value == null"
    :viewBox="viewBox"
    :width="width"
    :height="height"
    type="minus-circle"
    class="text-gray-200 dark:text-gray-800"
  />
  <Icon
    v-else
    :viewBox="viewBox"
    :width="width"
    :height="height"
    type="x-circle"
    class="text-red-500"
  />
</template>

<script>
export default {
  props: {
    value: {
      type: Boolean,
      default: false,
    },

    viewBox: {
      default: '0 0 24 24',
    },

    height: {
      default: 24,
    },

    width: {
      default: 24,
    },

    nullable: {
      type: Boolean,
      default: false,
    },
  },
}
</script>
