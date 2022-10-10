<template>
  <div :class="`text-${field.textAlign}`">
    <a
      v-if="fieldHasValue"
      @click.stop
      :href="`mailto:${field.value}`"
      class="link-default whitespace-nowrap"
      >{{ fieldValue }}</a
    >
    <p v-else>&mdash;</p>
  </div>
</template>

<script>
import { FieldValue } from '@/mixins'

export default {
  mixins: [FieldValue],

  props: ['resourceName', 'field'],
}
</script>
