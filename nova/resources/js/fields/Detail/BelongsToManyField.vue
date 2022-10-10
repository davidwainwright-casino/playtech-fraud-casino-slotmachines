<template>
  <ResourceIndex
    :field="field"
    :resource-name="field.resourceName"
    :via-resource="resourceName"
    :via-resource-id="resourceId"
    :via-relationship="field.belongsToManyRelationship"
    :relationship-type="'belongsToMany'"
    @actionExecuted="actionExecuted"
    :load-cards="false"
    :initialPerPage="field.perPage || 5"
    :should-override-meta="false"
  />
</template>

<script>
export default {
  emits: ['actionExecuted'],

  props: ['resourceName', 'resourceId', 'resource', 'field'],

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
