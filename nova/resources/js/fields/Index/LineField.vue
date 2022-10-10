<template>
  <div :class="`text-${field.textAlign}`">
    <template v-if="fieldValue">
      <div v-if="shouldDisplayAsHtml" @click.stop v-html="fieldValue"></div>
      <span v-else class="whitespace-nowrap" :class="field.classes">
        {{ fieldValue }}
      </span>
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
