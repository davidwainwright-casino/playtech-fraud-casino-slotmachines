<template>
  <PanelItem :index="index" :field="field">
    <template #value>
      <div
        v-if="hasValue"
        :class="`text-${field.textAlign}`"
        class="leading-normal"
      >
        <component
          v-for="line in field.lines"
          :key="line.value"
          :class="line.classes"
          :is="`index-${line.component}`"
          :field="line"
          :resourceName="resourceName"
        />
      </div>
      <p v-else>&mdash;</p>
    </template>
  </PanelItem>
</template>

<script>
export default {
  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

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
