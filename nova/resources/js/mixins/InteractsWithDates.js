import { hourCycle } from '@/util'

export default {
  computed: {
    /**
     * Get the user's local timezone.
     */
    userTimezone() {
      return Nova.config('userTimezone') || Nova.config('timezone')
    },

    /**
     * Determine if the user is used to 12 hour time.
     */
    usesTwelveHourTime() {
      let locale = new Intl.DateTimeFormat().resolvedOptions().locale

      return hourCycle(locale) === 12
    },
  },
}
