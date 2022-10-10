<template>
  <div
    class="flex items-center"
    @click="updateCheckedState(option.value, nextValue)"
  >
    <IconBoolean :value="currentValue" :nullable="true" />
    <span class="ml-2">
      {{ labelFor(option) }}
    </span>
  </div>
</template>

<script>
import isNil from 'lodash/isNil'

export default {
  props: {
    resourceName: {
      type: String,
      required: true,
    },
    filter: Object,
    option: Object,
    label: { default: 'name' },
  },

  methods: {
    labelFor(option) {
      return option[this.label] || ''
    },

    updateCheckedState(optionKey, checked) {
      let oldValue = this.filter.currentValue
      let newValue = { ...oldValue, [optionKey]: checked }

      this.$store.commit(`${this.resourceName}/updateFilterState`, {
        filterClass: this.filter.class,
        value: newValue,
      })

      this.$emit('change')
    },
  },

  computed: {
    currentValue() {
      let value = this.$store.getters[`${this.resourceName}/filterOptionValue`](
        this.filter.class,
        this.option.value
      )

      return !isNil(value) ? value : null
    },

    isChecked() {
      return this.currentValue == true
    },

    nextValue() {
      let value = this.currentValue

      if (value === true) {
        return false
      } else if (value === false) {
        return null
      }

      return true
    },
  },
}
</script>
