<template>
  <Link
    v-bind="{ ...$props, ...$attrs }"
    class="focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring-2 rounded border-2 border-gray-200 dark:border-gray-500 hover:border-primary-500 active:border-primary-400 dark:hover:border-gray-400 dark:active:border-gray-300 bg-white dark:bg-transparent text-primary-500 dark:text-gray-400 px-3 h-9 inline-flex items-center font-bold"
  >
    <slot />
  </Link>
</template>

<script>
export default {
  //
}
</script>
