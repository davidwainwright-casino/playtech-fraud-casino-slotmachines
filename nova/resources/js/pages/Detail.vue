<template>
  <ResourceDetail
    :resourceName="resourceName"
    :resourceId="resourceId"
    :shouldOverrideMeta="true"
    :shouldEnableShortcut="true"
  />
</template>

<script>
import { mapProps } from '@/mixins'

export default {
  name: 'Detail',

  props: mapProps(['resourceName', 'resourceId']),
}
</script>
