<template>
  <div
    class="-mx-6 px-6 py-2 md:py-0 bg-gray-100 dark:bg-gray-700"
    :class="{ 'border-t border-gray-100 dark:border-gray-700': index !== 0 }"
  >
    <div class="w-full py-4">
      <slot name="value">
        <p v-if="fieldValue && !shouldDisplayAsHtml" class="text-90">
          {{ fieldValue }}
        </p>
        <div
          v-else-if="fieldValue && shouldDisplayAsHtml"
          v-html="field.value"
        ></div>
        <p v-else>&mdash;</p>
      </slot>
    </div>
  </div>
</template>

<script>
import filled from '@/util/filled'

export default {
  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

  computed: {
    fieldValue() {
      if (!filled(this.field.value)) {
        return false
      }

      return String(this.field.value)
    },

    shouldDisplayAsHtml() {
      return this.field.asHtml
    },
  },
}
</script>
