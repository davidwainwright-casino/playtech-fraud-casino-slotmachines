<template>
  <input
    type="checkbox"
    class="checkbox"
    :disabled="disabled"
    :checked="checked"
    @change="handleChange"
    @click.stop
  />
</template>

<script setup>
const props = defineProps({
  disabled: {
    type: Boolean,
    default: false,
  },

  checked: {
    default: false,
  },
})

const emit = defineEmits(['input'])

const handleChange = e => {
  emit('input', e)
}
</script>
