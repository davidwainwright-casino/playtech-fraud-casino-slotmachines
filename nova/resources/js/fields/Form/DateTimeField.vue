<template>
  <DefaultField
    :field="currentField"
    :errors="errors"
    :show-help-text="showHelpText"
  >
    <template #field>
      <div class="flex items-center">
        <input
          type="datetime-local"
          class="form-control form-input form-input-bordered"
          ref="dateTimePicker"
          :id="currentField.uniqueKey"
          :dusk="field.attribute"
          :name="field.name"
          :value="formattedDate"
          :class="errorClasses"
          :disabled="currentlyIsReadonly"
          @change="handleChange"
          :min="currentField.min"
          :max="currentField.max"
          :step="currentField.step"
        />

        <span class="ml-3">
          {{ timezone }}
        </span>
      </div>
    </template>
  </DefaultField>
</template>

<script>
import isNil from 'lodash/isNil'
import { DateTime } from 'luxon'
import { DependentFormField, HandlesValidationErrors } from '@/mixins'
import filled from '@/util/filled'

export default {
  mixins: [HandlesValidationErrors, DependentFormField],

  data: () => ({
    formattedDate: '',
  }),

  methods: {
    /*
     * Set the initial value for the field
     */
    setInitialValue() {
      if (!isNil(this.currentField.value)) {
        let isoDate = DateTime.fromISO(this.currentField.value || this.value, {
          zone: Nova.config('timezone'),
        })

        this.value = isoDate.toString()

        isoDate = isoDate.setZone(this.timezone)

        this.formattedDate = [
          isoDate.toISODate(),
          isoDate.toFormat(this.timeFormat),
        ].join('T')
      }
    },

    /**
     * On save, populate our form data
     */
    fill(formData) {
      this.fillIfVisible(formData, this.field.attribute, this.value || '')

      if (this.currentlyIsVisible && filled(this.value)) {
        let isoDate = DateTime.fromISO(this.value, { zone: this.timezone })

        this.formattedDate = [
          isoDate.toISODate(),
          isoDate.toFormat(this.timeFormat),
        ].join('T')
      }
    },

    /**
     * Update the field's internal value
     */
    handleChange(event) {
      let value = event?.target?.value ?? event

      if (filled(value)) {
        let isoDate = DateTime.fromISO(value, { zone: this.timezone })

        this.value = isoDate.setZone(Nova.config('timezone')).toString()
      } else {
        this.value = ''
      }

      if (this.field) {
        this.emitFieldValueChange(this.field.attribute, this.value)
      }
    },
  },

  computed: {
    timeFormat() {
      return this.currentField.step % 60 === 0 ? 'HH:mm' : 'HH:mm:ss'
    },

    timezone() {
      return Nova.config('userTimezone') || Nova.config('timezone')
    },
  },
}
</script>
