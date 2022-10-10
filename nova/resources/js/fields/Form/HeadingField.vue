<template>
  <FieldWrapper
    class="bg-gray-100 dark:bg-gray-700"
    v-if="currentField.visible"
  >
    <div
      v-if="shouldDisplayAsHtml"
      v-html="currentField.value"
      :class="classes"
    />
    <div v-else :class="classes">
      <p>{{ currentField.value }}</p>
    </div>
  </FieldWrapper>
</template>

<script>
import { DependentFormField } from '@/mixins'

export default {
  mixins: [DependentFormField],

  props: {
    resourceName: {
      type: String,
      require: true,
    },
    field: {
      type: Object,
      require: true,
    },
  },

  created() {
    this.field.fill = () => {}
  },

  computed: {
    classes: () => [
      'remove-last-margin-bottom',
      'leading-normal',
      'w-full',
      'py-4',
      'px-8',
    ],

    shouldDisplayAsHtml() {
      return this.currentField.asHtml || false
    },
  },
}
</script>
