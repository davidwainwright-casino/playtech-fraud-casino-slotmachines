import identity from 'lodash/identity'
import pickBy from 'lodash/pickBy'

export default {
  data: () => ({
    filterIsActive: false,
  }),

  methods: {
    /**
     * Clear filters and reset the resource table
     */
    async clearSelectedFilters(lens) {
      if (lens) {
        await this.$store.dispatch(`${this.resourceName}/resetFilterState`, {
          resourceName: this.resourceName,
          lens,
        })
      } else {
        await this.$store.dispatch(`${this.resourceName}/resetFilterState`, {
          resourceName: this.resourceName,
        })
      }

      this.updateQueryString({
        [this.pageParameter]: 1,
        [this.filterParameter]: '',
      })

      Nova.$emit('filter-reset')
      Nova.$emit('filter-changed', [''])
    },

    /**
     * Handle a filter state change.
     */
    filterChanged() {
      let filtersAreApplied =
        this.$store.getters[`${this.resourceName}/filtersAreApplied`]
      let currentEncodedFilters =
        this.$store.getters[`${this.resourceName}/currentEncodedFilters`]

      if (filtersAreApplied || this.filterIsActive) {
        this.filterIsActive = true
        this.updateQueryString({
          [this.pageParameter]: 1,
          [this.filterParameter]: currentEncodedFilters,
        })
      }

      Nova.$emit('filter-changed', [currentEncodedFilters])
    },

    /**
     * Set up filters for the current view
     */
    async initializeFilters(lens) {
      // Clear out the filters from the store first
      this.$store.commit(`${this.resourceName}/clearFilters`)

      await this.$store.dispatch(
        `${this.resourceName}/fetchFilters`,
        pickBy(
          {
            resourceName: this.resourceName,
            viaResource: this.viaResource,
            viaResourceId: this.viaResourceId,
            viaRelationship: this.viaRelationship,
            relationshipType: this.relationshipType,
            lens,
          },
          identity
        )
      )

      await this.initializeState(lens)
    },

    /**
     * Initialize the filter state
     */
    async initializeState(lens) {
      this.initialEncodedFilters
        ? await this.$store.dispatch(
            `${this.resourceName}/initializeCurrentFilterValuesFromQueryString`,
            this.initialEncodedFilters
          )
        : await this.$store.dispatch(`${this.resourceName}/resetFilterState`, {
            resourceName: this.resourceName,
            lens,
          })
    },
  },

  computed: {
    /**
     * Get the name of the filter query string variable.
     */
    filterParameter() {
      return this.resourceName + '_filter'
    },
  },
}
