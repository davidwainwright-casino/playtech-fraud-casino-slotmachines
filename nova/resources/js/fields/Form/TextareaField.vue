<template>
  <DefaultField
    :field="currentField"
    :errors="errors"
    :full-width-content="true"
    :show-help-text="showHelpText"
  >
    <template #field>
      <textarea
        v-bind="extraAttributes"
        class="w-full form-control form-input form-input-bordered py-3 h-auto"
        :id="currentField.uniqueKey"
        :dusk="field.attribute"
        :value="value"
        @input="handleChange"
      />
    </template>
  </DefaultField>
</template>

<script>
import { DependentFormField, HandlesValidationErrors } from '@/mixins'

export default {
  mixins: [HandlesValidationErrors, DependentFormField],

  computed: {
    defaultAttributes() {
      return {
        rows: this.currentField.rows,
        class: this.errorClasses,
        placeholder: this.field.name,
      }
    },

    extraAttributes() {
      const attrs = this.currentField.extraAttributes

      return {
        ...this.defaultAttributes,
        ...attrs,
      }
    },
  },
}
</script>
