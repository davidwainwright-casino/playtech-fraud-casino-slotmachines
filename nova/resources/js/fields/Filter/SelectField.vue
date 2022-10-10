<template>
  <FilterContainer>
    <span>{{ filter.name }}</span>

    <template #filter>
      <!-- Search Input -->
      <SearchInput
        v-if="isSearchable"
        ref="searchable"
        :data-testid="`${field.uniqueKey}-search-filter`"
        @input="performSearch"
        @clear="clearSelection"
        @selected="selectOption"
        :value="selectedOption"
        :data="filteredOptions"
        :clearable="true"
        trackBy="value"
        class="w-full"
      >
        <!-- The Selected Option Slot -->
        <div v-if="selectedOption" class="flex items-center">
          {{ selectedOption.label }}
        </div>

        <!-- Options List Slot -->
        <template #option="{ option, selected }">
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
        :dusk="`${field.uniqueKey}-filter`"
        v-model:selected="value"
        @change="value = $event"
        :options="field.options"
      >
        <option value="" :selected="value === ''">&mdash;</option>
      </SelectControl>
    </template>
  </FilterContainer>
</template>

<script>
import debounce from 'lodash/debounce'
import find from 'lodash/find'
import isNil from 'lodash/isNil'

export default {
  emits: ['change'],

  props: {
    resourceName: {
      type: String,
      required: true,
    },
    filterKey: {
      type: String,
      required: true,
    },
    lens: String,
  },

  data: () => ({
    selectedOption: null,
    search: '',

    value: null,
    debouncedHandleChange: null,
  }),

  mounted() {
    Nova.$on('filter-reset', this.handleFilterReset)
  },

  created() {
    this.debouncedHandleChange = debounce(() => this.handleChange(), 500)
    let value = this.filter.currentValue

    if (value) {
      let selectedOption = find(this.field.options, v => v.value == value)

      this.selectOption(selectedOption)
    }
  },

  beforeUnmount() {
    Nova.$off('filter-reset', this.handleFilterReset)
  },

  watch: {
    selectedOption(option) {
      if (!isNil(option) && option !== '') {
        this.value = option.value
      } else {
        this.value = ''
      }
    },

    value() {
      this.debouncedHandleChange()
    },
  },

  methods: {
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

      if (this.$refs.searchable) {
        this.$refs.searchable.close()
      }
    },

    /**
     * Select the given option.
     */
    selectOption(option) {
      this.selectedOption = option
      this.value = option.value
    },

    handleChange() {
      this.$store.commit(`${this.resourceName}/updateFilterState`, {
        filterClass: this.filterKey,
        value: this.value,
      })

      this.$emit('change')
    },

    handleFilterReset() {
      if (this.filter.currentValue !== '') {
        return
      }

      this.clearSelection()
    },
  },

  computed: {
    filter() {
      return this.$store.getters[`${this.resourceName}/getFilter`](
        this.filterKey
      )
    },

    field() {
      return this.filter.field
    },

    /**
     * Determine if the related resources is searchable
     */
    isSearchable() {
      return this.field.searchable
    },

    /**
     * Return the field options filtered by the search string.
     */
    filteredOptions() {
      return this.field.options.filter(option => {
        return (
          option.label.toLowerCase().indexOf(this.search.toLowerCase()) > -1
        )
      })
    },
  },
}
</script>
