<template>
  <PanelItem :index="index" :field="field">
    <template v-slot:value>
      <span
        v-for="item in fieldValues"
        v-text="item"
        class="inline-block text-sm mb-1 mr-2 px-2 py-0 bg-primary-500 text-white dark:text-gray-900 rounded"
      />
    </template>
  </PanelItem>
</template>

<script>
import forEach from 'lodash/forEach'
import indexOf from 'lodash/indexOf'

export default {
  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

  computed: {
    fieldValues() {
      let selected = []

      forEach(this.field.options, option => {
        if (indexOf(this.field.value, option.value.toString()) >= 0) {
          selected.push(option.label)
        }
      })

      return selected
    },
  },
}
</script>
