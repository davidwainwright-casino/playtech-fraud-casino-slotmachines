<template>
  <div :class="`text-${field.textAlign}`">
    <template v-if="hasValue">
      <div class="leading-normal">
        <component
          :key="line.value"
          v-for="line in field.lines"
          class="whitespace-nowrap"
          :is="`index-${line.component}`"
          :field="line"
          :resourceName="resourceName"
        />
      </div>
    </template>
    <p v-else>&mdash;</p>
  </div>
</template>

<script>
export default {
  props: ['resourceName', 'field'],

  computed: {
    /**
     * Determine if the field has a value other than null.
     */
    hasValue() {
      return this.field.lines
    },
  },
}
</script>
