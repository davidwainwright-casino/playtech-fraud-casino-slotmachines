<template>
  <div>
    <template v-if="fieldValue">
      <div v-if="shouldDisplayAsHtml" @click.stop v-html="fieldValue"></div>
      <span v-else>{{ fieldValue }}</span>
    </template>
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
