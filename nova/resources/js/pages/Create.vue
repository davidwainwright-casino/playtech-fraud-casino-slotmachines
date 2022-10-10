<template>
  <ResourceCreate
    :resource-name="resourceName"
    :via-resource="viaResource"
    :via-resource-id="viaResourceId"
    :via-relationship="viaRelationship"
    mode="form"
  />
</template>

<script>
import { mapProps } from '@/mixins'
import ResourceCreate from '@/views/Create'

export default {
  name: 'Create',

  components: {
    ResourceCreate,
  },

  props: mapProps([
    'resourceName',
    'viaResource',
    'viaResourceId',
    'viaRelationship',
  ]),
}
</script>
