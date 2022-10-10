<template>
  <DefaultField
    :field="currentField"
    :errors="errors"
    :show-help-text="showHelpText"
  >
    <template #field>
      <!-- Select Input Field -->
      <MutilSelectControl
        :id="currentField.uniqueKey"
        :dusk="field.attribute"
        v-model:selected="value"
        @change="handleChange"
        class="w-full"
        :class="errorClasses"
        :options="currentField.options"
        :disabled="isReadonly"
      >
        <option value="" :selected="!hasValue" :disabled="!field.nullable">
          {{ placeholder }}
        </option>
      </MutilSelectControl>
    </template>
  </DefaultField>
</template>

<script>
import find from 'lodash/find'
import { DependentFormField, HandlesValidationErrors } from '@/mixins'

export default {
  mixins: [HandlesValidationErrors, DependentFormField],

  data: () => ({
    value: [],
    selectedOption: [],
    search: '',
  }),

  created() {
    if (this.field.value && this.isSearchable) {
      this.selectedOption = find(
        this.field.options ?? [],
        v => this.field.value.indexOf(v.value) >= 0
      )
    }
  },

  methods: {
    /*
     * Set the initial value for the field
     */
    setInitialValue() {
      this.value = !(
        this.currentField.value === undefined ||
        this.currentField.value === null ||
        this.currentField.value === ''
      )
        ? this.currentField.value
        : this.value
    },

    /**
     * Provide a function that fills a passed FormData object with the
     * field's internal value attribute. Here we are forcing there to be a
     * value sent to the server instead of the default behavior of
     * `this.value || ''` to avoid loose-comparison issues if the keys
     * are truthy or falsey
     */
    fill(formData) {
      this.fillIfVisible(
        formData,
        this.field.attribute,
        JSON.stringify(this.value)
      )
    },

    /**
     * Set the search string to be used to filter the select field.
     */
    performSearch(event) {
      this.search = event
    },

    /**
     * Handle the selection change event.
     */
    handleChange(value) {
      this.value = value

      if (this.field) {
        this.emitFieldValueChange(this.field.attribute, this.value)
      }
    },
  },

  computed: {
    /**
     * Return the field options filtered by the search string.
     */
    filteredOptions() {
      let options = this.currentField.options || []

      return options.filter(option => {
        return (
          option.label.toLowerCase().indexOf(this.search.toLowerCase()) > -1
        )
      })
    },

    /**
     * Return the placeholder text for the field.
     */
    placeholder() {
      return this.currentField.placeholder || this.__('Choose an option')
    },

    /**
     * Return value has been setted.
     */
    hasValue() {
      return Boolean(
        !(this.value === undefined || this.value === null || this.value === '')
      )
    },
  },
}
</script>
