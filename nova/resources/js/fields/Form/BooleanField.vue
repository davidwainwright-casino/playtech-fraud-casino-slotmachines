<template>
  <DefaultField
    :field="currentField"
    :errors="errors"
    :show-help-text="showHelpText"
  >
    <template #field>
      <Checkbox
        class="mt-2"
        @input="toggle"
        :id="currentField.uniqueKey"
        :name="field.name"
        :checked="checked"
        :disabled="currentlyIsReadonly"
      />
    </template>
  </DefaultField>
</template>

<script>
import { DependentFormField, HandlesValidationErrors } from '@/mixins'

export default {
  mixins: [HandlesValidationErrors, DependentFormField],

  data: () => ({
    value: false,
  }),

  methods: {
    /*
     * Set the initial value for the field
     */
    setInitialValue() {
      this.value = this.currentField.value || this.value
    },

    /**
     * Provide a function that fills a passed FormData object with the
     * field's internal value attribute
     */
    fill(formData) {
      this.fillIfVisible(formData, this.field.attribute, this.trueValue)
    },

    toggle() {
      this.value = !this.value

      if (this.field) {
        this.emitFieldValueChange(this.field.attribute, this.value)
      }
    },
  },

  computed: {
    checked() {
      return Boolean(this.value)
    },

    trueValue() {
      return +this.checked
    },
  },
}
</script>
