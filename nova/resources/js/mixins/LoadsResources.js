export default {
  computed: {
    localStorageKey() {
      return `nova.resources.${this.resourceName}.collapsed`
    },
  },
}
