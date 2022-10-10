import { CancelToken } from 'axios'
import debounce from 'lodash/debounce'
import forIn from 'lodash/forIn'
import get from 'lodash/get'
import identity from 'lodash/identity'
import isEmpty from 'lodash/isEmpty'
import isNil from 'lodash/isNil'
import pickBy from 'lodash/pickBy'
import FormField from './FormField'
import { mapProps } from './propTypes'

export default {
  extends: FormField,

  emits: ['field-shown', 'field-hidden'],

  props: {
    ...mapProps([
      'shownViaNewRelationModal',
      'field',
      'viaResource',
      'viaResourceId',
      'viaRelationship',
      'resourceName',
      'resourceId',
      'relatedResourceName',
      'relatedResourceId',
    ]),

    syncEndpoint: { type: String, required: false },
  },

  data: () => ({
    dependentFieldDebouncer: null,
    canceller: null,
    watchedFields: {},
    watchedEvents: {},
    syncedField: null,
    pivot: false,
    editMode: 'create',
  }),

  created() {
    this.dependentFieldDebouncer = debounce(callback => callback(), 50)
  },

  mounted() {
    if (this.relatedResourceName !== '' && !isNil(this.relatedResourceName)) {
      this.pivot = true

      if (this.relatedResourceId !== '' && !isNil(this.relatedResourceId)) {
        this.editMode = 'update-attached'
      } else {
        this.editMode = 'attach'
      }
    } else {
      if (this.resourceId !== '' && !isNil(this.resourceId)) {
        this.editMode = 'update'
      }
    }

    if (!isEmpty(this.dependsOn)) {
      forIn(this.dependsOn, (defaultValue, dependsOn) => {
        this.watchedEvents[dependsOn] = value => {
          this.watchedFields[dependsOn] = value

          this.dependentFieldDebouncer(() => this.syncField())
        }

        this.watchedFields[dependsOn] = defaultValue

        Nova.$on(
          this.getFieldAttributeChangeEventName(dependsOn),
          this.watchedEvents[dependsOn]
        )
      })
    }
  },

  beforeUnmount() {
    if (!isEmpty(this.watchedEvents)) {
      forIn(this.watchedEvents, (event, dependsOn) => {
        Nova.$off(this.getFieldAttributeChangeEventName(event.dependsOn), event)
      })
    }
  },

  methods: {
    /*
     * Set the initial value for the field
     */
    setInitialValue() {
      this.value = !(
        this.currentField.value === undefined ||
        this.currentField.value === null
      )
        ? this.currentField.value
        : this.value
    },

    /**
     * Provide a function to fills FormData when field is visible.
     */
    fillIfVisible(formData, attribute, value) {
      if (this.currentlyIsVisible) {
        formData.append(attribute, value)
      }
    },

    syncField() {
      if (this.canceller !== null) this.canceller()

      Nova.request()
        .patch(
          this.syncEndpoint || this.syncFieldEndpoint,
          this.watchedFields,
          {
            params: pickBy(
              {
                editing: true,
                editMode: this.editMode,
                viaResource: this.viaResource,
                viaResourceId: this.viaResourceId,
                viaRelationship: this.viaRelationship,
                field: this.field.attribute,
                component: this.field.dependentComponentKey,
              },
              identity
            ),
            cancelToken: new CancelToken(canceller => {
              this.canceller = canceller
            }),
          }
        )
        .then(response => {
          let wasVisible = this.currentlyIsVisible

          this.syncedField = response.data

          if (this.syncedField.visible !== wasVisible) {
            this.$emit(
              this.syncedField.visible === true
                ? 'field-shown'
                : 'field-hidden',
              this.field.attribute
            )
          }

          if (isNil(this.syncedField.value)) {
            this.syncedField.value = this.field.value
          } else {
            this.setInitialValue()
          }

          this.onSyncedField()
        })
    },

    onSyncedField() {
      //
    },
  },

  computed: {
    /**
     * Determine if the field is in readonly mode
     */
    currentField() {
      return this.syncedField || this.field
    },

    /**
     * Determine if the field is in visible mode
     */
    currentlyIsVisible() {
      return this.currentField.visible
    },

    /**
     * Determine if the field is in readonly mode
     */
    currentlyIsReadonly() {
      if (this.syncedField !== null) {
        return Boolean(
          this.syncedField.readonly ||
            get(this.syncedField, 'extraAttributes.readonly')
        )
      }

      return Boolean(
        this.field.readonly || get(this.field, 'extraAttributes.readonly')
      )
    },

    dependsOn() {
      return this.field.dependsOn || []
    },

    syncFieldEndpoint() {
      if (this.editMode === 'update-attached') {
        return `/nova-api/${this.resourceName}/${this.resourceId}/update-pivot-fields/${this.relatedResourceName}/${this.relatedResourceId}`
      } else if (this.editMode == 'attach') {
        return `/nova-api/${this.resourceName}/${this.resourceId}/creation-pivot-fields/${this.relatedResourceName}`
      } else if (this.editMode === 'update') {
        return `/nova-api/${this.resourceName}/${this.resourceId}/update-fields`
      }

      return `/nova-api/${this.resourceName}/creation-fields`
    },
  },
}
