<template>
  <ResourceUpdate
    :resource-name="resourceName"
    :resource-id="resourceId"
    :via-resource="viaResource"
    :via-resource-id="viaResourceId"
    :via-relationship="viaRelationship"
    :form-unique-id="formUniqueId"
  />
</template>

<script>
import { mapProps } from '@/mixins'
import ResourceUpdate from '@/views/Update'
import { uid } from 'uid/single'

export default {
  name: 'Update',

  components: {
    ResourceUpdate,
  },

  props: mapProps([
    'resourceName',
    'resourceId',
    'viaResource',
    'viaResourceId',
    'viaRelationship',
  ]),

  data: () => ({
    formUniqueId: uid(),
  }),
}
</script>
