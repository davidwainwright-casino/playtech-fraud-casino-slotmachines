import filter from 'lodash/filter'

export default {
  props: {
    loadCards: {
      type: Boolean,
      default: true,
    },
  },

  data: () => ({ cards: [] }),

  /**
   * Fetch all of the metrics panels for this view
   */
  created() {
    this.fetchCards()
  },

  watch: {
    cardsEndpoint() {
      this.fetchCards()
    },
  },

  methods: {
    async fetchCards() {
      // We disable fetching of cards when the component is being show
      // on a resource detail view to avoid extra network requests
      if (this.loadCards) {
        const { data: cards } = await Nova.request().get(this.cardsEndpoint, {
          params: this.extraCardParams,
        })
        this.cards = cards
      }
    },
  },

  computed: {
    /**
     * Determine whether we have cards to show on the Dashboard.
     */
    shouldShowCards() {
      return this.cards.length > 0
    },

    /**
     * Determine if the cards array contains some detail-only cards.
     */
    hasDetailOnlyCards() {
      return filter(this.cards, c => c.onlyOnDetail == true).length > 0
    },

    /**
     * Get the extra card params to pass to the endpoint.
     */
    extraCardParams() {
      return null
    },
  },
}
