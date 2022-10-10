<template>
  <BasicButton
    v-bind="{ ...$props, ...$attrs }"
    :component="component"
    class="appearance-none bg-transparent font-bold text-gray-400 hover:text-gray-300 active:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 dark:active:text-gray-600 dark:hover:bg-gray-800"
  >
    <slot />
  </BasicButton>
</template>

<script setup>
const props = defineProps({
  size: {
    type: String,
    default: 'lg',
  },

  align: {
    type: String,
    default: 'center',
    validator: v => ['left', 'center'].includes(v),
  },

  component: {
    type: String,
    default: 'button',
  },
})
</script>
