<template>
  <BasicButton
    v-bind="$attrs"
    component="button"
    class="focus:outline-none focus:ring rounded border-2 border-primary-300 dark:border-gray-500 hover:border-primary-500 active:border-primary-400 dark:hover:border-gray-400 dark:active:border-gray-300 bg-white dark:bg-transparent text-primary-500 dark:text-gray-400 px-3 h-9 inline-flex items-center font-bold"
  >
    <slot />
  </BasicButton>
</template>

<script>
export default {
  //
}
</script>
