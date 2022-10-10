<template>
  <div :class="`text-${field.textAlign}`">
    <span
      class="rounded inline-flex items-center justify-center border border-60"
      :style="{ borderRadius: '4px', padding: '2px' }"
    >
      <span
        class="block w-4 h-4"
        :style="{ borderRadius: '2px', backgroundColor: field.value }"
      />
    </span>
  </div>
</template>

<script>
export default {
  props: ['resourceName', 'field'],
}
</script>
