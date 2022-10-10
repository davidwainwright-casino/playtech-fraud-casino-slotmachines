<template>
  <LoadingView :loading="loading" :dusk="'dashboard-' + this.name">
    <Head :title="label" />

    <Heading v-if="label && cards.length > 1" class="mb-3">{{
      __(label)
    }}</Heading>

    <div v-if="shouldShowCards">
      <Cards v-if="cards.length > 0" :cards="cards" />
    </div>
  </LoadingView>
</template>

<script>
export default {
  props: {
    name: {
      type: String,
      required: false,
      default: 'main',
    },
  },

  data: () => ({
    loading: true,
    label: '',
    cards: [],
  }),

  created() {
    this.fetchDashboard()
  },

  methods: {
    async fetchDashboard() {
      try {
        const {
          data: { label, cards },
        } = await Nova.request().get(this.dashboardEndpoint, {
          params: this.extraCardParams,
        })

        this.loading = false
        this.label = label
        this.cards = cards
      } catch (error) {
        if (error.response.status == 401) {
          return Nova.redirectToLogin()
        }

        Nova.visit('/404')
      }
    },
  },

  computed: {
    /**
     * Get the endpoint for this dashboard.
     */
    dashboardEndpoint() {
      return `/nova-api/dashboards/${this.name}`
    },

    /**
     * Determine whether we have cards to show on the Dashboard
     */
    shouldShowCards() {
      return this.cards.length > 0
    },

    /**
     * Get the extra card params to pass to the endpoint.
     */
    extraCardParams() {
      return null
    },
  },
}
</script>
