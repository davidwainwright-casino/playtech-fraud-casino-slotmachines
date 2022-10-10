<template>
  <FilterContainer>
    <template #filter>
      <label class="block">
        <span class="uppercase text-xs font-bold tracking-wide">{{
          `${filter.name} - ${__('From')}`
        }}</span>

        <input
          class="block w-full form-control form-input form-input-bordered"
          v-model="startValue"
          :dusk="`${field.uniqueKey}-range-start`"
          v-bind="startExtraAttributes"
        />
      </label>

      <label class="block mt-2">
        <span class="uppercase text-xs font-bold tracking-wide">{{
          `${filter.name} - ${__('To')}`
        }}</span>
        <input
          class="block w-full form-control form-input form-input-bordered"
          v-model="endValue"
          :dusk="`${field.uniqueKey}-range-end`"
          v-bind="endExtraAttributes"
        />
      </label>
    </template>
  </FilterContainer>
</template>

<script>
import debounce from 'lodash/debounce'
import omit from 'lodash/omit'
import toNumber from 'lodash/toNumber'
import filled from '@/util/filled'

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
    startValue: null,
    endValue: null,
    debouncedHandleChange: null,
  }),

  created() {
    this.debouncedHandleChange = debounce(() => this.handleChange(), 500)
    this.setCurrentFilterValue()
  },

  mounted() {
    Nova.$on('filter-reset', this.setCurrentFilterValue)
  },

  beforeUnmount() {
    Nova.$off('filter-reset', this.setCurrentFilterValue)
  },

  watch: {
    startValue() {
      this.debouncedHandleChange()
    },

    endValue() {
      this.debouncedHandleChange()
    },
  },

  methods: {
    setCurrentFilterValue() {
      let [startValue, endValue] = this.filter.currentValue || [null, null]

      this.startValue = filled(startValue) ? toNumber(startValue) : null
      this.endValue = filled(endValue) ? toNumber(endValue) : null
    },

    validateFilter(startValue, endValue) {
      startValue = filled(startValue) ? toNumber(startValue) : null
      endValue = filled(endValue) ? toNumber(endValue) : null

      if (
        startValue !== null &&
        this.field.min &&
        this.field.min > startValue
      ) {
        startValue = toNumber(this.field.min)
      }

      if (endValue !== null && this.field.max && this.field.max < endValue) {
        endValue = toNumber(this.field.max)
      }

      return [startValue, endValue]
    },

    handleChange() {
      this.$store.commit(`${this.resourceName}/updateFilterState`, {
        filterClass: this.filterKey,
        value: this.validateFilter(this.startValue, this.endValue),
      })

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

    startExtraAttributes() {
      const attrs = omit(this.field.extraAttributes, ['readonly'])

      return {
        // Leave the default attributes even though we can now specify
        // whatever attributes we like because the old number field still
        // uses the old field attributes
        type: this.field.type || 'number',
        min: this.field.min,
        max: this.field.max,
        step: this.field.step,
        pattern: this.field.pattern,
        placeholder: this.__('Min'),
        ...attrs,
      }
    },

    endExtraAttributes() {
      const attrs = omit(this.field.extraAttributes, ['readonly'])

      return {
        // Leave the default attributes even though we can now specify
        // whatever attributes we like because the old number field still
        // uses the old field attributes
        type: this.field.type || 'number',
        min: this.field.min,
        max: this.field.max,
        step: this.field.step,
        pattern: this.field.pattern,
        placeholder: this.__('Max'),
        ...attrs,
      }
    },
  },
}
</script>
