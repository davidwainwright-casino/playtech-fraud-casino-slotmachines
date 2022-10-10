<template>
  <DefaultField
    :field="currentField"
    :errors="errors"
    :show-help-text="showHelpText"
  >
    <template #field>
      <input
        :id="currentField.uniqueKey"
        :type="inputType"
        :min="inputMin"
        :max="inputMax"
        :step="inputStep"
        v-model="value"
        class="w-full form-control form-input form-input-bordered"
        :class="errorClasses"
        :placeholder="field.name"
      />
    </template>
  </DefaultField>
</template>

<script>
import { DependentFormField, HandlesValidationErrors } from '@/mixins'

export default {
  mixins: [HandlesValidationErrors, DependentFormField],

  computed: {
    inputType() {
      return this.currentField.type || 'text'
    },

    inputStep() {
      return this.currentField.step
    },

    inputMin() {
      return this.currentField.min
    },

    inputMax() {
      return this.currentField.max
    },
  },
}
</script>
