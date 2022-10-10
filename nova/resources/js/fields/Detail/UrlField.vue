<template>
  <PanelItem :index="index" :field="field">
    <template v-slot:value>
      <p v-if="fieldHasValue && !shouldDisplayAsHtml" class="text-90">
        <a
          class="link-default"
          :href="field.value"
          rel="noreferrer noopener"
          target="_blank"
        >
          {{ fieldValue }}
        </a>
      </p>
      <div
        v-else-if="fieldValue && shouldDisplayAsHtml"
        v-html="fieldValue"
      ></div>
      <p v-else>&mdash;</p>
    </template>
  </PanelItem>
</template>

<script>
import { FieldValue } from '@/mixins'

export default {
  mixins: [FieldValue],

  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],
}
</script>
