<template>
  <FilterContainer>
    <span>{{ filter.name }}</span>

    <template #filter>
      <div class="space-y-2 mt-2">
        <BooleanOption
          :dusk="`${filter.name}-boolean-filter-${option.value}-option`"
          :resource-name="resourceName"
          :key="option.value"
          v-for="option in options"
          :filter="filter"
          :option="option"
          @change="handleChange"
          label="label"
        />
      </div>
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
    handleChange() {
      this.$emit('change')
    },
  },

  computed: {
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
