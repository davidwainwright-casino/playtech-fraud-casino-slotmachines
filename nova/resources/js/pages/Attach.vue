<template>
  <AttachResource
    :resource-name="resourceName"
    :resource-id="resourceId"
    :related-resource-name="relatedResourceName"
    :via-resource="viaResource"
    :via-resource-id="viaResourceId"
    :parent-resource="parentResource"
    :via-relationship="viaRelationship"
    :polymorphic="polymorphic"
    :form-unique-id="formUniqueId"
  />
</template>

<script>
import { uid } from 'uid/single'

export default {
  name: 'Attach',

  props: {
    resourceName: {
      type: String,
      required: true,
    },
    resourceId: {
      required: true,
    },
    relatedResourceName: {
      type: String,
      required: true,
    },
    viaResource: {
      default: '',
    },
    viaResourceId: {
      default: '',
    },
    parentResource: {
      type: Object,
    },
    viaRelationship: {
      default: '',
    },
    polymorphic: {
      default: false,
    },
  },

  data: () => ({
    formUniqueId: uid(),
  }),
}
</script>
