<template>
  <FilterContainer>
    <span>{{ filter.name }}</span>

    <template #filter>
      <input
        class="w-full flex form-control form-control-sm form-input form-input-bordered"
        type="date"
        :dusk="`${filter.name}-date-filter`"
        name="date-filter"
        autocomplete="off"
        :value="value"
        :placeholder="placeholder"
        @change="handleChange"
      />
    </template>
  </FilterContainer>
</template>

<script>
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

  methods: {
    handleChange(event) {
      let value = event?.target?.value ?? event

      this.$store.commit(`${this.resourceName}/updateFilterState`, {
        filterClass: this.filterKey,
        value,
      })

      this.$emit('change')
    },
  },

  computed: {
    placeholder() {
      return this.filter.placeholder || this.__('Choose date')
    },

    value() {
      return this.filter.currentValue
    },

    filter() {
      return this.$store.getters[`${this.resourceName}/getFilter`](
        this.filterKey
      )
    },

    options() {
      return this.$store.getters[`${this.resourceName}/getOptionsForFilter`](
        this.filterKey
      )
    },
  },
}
</script>
