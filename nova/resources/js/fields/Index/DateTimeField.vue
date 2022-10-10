<template>
  <div :class="`text-${field.textAlign}`">
    <span
      v-if="fieldHasValue"
      class="whitespace-nowrap"
      :title="field.originalValue"
    >
      {{ formattedDate }}
    </span>
    <span v-else>&mdash;</span>
  </div>
</template>

<script>
import { DateTime } from 'luxon'
import { FieldValue } from '@/mixins'

export default {
  mixins: [FieldValue],

  props: ['resourceName', 'field'],

  computed: {
    timezone() {
      return Nova.config('userTimezone') || Nova.config('timezone')
    },

    formattedDate() {
      if (this.field.usesCustomizedDisplay) {
        return this.field.displayedAs
      }

      return DateTime.fromISO(this.field.value)
        .setZone(this.timezone)
        .toLocaleString({
          year: 'numeric',
          month: '2-digit',
          day: '2-digit',
          hour: '2-digit',
          minute: '2-digit',
          timeZoneName: 'short',
        })
    },
  },
}
</script>
