<template>
  <Dropdown
    v-if="filters.length > 0 || softDeletes || !viaResource"
    :class="{
      'bg-primary-500 hover:bg-primary-600 border-primary-500':
        filtersAreApplied,
      'dark:bg-primary-500 dark:hover:bg-primary-600 dark:border-primary-500':
        filtersAreApplied,
    }"
    :handle-internal-clicks="false"
    class="flex h-9 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
    dusk="filter-selector"
    @menu-opened="handleMenuOpened"
  >
    <span class="sr-only">{{ __('Filter Dropdown') }}</span>
    <DropdownTrigger
      :class="{
        'text-white hover:text-white dark:text-gray-800 dark:hover:text-gray-800':
          filtersAreApplied,
      }"
      class="toolbar-button px-2"
    >
      <Icon type="filter" />

      <span
        v-if="filtersAreApplied"
        :class="{
          'text-white dark:text-gray-800': filtersAreApplied,
        }"
        class="ml-2 font-bold"
      >
        {{ activeFilterCount }}
      </span>
    </DropdownTrigger>

    <template #menu>
      <DropdownMenu width="260">
        <ScrollWrap :height="350" class="bg-white dark:bg-gray-900">
          <div
            ref="theForm"
            class="divide-y divide-gray-200 dark:divide-gray-800 divide-solid"
          >
            <div v-if="filtersAreApplied" class="bg-gray-100">
              <button
                class="py-2 w-full block text-xs uppercase tracking-wide text-center text-gray-500 dark:bg-gray-800 dark:hover:bg-gray-700 font-bold focus:outline-none"
                @click="handleClearSelectedFiltersClick"
              >
                {{ __('Reset Filters') }}
              </button>
            </div>

            <!-- Custom Filters -->
            <div v-for="filter in filters" :key="filter.name">
              <component
                :is="filter.component"
                :filter-key="filter.class"
                :lens="lens"
                :resource-name="resourceName"
                @change="$emit('filter-changed')"
                @input="$emit('filter-changed')"
              />
            </div>

            <!-- Soft Deletes -->
            <FilterContainer v-if="softDeletes" dusk="filter-soft-deletes">
              <span>{{ __('Trashed') }}</span>

              <template #filter>
                <SelectControl
                  v-model:selected="trashedValue"
                  :options="[
                    { value: '', label: '—' },
                    { value: 'with', label: __('With Trashed') },
                    { value: 'only', label: __('Only Trashed') },
                  ]"
                  dusk="trashed-select"
                  size="sm"
                  @change="trashedValue = $event"
                />
              </template>
            </FilterContainer>

            <!-- Per Page -->
            <FilterContainer v-if="!viaResource" dusk="filter-per-page">
              <span>{{ __('Per Page') }}</span>

              <template #filter>
                <SelectControl
                  v-model:selected="perPageValue"
                  :options="perPageOptionsForFilter"
                  dusk="per-page-select"
                  size="sm"
                  @change="perPageValue = $event"
                />
              </template>
            </FilterContainer>
          </div>
        </ScrollWrap>
      </DropdownMenu>
    </template>
  </Dropdown>
</template>

<script>
import map from 'lodash/map'

export default {
  emits: [
    'filter-changed',
    'clear-selected-filters',
    'trashed-changed',
    'per-page-changed',
  ],

  props: {
    resourceName: String,
    lens: {
      type: String,
      default: '',
    },
    viaResource: String,
    viaHasOne: Boolean,
    softDeletes: Boolean,
    trashed: {
      type: String,
      validator: value => ['', 'with', 'only'].indexOf(value) != -1,
    },
    perPage: [String, Number],
    perPageOptions: Array,
  },

  methods: {
    handleClearSelectedFiltersClick() {
      Nova.$emit('clear-filter-values')

      setTimeout(() => {
        this.$emit('clear-selected-filters')
      }, 500)
    },

    handleMenuOpened() {
      this.$nextTick(() => {
        let formFields = this.$refs.theForm.querySelectorAll(
          'input, textarea, select'
        )

        if (formFields.length > 0) {
          formFields[0].focus({
            preventScroll: true,
          })
        }
      })
    },
  },

  computed: {
    trashedValue: {
      set(event) {
        let value = event?.target?.value || event

        this.$emit('trashed-changed', value)
      },
      get() {
        return this.trashed
      },
    },

    perPageValue: {
      set(event) {
        let value = event?.target?.value || event

        this.$emit('per-page-changed', value)
      },
      get() {
        return this.perPage
      },
    },

    /**
     * Return the filters from state
     */
    filters() {
      return this.$store.getters[`${this.resourceName}/filters`]
    },

    /**
     * Determine via state whether filters are applied
     */
    filtersAreApplied() {
      return this.$store.getters[`${this.resourceName}/filtersAreApplied`]
    },

    /**
     * Return the number of active filters
     */
    activeFilterCount() {
      return this.$store.getters[`${this.resourceName}/activeFilterCount`]
    },

    /**
     * Return the values for the per page filter
     */
    perPageOptionsForFilter() {
      return map(this.perPageOptions, option => {
        return { value: option, label: option }
      })
    },
  },
}
</script>
