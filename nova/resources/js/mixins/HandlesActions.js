import each from 'lodash/each'
import filter from 'lodash/filter'
import find from 'lodash/find'
import isNil from 'lodash/isNil'
import tap from 'lodash/tap'
import { Errors } from 'form-backend-validation'
import { mapActions } from 'vuex'

export default {
  emits: ['actionExecuted'],

  props: {
    resourceName: String,

    actions: {},

    pivotActions: {
      default: () => [],
    },

    endpoint: {
      type: String,
      default: null,
    },
  },

  data: () => ({
    working: false,
    selectedActionKey: '',
    errors: new Errors(),
    confirmActionModalOpened: false,
  }),

  methods: {
    ...mapActions(['fetchPolicies']),

    handleSelectionChange(event) {
      this.selectedActionKey = event
      this.determineActionStrategy()
      this.$refs.selectControl.resetSelection()
    },

    /**
     * Determine whether the action should redirect or open a confirmation modal
     */
    determineActionStrategy() {
      if (this.selectedAction.withoutConfirmation) {
        this.executeAction()
      } else {
        this.openConfirmationModal()
      }
    },

    /**
     * Confirm with the user that they actually want to run the selected action.
     */
    openConfirmationModal() {
      this.confirmActionModalOpened = true
    },

    /**
     * Close the action confirmation modal.
     */
    closeConfirmationModal() {
      this.confirmActionModalOpened = false
      this.errors = new Errors()
    },

    /**
     * Close the action response modal.
     */
    closeActionResponseModal() {
      this.showActionResponseModal = false
    },

    /**
     * Initialize all of the action fields to empty strings.
     */
    initializeActionFields() {
      each(this.allActions, action => {
        each(action.fields, field => {
          field.fill = () => ''
        })
      })
    },

    /**
     * Execute the selected action.
     */
    executeAction() {
      this.working = true
      Nova.$progress.start()

      let responseType = this.selectedAction.responseType ?? 'json'

      Nova.request({
        method: 'post',
        url: this.endpoint || `/nova-api/${this.resourceName}/action`,
        params: this.actionRequestQueryString,
        data: this.actionFormData(),
        responseType,
      })
        .then(async response => {
          this.confirmActionModalOpened = false
          await this.fetchPolicies()

          this.handleActionResponse(response.data, response.headers)

          this.working = false
          Nova.$progress.done()
          this.$refs.selectControl.selectedIndex = 0
        })
        .catch(error => {
          this.working = false
          Nova.$progress.done()

          if (error.response && error.response.status == 422) {
            if (responseType === 'blob') {
              error.response.data.text().then(data => {
                this.errors = new Errors(JSON.parse(data).errors)
              })
            } else {
              this.errors = new Errors(error.response.data.errors)
            }

            Nova.error(this.__('There was a problem executing the action.'))
          }
        })
    },

    /**
     * Gather the action FormData for the given action.
     */
    actionFormData() {
      return tap(new FormData(), formData => {
        formData.append('resources', this.selectedResources)

        each(this.selectedAction.fields, field => {
          field.fill(formData)
        })
      })
    },

    emitResponseCallback(callback) {
      this.$emit('actionExecuted')
      Nova.$emit('action-executed')

      if (typeof callback === 'function') {
        callback()
      }
    },

    /**
     * Handle the action response. Typically either a message, download or a redirect.
     */
    handleActionResponse(data, headers) {
      let contentDisposition = headers['content-disposition']

      if (
        data instanceof Blob &&
        isNil(contentDisposition) &&
        data.type === 'application/json'
      ) {
        data.text().then(jsonStringData => {
          this.handleActionResponse(JSON.parse(jsonStringData), headers)
        })

        return
      }

      if (data instanceof Blob) {
        this.emitResponseCallback(() => {
          let fileName = 'unknown'
          let url = window.URL.createObjectURL(new Blob([data]))
          let link = document.createElement('a')
          link.href = url

          if (contentDisposition) {
            let fileNameMatch = contentDisposition.match(/filename="(.+)"/)
            if (fileNameMatch.length === 2) fileName = fileNameMatch[1]
          }

          link.setAttribute('download', fileName)
          document.body.appendChild(link)
          link.click()
          link.remove()
          window.URL.revokeObjectURL(url)
        })
      } else if (data.modal) {
        this.actionResponseData = data
        this.showActionResponseModal = true
      } else if (data.message) {
        this.emitResponseCallback(() => {
          Nova.success(data.message)
        })
      } else if (data.deleted) {
        this.emitResponseCallback()
      } else if (data.danger) {
        this.emitResponseCallback(() => {
          Nova.error(data.danger)
        })
      } else if (data.download) {
        this.emitResponseCallback(() => {
          let link = document.createElement('a')
          link.href = data.download
          link.download = data.name
          document.body.appendChild(link)
          link.click()
          document.body.removeChild(link)
        })
      } else if (data.redirect) {
        window.location = data.redirect
      } else if (data.visit) {
        Nova.visit({
          url: Nova.url(data.visit.path, data.visit.options),
          remote: false,
        })
      } else if (data.openInNewTab) {
        this.emitResponseCallback(() => {
          window.open(data.openInNewTab, '_blank')
        })
      } else {
        let message =
          data.message || this.__('The action was executed successfully.')

        this.emitResponseCallback(() => {
          Nova.success(message)
        })
      }
    },

    /**
     * Handle an Action button click
     */
    handleActionClick(uriKey) {
      this.selectedActionKey = uriKey
      this.determineActionStrategy()
    },
  },

  computed: {
    /**
     * Get the query string for an action request.
     */
    actionRequestQueryString() {
      return {
        action: this.selectedActionKey,
        pivotAction: this.selectedActionIsPivotAction,
        search: this.currentSearch,
        filters: this.encodedFilters,
        trashed: this.currentTrashed,
        viaResource: this.viaResource,
        viaResourceId: this.viaResourceId,
        viaRelationship: this.viaRelationship,
      }
    },

    /**
     * Get all of the available actions.
     */
    allActions() {
      if (!this.pivotActions) {
        return this.actions
      }

      return this.actions.concat(this.pivotActions.actions)
    },

    /**
     * Return the selected action being executed.
     */
    selectedAction() {
      if (this.selectedActionKey) {
        return find(this.allActions, a => a.uriKey == this.selectedActionKey)
      }
    },

    /**
     * Determine if the selected action is a pivot action.
     */
    selectedActionIsPivotAction() {
      return (
        this.hasPivotActions &&
        Boolean(find(this.pivotActions.actions, a => a === this.selectedAction))
      )
    },

    /**
     * Get all of the available actions for the resource.
     */
    availableActions() {
      return filter(this.actions, action => {
        return this.selectedResources.length > 0 && !action.standalone
      })
    },

    /**
     * Get all of the available actions for the resource.
     */
    availableStandaloneActions() {
      return filter(this.actions, action => {
        return action.standalone
      })
    },

    /**
     * Get all of the available pivot actions for the resource.
     */
    availablePivotActions() {
      if (!this.pivotActions) {
        return []
      }

      return filter(this.pivotActions.actions, action => {
        if (this.selectedResources.length == 0) {
          return action.standalone
        }

        return true
      })
    },

    /**
     * Determine whether there are any pivot actions
     */
    hasPivotActions() {
      return this.availablePivotActions.length > 0
    },
  },
}
