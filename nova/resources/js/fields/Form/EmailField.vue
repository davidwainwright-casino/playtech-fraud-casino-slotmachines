<template>
  <DefaultField
    :field="currentField"
    :errors="errors"
    :show-help-text="showHelpText"
  >
    <template #field>
      <input
        v-bind="extraAttributes"
        class="w-full form-control form-input form-input-bordered"
        @input="handleChange"
        :value="value"
        :id="currentField.uniqueKey"
        :dusk="field.attribute"
        :disabled="currentlyIsReadonly"
      />
    </template>
  </DefaultField>
</template>

<script>
import { DependentFormField, HandlesValidationErrors } from '@/mixins'

export default {
  mixins: [HandlesValidationErrors, DependentFormField],

  computed: {
    extraAttributes() {
      return {
        // Leave the default attributes even though we can now specify
        // whatever attributes we like because the old number field still
        // uses the old field attributes
        type: this.currentField.type || 'email',
        pattern: this.currentField.pattern,
        placeholder: this.currentField.placeholder || this.field.name,
        class: this.errorClasses,
        ...this.currentField.extraAttributes,
      }
    },
  },
}
</script>
