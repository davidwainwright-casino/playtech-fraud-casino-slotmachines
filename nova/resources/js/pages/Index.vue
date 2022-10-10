<template>
  <ResourceIndex
    :resourceName="resourceName"
    :shouldOverrideMeta="true"
    :shouldEnableShortcut="true"
  />
</template>

<script>
import { mapProps } from '@/mixins'

export default {
  name: 'Index',

  props: mapProps(['resourceName']),
}
</script>
