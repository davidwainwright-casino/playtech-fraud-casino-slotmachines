<template>
  <Link
    v-bind="{ ...$props, ...$attrs }"
    class="shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold"
    :class="{
      'px-4 h-9 text-sm': size == 'md',
      'px-3 h-7 text-xs': size == 'sm',
    }"
  >
    <slot />
  </Link>
</template>

<script>
export default {
  props: {
    size: {
      type: String,
      default: 'md',
      validator: val => ['sm', 'md'].includes(val),
    },
  },
}
</script>
