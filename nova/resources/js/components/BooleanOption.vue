<template>
  <CheckboxWithLabel
    :dusk="`${option.value}-checkbox`"
    :checked="isChecked"
    @input="updateCheckedState(option.value, $event.target.checked)"
  >
    {{ labelFor(option) }}
  </CheckboxWithLabel>
</template>

<script>
export default {
  emits: ['change'],

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
    isChecked() {
      return (
        this.$store.getters[`${this.resourceName}/filterOptionValue`](
          this.filter.class,
          this.option.value
        ) == true
      )
    },
  },
}
</script>
