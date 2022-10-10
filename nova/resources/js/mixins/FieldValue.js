import filled from '../util/filled'

export default {
  computed: {
    fieldHasValue() {
      return filled(this.field.value)
    },

    usesCustomizedDisplay() {
      return this.field.usesCustomizedDisplay && filled(this.field.displayedAs)
    },

    fieldValue() {
      if (!this.usesCustomizedDisplay && !this.fieldHasValue) {
        return null
      }

      return String(this.field.displayedAs || this.field.value)
    },

    shouldDisplayAsHtml() {
      return this.field.asHtml
    },
  },
}
