<template>
  <FilterContainer>
    <span>{{ filter.name }}</span>

    <template #filter>
      <div class="space-y-2">
        <button type="button">
          <IconBooleanOption
            :dusk="`${field.uniqueKey}-filter-${option.value}-option`"
            :resource-name="resourceName"
            :key="option.value"
            v-for="option in field.options"
            :filter="filter"
            :option="option"
            @change="handleChange"
            label="label"
          />
        </button>
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

    field() {
      return this.filter.field
    },
  },
}
</script>
