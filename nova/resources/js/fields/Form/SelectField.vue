<template>
  <DefaultField
    :field="currentField"
    :errors="errors"
    :show-help-text="showHelpText"
  >
    <template #field>
      <!-- Search Input -->
      <SearchInput
        v-if="!currentlyIsReadonly && isSearchable"
        :data-testid="`${field.attribute}-search-input`"
        @input="performSearch"
        @clear="clearSelection"
        @selected="selectOption"
        :error="hasError"
        :value="selectedOption"
        :data="filteredOptions"
        :clearable="currentField.nullable"
        trackBy="value"
        class="w-full"
        :mode="mode"
      >
        <!-- The Selected Option Slot -->
        <div v-if="selectedOption" class="flex items-center">
          {{ selectedOption.label }}
        </div>

        <template #option="{ selected, option }">
          <!-- Options List Slot -->
          <div
            class="flex items-center text-sm font-semibold leading-5 text-90"
            :class="{ 'text-white': selected }"
          >
            {{ option.label }}
          </div>
        </template>
      </SearchInput>

      <!-- Select Input Field -->
      <SelectControl
        v-else
        :id="field.attribute"
        :dusk="field.attribute"
        v-model:selected="value"
        @change="handleChange"
        class="w-full"
        :select-classes="{ 'form-input-border-error': hasError }"
        :options="currentField.options"
        :disabled="currentlyIsReadonly"
      >
        <option value="" selected :disabled="!currentField.nullable">
          {{ placeholder }}
        </option>
      </SelectControl>
    </template>
  </DefaultField>
</template>

<script>
import find from 'lodash/find'
import first from 'lodash/first'
import isNil from 'lodash/isNil'
import { DependentFormField, HandlesValidationErrors } from '@/mixins'

export default {
  mixins: [HandlesValidationErrors, DependentFormField],

  data: () => ({
    search: '',
    selectedOption: null,
    value: null,
  }),

  created() {
    if (this.field.value) {
      let selectedOption = find(
        this.field.options,
        v => v.value == this.field.value
      )

      this.$nextTick(() => {
        this.selectOption(selectedOption)
      })
    }
  },

  methods: {
    /**
     * Provide a function that fills a passed FormData object with the
     * field's internal value attribute. Here we are forcing there to be a
     * value sent to the server instead of the default behavior of
     * `this.value || ''` to avoid loose-comparison issues if the keys
     * are truthy or falsey
     */
    fill(formData) {
      this.fillIfVisible(formData, this.field.attribute, this.value ?? '')
    },

    /**
     * Set the search string to be used to filter the select field.
     */
    performSearch(event) {
      this.search = event
    },

    /**
     * Clear the current selection for the field.
     */
    clearSelection() {
      this.selectedOption = ''
      this.value = ''

      if (this.field) {
        this.emitFieldValueChange(this.field.attribute, this.value)
      }
    },

    /**
     * Select the given option.
     */
    selectOption(option) {
      if (isNil(option)) {
        this.clearSelection()
        return
      }

      this.selectedOption = option
      this.value = option.value

      if (this.field) {
        this.emitFieldValueChange(this.field.attribute, this.value)
      }
    },

    /**
     * Handle the selection change event.
     */
    handleChange(value) {
      let selectedOption = find(
        this.currentField.options,
        v => v.value == value
      )

      this.selectOption(selectedOption)
    },

    /**
     * Handle on synced field.
     */
    onSyncedField() {
      let currentSelectedOption = null
      let hasValue = false

      if (this.selectedOption) {
        hasValue = true
        currentSelectedOption = find(
          this.currentField.options,
          v => v.value == this.selectedOption.value
        )
      }

      let selectedOption = find(
        this.currentField.options,
        v => v.value == this.currentField.value
      )

      if (isNil(currentSelectedOption)) {
        this.clearSelection()

        if (this.currentField.value) {
          this.selectOption(selectedOption)
        } else if (hasValue && !this.currentField.nullable) {
          this.selectOption(first(this.currentField.options))
        }

        return
      } else if (
        currentSelectedOption &&
        selectedOption &&
        ['create', 'attach'].includes(this.editMode)
      ) {
        this.selectOption(selectedOption)

        return
      }

      this.selectOption(currentSelectedOption)
    },
  },

  computed: {
    /**
     * Determine if the related resources is searchable
     */
    isSearchable() {
      return this.currentField.searchable
    },

    /**
     * Return the field options filtered by the search string.
     */
    filteredOptions() {
      return this.currentField.options.filter(option => {
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
