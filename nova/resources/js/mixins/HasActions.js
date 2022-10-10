import filter from 'lodash/filter'

export default {
  data: () => ({
    actions: [],
    pivotActions: null,
  }),

  computed: {
    /**
     * Determine whether there are any standalone actions.
     */
    haveStandaloneActions() {
      return filter(this.allActions, a => a.standalone == true).length > 0
    },

    /**
     * Return the available actions.
     */
    availableActions() {
      return this.actions
    },

    /**
     * Determine if the resource has any pivot actions available.
     */
    hasPivotActions() {
      return this.pivotActions && this.pivotActions.actions.length > 0
    },

    /**
     * Get the name of the pivot model for the resource.
     */
    pivotName() {
      return this.pivotActions ? this.pivotActions.name : ''
    },

    /**
     * Determine if the resource has any actions available.
     */
    actionsAreAvailable() {
      return this.allActions.length > 0
    },

    /**
     * Get all of the actions available to the resource.
     */
    allActions() {
      return this.hasPivotActions
        ? this.actions.concat(this.pivotActions.actions)
        : this.actions
    },

    /**
     * Get the selected resources for the action selector.
     */
    selectedResourcesForActionSelector() {
      return this.selectAllMatchingChecked ? 'all' : this.selectedResourceIds
    },
  },
}
