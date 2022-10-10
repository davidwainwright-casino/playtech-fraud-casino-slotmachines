<template>
  <button
    @click="$emit('click')"
    type="button"
    class="rounded font-bold text-sm text-primary-500 hover:text-primary-400 focus:text-primary-400 active:text-primary-600 inline-flex items-center focus:outline-none focus:ring"
  >
    <Icon type="plus-circle" />
  </button>
</template>

<script>
export default {
  emits: ['click'],
}
</script>
