<template>
  <component
    v-bind="{ ...$props, ...$attrs }"
    :is="component"
    ref="button"
    class="cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600"
    :class="{
      'inline-flex items-center justify-center': align == 'center',
      'inline-flex items-center justify-start': align == 'left',
      'h-9 px-3': size == 'lg',
      'h-8 px-3': size == 'sm',
      'h-7 px-1 md:px-3': size == 'xs',
    }"
  >
    <slot />
  </component>
</template>

<script setup>
import { ref } from 'vue'

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

const button = ref(null)

const focus = () => button.value.focus()

defineExpose({ focus })
</script>
