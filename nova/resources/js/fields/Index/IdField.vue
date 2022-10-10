<template>
  <div :class="`text-${field.textAlign}`">
    <Link
      @click.stop
      v-if="fieldHasValue && !isPivot && authorizedToView"
      :href="$url(`/resources/${resourceName}/${field.value}`)"
      class="link-default"
    >
      {{ field.value }}
    </Link>
    <p v-else-if="fieldHasValue || isPivot">
      {{ field.pivotValue || field.value }}
    </p>
    <p v-else>&mdash;</p>
  </div>
</template>

<script>
import isNil from 'lodash/isNil'
import { FieldValue } from '@/mixins'

export default {
  mixins: [FieldValue],

  props: ['resource', 'resourceName', 'field'],

  computed: {
    isPivot() {
      return !isNil(this.field.pivotValue)
    },

    authorizedToView() {
      return this.resource?.authorizedToView ?? false
    },
  },
}
</script>
