<template>
  <button
    type="button"
    @keydown.enter.prevent="$emit('click')"
    @click.prevent="$emit('click')"
    tabindex="0"
    class="cursor-pointer text-gray-500 inline-flex items-center"
  >
    <Icon type="trash" :solid="true" />
    <slot />
  </button>
</template>

<script>
export default {
  emits: ['click'],
}
</script>
