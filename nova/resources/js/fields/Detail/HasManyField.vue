<template>
  <ResourceIndex
    :field="field"
    :resource-name="field.resourceName"
    :via-resource="resourceName"
    :via-resource-id="resourceId"
    :via-relationship="field.hasManyRelationship"
    :relationship-type="'hasMany'"
    @actionExecuted="actionExecuted"
    :load-cards="false"
    :initialPerPage="field.perPage || 5"
    :should-override-meta="false"
  />
</template>

<script>
import { mapProps } from '@/mixins'

export default {
  emits: ['actionExecuted'],

  props: {
    ...mapProps(['resourceId', 'field']),
    resourceName: {},
    resource: {},
  },

  methods: {
    /**
     * Handle the actionExecuted event and pass it up the chain.
     */
    actionExecuted() {
      this.$emit('actionExecuted')
    },
  },
}
</script>
