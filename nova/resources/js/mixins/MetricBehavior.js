export default {
  created() {
    Nova.$on('metric-refresh', this.fetch)

    Nova.$on('resources-deleted', this.fetch)
    Nova.$on('resources-restored', this.fetch)

    if (this.card.refreshWhenActionRuns) {
      Nova.$on('action-executed', this.fetch)
    }
  },

  beforeUnmount() {
    Nova.$off('metric-refresh', this.fetch)
    Nova.$off('resources-deleted', this.fetch)
    Nova.$off('resources-restored', this.fetch)
    Nova.$off('action-executed', this.fetch)
  },
}
