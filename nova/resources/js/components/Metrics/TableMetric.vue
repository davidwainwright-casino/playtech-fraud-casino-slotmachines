<template>
  <LoadingCard :loading="loading" class="pt-4">
    <div class="h-6 flex items-center px-6 mb-4">
      <h3 class="mr-3 leading-tight text-sm font-bold">{{ card.name }}</h3>
      <HelpTextTooltip :text="card.helpText" :width="card.helpWidth" />
    </div>

    <div class="mb-5 pb-4">
      <div class="overflow-hidden overflow-x-auto relative">
        <table class="w-full table-default">
          <tbody
            class="border-t border-b border-gray-100 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700"
          >
            <MetricTableRow v-for="row in value" :row="row" />
          </tbody>
        </table>
      </div>
    </div>
  </LoadingCard>
</template>

<script>
import { minimum } from '@/util'
import { InteractsWithDates, MetricBehavior } from '@/mixins'

export default {
  name: 'TableCard',

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
    value: [],
  }),

  watch: {
    resourceId() {
      this.fetch()
    },
  },

  created() {
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
        ({ data: { value } }) => {
          this.value = value
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
