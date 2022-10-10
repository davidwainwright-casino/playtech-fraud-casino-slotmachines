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

      <datalist v-if="suggestions.length > 0" :id="suggestionsId">
        <option
          :key="suggestion"
          v-for="suggestion in suggestions"
          :value="suggestion"
        />
      </datalist>
    </template>
  </DefaultField>
</template>

<script>
import {
  DependentFormField,
  FieldSuggestions,
  HandlesValidationErrors,
} from '@/mixins'

export default {
  mixins: [DependentFormField, FieldSuggestions, HandlesValidationErrors],

  computed: {
    defaultAttributes() {
      return {
        type: this.currentField.type || 'text',
        placeholder: this.currentField.placeholder || this.field.name,
        class: this.errorClasses,
        min: this.currentField.min,
        max: this.currentField.max,
        step: this.currentField.step,
        pattern: this.currentField.pattern,

        ...this.suggestionsAttributes,
      }
    },

    extraAttributes() {
      const attrs = this.currentField.extraAttributes

      return {
        // Leave the default attributes even though we can now specify
        // whatever attributes we like because the old number field still
        // uses the old field attributes
        ...this.defaultAttributes,
        ...attrs,
      }
    },
  },
}
</script>
