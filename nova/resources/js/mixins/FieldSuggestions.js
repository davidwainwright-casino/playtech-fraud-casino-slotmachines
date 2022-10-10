import isNil from 'lodash/isNil'
import omitBy from 'lodash/omitBy'

export default {
  computed: {
    suggestionsId() {
      return `${this.field.attribute}-list`
    },

    suggestions() {
      let field = !isNil(this.syncedField) ? this.syncedField : this.field

      if (isNil(field.suggestions)) {
        return []
      }

      return field.suggestions
    },

    suggestionsAttributes() {
      return {
        ...omitBy(
          {
            list: this.suggestions.length > 0 ? this.suggestionsId : null,
          },
          isNil
        ),
      }
    },
  },
}
