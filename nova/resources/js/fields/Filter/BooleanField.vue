<template>
  <FilterContainer>
    <div class="" @click="handleChange">
      <label class="block">{{ filter.name }}</label>

      <IconBoolean
        :dusk="`${field.uniqueKey}-filter`"
        class="mt-2"
        :value="value"
        :nullable="true"
      />
    </div>
  </FilterContainer>
</template>

<script>
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

  methods: {
    handleChange() {
      let value = this.nextValue(this.value)

      this.$store.commit(`${this.resourceName}/updateFilterState`, {
        filterClass: this.filterKey,
        value: value ?? '',
      })

      this.$emit('change')
    },

    nextValue(value) {
      if (value === true) {
        return false
      } else if (value === false) {
        return null
      }

      return true
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

    value() {
      let value = this.filter.currentValue

      return value === true || value === false ? value : null
    },
  },
}
</script>
