<template>
  <div>
    <div :class="`text-${field.textAlign}`">
      <span v-if="fieldHasValue" class="whitespace-nowrap">
        {{ formattedDate }}
      </span>
      <span v-else>&mdash;</span>
    </div>
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

      return DateTime.fromISO(this.field.value, {
        setZone: this.timezone,
      }).toLocaleString({
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
      })
    },
  },
}
</script>
