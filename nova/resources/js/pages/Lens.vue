<template>
  <ResourceLens :resourceName="resourceName" :lens="lens" />
</template>

<script>
import { mapProps } from '@/mixins'
import ResourceLens from '@/views/Lens'

export default {
  name: 'Lens',

  components: {
    ResourceLens,
  },

  props: {
    lens: {
      type: String,
      required: true,
    },

    ...mapProps(['resourceName']),
  },
}
</script>
