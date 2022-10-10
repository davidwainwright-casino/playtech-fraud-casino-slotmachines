<template>
  <DefaultField
    :field="currentField"
    :errors="errors"
    :show-help-text="showHelpText"
  >
    <template #field>
      <div class="space-y-2">
        <CheckboxWithLabel
          v-for="option in value"
          :key="option.name"
          :name="option.name"
          :checked="option.checked"
          @input="toggle($event, option)"
          :disabled="currentlyIsReadonly"
        >
          {{ option.label }}
        </CheckboxWithLabel>
      </div>
    </template>
  </DefaultField>
</template>

<script>
import find from 'lodash/find'
import isNil from 'lodash/isNil'
import fromPairs from 'lodash/fromPairs'
import map from 'lodash/map'
import { DependentFormField, HandlesValidationErrors } from '@/mixins'

export default {
  mixins: [HandlesValidationErrors, DependentFormField],

  data: () => ({
    value: [],
  }),

  methods: {
    /*
     * Set the initial value for the field
     */
    setInitialValue() {
      let values = this.currentField.value || {}

      this.value = map(this.currentField.options, o => {
        return {
          name: o.name,
          label: o.label,
          checked: values[o.name] || false,
        }
      })
    },

    /**
     * Provide a function that fills a passed FormData object with the
     * field's internal value attribute.
     */
    fill(formData) {
      this.fillIfVisible(
        formData,
        this.field.attribute,
        JSON.stringify(this.finalPayload)
      )
    },

    /**
     * Toggle the option's value.
     */
    toggle(event, option) {
      const firstOption = find(this.value, o => o.name == option.name)
      firstOption.checked = event.target.checked

      if (this.field) {
        this.emitFieldValueChange(
          this.field.attribute,
          JSON.stringify(this.finalPayload)
        )
      }
    },

    onSyncedField() {
      if (isNil(this.currentField.value)) {
        this.setInitialValue()
      }
    },
  },

  computed: {
    /**
     * Return the final filtered json object
     */
    finalPayload() {
      return fromPairs(map(this.value, o => [o.name, o.checked]))
    },
  },
}
</script>
