<template>
  <Checkbox
    :checked="checked"
    :disabled="true"
    class="pointer-events-none"
    :indeterminate="indeterminate"
  />
</template>

<script setup>
const props = defineProps({
  checked: { type: Boolean, default: false },
  indeterminate: { type: Boolean, default: false },
})
</script>
