<template>
  <PanelItem :index="index" :field="field">
    <template #value>
      <p>
        <span v-if="fieldHasValue">
          {{ formattedDate }}
        </span>
        <span v-else>&mdash;</span>
      </p>
    </template>
  </PanelItem>
</template>

<script>
import { DateTime } from 'luxon'
import { FieldValue } from '@/mixins'

export default {
  mixins: [FieldValue],

  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

  computed: {
    formattedDate() {
      if (this.field.usesCustomizedDisplay) {
        return this.field.displayedAs
      }

      return DateTime.fromISO(this.field.value, {
        setZone: Nova.config('userTimezone') || Nova.config('timezone'),
      }).toLocaleString({
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
      })
    },
  },
}
</script>
