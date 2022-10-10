<template>
  <BaseProgressMetric
    :title="card.name"
    :help-text="card.helpText"
    :help-width="card.helpWidth"
    :target="target"
    :value="value"
    :percentage="percentage"
    :prefix="prefix"
    :suffix="suffix"
    :suffix-inflection="suffixInflection"
    :format="format"
    :avoid="avoid"
    :loading="loading"
  />
</template>

<script>
import { minimum } from '@/util'
import { InteractsWithDates, MetricBehavior } from '@/mixins'

export default {
  name: 'ProgressMetric',

  mixins: [InteractsWithDates, MetricBehavior],

  props: {
    card: {
      type: Object,
      required: true,
    },

    resourceName: {
      type: String,
      default: '',
    },

    resourceId: {
      type: [Number, String],
      default: '',
    },

    lens: {
      type: String,
      default: '',
    },
  },

  data: () => ({
    loading: true,
    format: '(0[.]00a)',
    avoid: false,
    prefix: '',
    suffix: '',
    suffixInflection: true,
    value: 0,
    target: 0,
    percentage: 0,
    zeroResult: false,
  }),

  watch: {
    resourceId() {
      this.fetch()
    },
  },

  created() {
    if (this.hasRanges) {
      this.selectedRangeKey =
        this.card.selectedRangeKey || this.card.ranges[0].value
    }

    this.fetch()
  },

  mounted() {
    if (this.card && this.card.refreshWhenFiltersChange === true) {
      Nova.$on('filter-changed', this.fetch)
    }
  },

  beforeUnmount() {
    if (this.card && this.card.refreshWhenFiltersChange === true) {
      Nova.$off('filter-changed', this.fetch)
    }
  },

  methods: {
    fetch() {
      this.loading = true

      minimum(Nova.request().get(this.metricEndpoint, this.metricPayload)).then(
        ({
          data: {
            value: {
              value,
              target,
              percentage,
              prefix,
              suffix,
              suffixInflection,
              format,
              avoid,
            },
          },
        }) => {
          this.value = value
          this.target = target
          this.percentage = percentage
          this.format = format || this.format
          this.avoid = avoid
          this.prefix = prefix || this.prefix
          this.suffix = suffix || this.suffix
          this.suffixInflection = suffixInflection
          this.loading = false
        }
      )
    },
  },

  computed: {
    metricPayload() {
      const payload = {
        params: {
          timezone: this.userTimezone,
        },
      }

      if (
        !Nova.missingResource(this.resourceName) &&
        this.card &&
        this.card.refreshWhenFiltersChange === true
      ) {
        payload.params.filter =
          this.$store.getters[`${this.resourceName}/currentEncodedFilters`]
      }

      return payload
    },

    metricEndpoint() {
      const lens = this.lens !== '' ? `/lens/${this.lens}` : ''
      if (this.resourceName && this.resourceId) {
        return `/nova-api/${this.resourceName}${lens}/${this.resourceId}/metrics/${this.card.uriKey}`
      } else if (this.resourceName) {
        return `/nova-api/${this.resourceName}${lens}/metrics/${this.card.uriKey}`
      } else {
        return `/nova-api/metrics/${this.card.uriKey}`
      }
    },
  },
}
</script>
