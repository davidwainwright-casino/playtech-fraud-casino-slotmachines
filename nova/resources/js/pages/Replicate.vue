<template>
  <CreateForm
    @resource-created="handleResourceCreated"
    @create-cancelled="handleCreateCancelled"
    mode="form"
    :resource-name="resourceName"
    :from-resource-id="resourceId"
    :via-resource="viaResource"
    :via-resource-id="viaResourceId"
    :via-relationship="viaRelationship"
    @update-form-status="onUpdateFormStatus"
    :should-override-meta="true"
    :form-unique-id="formUniqueId"
  />
</template>

<script>
import { mapProps } from '@/mixins'
import ResourceCreate from '@/views/Create'

export default {
  name: 'Replicate',

  extends: ResourceCreate,

  props: {
    ...mapProps(['resourceId']),
  },
}
</script>
