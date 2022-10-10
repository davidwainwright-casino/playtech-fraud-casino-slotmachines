<template>
  <PanelItem :index="index" :field="field">
    <template #value>
      <FormKeyValueTable
        v-if="theData.length > 0"
        :edit-mode="false"
        class="overflow-hidden"
      >
        <FormKeyValueHeader
          :key-label="field.keyLabel"
          :value-label="field.valueLabel"
        />

        <div
          class="bg-gray-50 dark:bg-gray-700 overflow-hidden key-value-items"
        >
          <FormKeyValueItem
            v-for="(item, index) in theData"
            :index="index"
            :item="item"
            :disabled="true"
            :key="item.key"
          />
        </div>
      </FormKeyValueTable>
    </template>
  </PanelItem>
</template>

<script>
import map from 'lodash/map'

export default {
  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

  data: () => ({ theData: [] }),

  created() {
    this.theData = map(
      Object.entries(this.field.value || {}),
      ([key, value]) => ({
        key: `${key}`,
        value,
      })
    )
  },
}
</script>
