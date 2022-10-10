import cloneDeep from 'lodash/cloneDeep'
import each from 'lodash/each'
import find from 'lodash/find'
import filter from 'lodash/filter'
import map from 'lodash/map'
import reduce from 'lodash/reduce'
import { escapeUnicode } from '@/util/escapeUnicode'

export default {
  namespaced: true,

  state: () => ({
    filters: [],
    originalFilters: [],
  }),

  getters: {
    /**
     * The filters for the resource
     */
    filters: state => state.filters,

    /**
     * The original filters for the resource
     */
    originalFilters: state => state.originalFilters,

    /**
     * Determine if there are any filters for the resource.
     */
    hasFilters: state => Boolean(state.filters.length > 0),

    /**
     * The current unencoded filter value payload
     */
    currentFilters: (state, getters) => {
      return map(filter(state.filters), f => {
        return {
          [f.class]: f.currentValue,
        }
      })
    },

    /**
     * Return the current filters encoded to a string.
     */
    currentEncodedFilters: (state, getters) =>
      btoa(escapeUnicode(JSON.stringify(getters.currentFilters))),

    /**
     * Determine whether any filters are applied
     */
    filtersAreApplied: (state, getters) => getters.activeFilterCount > 0,

    /**
     * Return the number of filters that are non-default
     */
    activeFilterCount: (state, getters) => {
      return reduce(
        state.filters,
        (result, f) => {
          const originalFilter = getters.getOriginalFilter(f.class)
          const originalFilterCloneValue = JSON.stringify(
            originalFilter.currentValue
          )
          const currentFilterCloneValue = JSON.stringify(f.currentValue)
          return currentFilterCloneValue == originalFilterCloneValue
            ? result
            : result + 1
        },
        0
      )
    },

    /**
     * Get a single filter from the list of filters.
     */
    getFilter: state => filterKey => {
      return find(state.filters, filter => {
        return filter.class == filterKey
      })
    },

    getOriginalFilter: state => filterKey => {
      return find(state.originalFilters, filter => {
        return filter.class == filterKey
      })
    },

    /**
     * Get the options for a single filter.
     */
    getOptionsForFilter: (state, getters) => filterKey => {
      const filter = getters.getFilter(filterKey)
      return filter ? filter.options : []
    },

    /**
     * Get the current value for a given filter at the provided key.
     */
    filterOptionValue: (state, getters) => (filterKey, optionKey) => {
      const filter = getters.getFilter(filterKey)

      return find(filter.currentValue, (value, key) => key == optionKey)
    },
  },

  actions: {
    /**
     * Fetch the current filters for the given resource name.
     */
    async fetchFilters({ commit, state }, options) {
      let { resourceName, lens = false } = options
      let { viaResource, viaResourceId, viaRelationship, relationshipType } =
        options
      let params = {
        params: {
          viaResource,
          viaResourceId,
          viaRelationship,
          relationshipType,
        },
      }

      const { data } = lens
        ? await Nova.request().get(
            '/nova-api/' + resourceName + '/lens/' + lens + '/filters',
            params
          )
        : await Nova.request().get(
            '/nova-api/' + resourceName + '/filters',
            params
          )

      commit('storeFilters', data)
    },

    /**
     * Reset the default filter state to the original filter settings.
     */
    async resetFilterState({ commit, getters }) {
      each(getters.originalFilters, filter => {
        commit('updateFilterState', {
          filterClass: filter.class,
          value: filter.currentValue,
        })
      })
    },

    /**
     * Initialize the current filter values from the decoded query string.
     */
    async initializeCurrentFilterValuesFromQueryString(
      { commit, getters },
      encodedFilters
    ) {
      if (encodedFilters) {
        const initialFilters = JSON.parse(atob(encodedFilters))
        each(initialFilters, filter => {
          if (
            filter.hasOwnProperty('class') &&
            filter.hasOwnProperty('value')
          ) {
            commit('updateFilterState', {
              filterClass: filter.class,
              value: filter.value,
            })
          } else {
            for (let key in filter) {
              commit('updateFilterState', {
                filterClass: key,
                value: filter[key],
              })
            }
          }
        })
      }
    },
  },

  mutations: {
    updateFilterState(state, { filterClass, value }) {
      const filter = find(state.filters, f => f.class == filterClass)

      if (filter !== undefined && filter !== null) {
        filter.currentValue = value
      }
    },

    /**
     * Store the mutable filter settings
     */
    storeFilters(state, data) {
      state.filters = data
      state.originalFilters = cloneDeep(data)
    },

    /**
     * Clear the filters for this resource
     */
    clearFilters(state) {
      state.filters = []
      state.originalFilters = []
    },
  },
}
