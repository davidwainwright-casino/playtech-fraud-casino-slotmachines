export default {
  data: () => ({ collapsed: false }),

  created() {
    this.collapsed = JSON.parse(localStorage.getItem(this.localStorageKey))
  },

  unmounted() {
    localStorage.setItem(this.localStorageKey, this.collapsed)
  },

  methods: {
    toggleCollapse() {
      this.collapsed = !this.collapsed
      localStorage.setItem(this.localStorageKey, this.collapsed)
    },
  },

  computed: {
    ariaExpanded() {
      return this.collapsed === false ? 'true' : 'false'
    },

    shouldBeCollapsed() {
      return this.collapsed
    },

    localStorageKey() {
      return `nova.navigation.${this.item.key}.collapsed`
    },
  },
}
