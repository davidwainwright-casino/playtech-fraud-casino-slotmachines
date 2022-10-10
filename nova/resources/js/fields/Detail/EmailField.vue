<template>
  <PanelItem :index="index" :field="field">
    <template #value>
      <p v-if="fieldHasValue" class="text-90 flex items-center">
        <a :href="`mailto:${field.value}`" class="link-default">
          {{ fieldValue }}
        </a>
      </p>
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
