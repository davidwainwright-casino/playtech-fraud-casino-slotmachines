<template>
  <div class="bg-20 rounded-b-lg">
    <nav class="flex justify-between items-center">
      <!-- Previous Link -->
      <button
        :disabled="!hasPreviousPages || linksDisabled"
        class="text-xs font-bold py-3 px-4 focus:outline-none rounded-bl-lg focus:ring focus:ring-inset"
        :class="{
          'text-primary-500 hover:text-primary-400 active:text-primary-600':
            hasPreviousPages,
          'text-gray-300 dark:text-gray-600':
            !hasPreviousPages || linksDisabled,
        }"
        rel="prev"
        @click.prevent="selectPreviousPage"
        dusk="previous"
      >
        {{ __('Previous') }}
      </button>

      <slot />

      <!-- Next Link -->
      <button
        :disabled="!hasMorePages || linksDisabled"
        class="text-xs font-bold py-3 px-4 focus:outline-none rounded-br-lg focus:ring focus:ring-inset"
        :class="{
          'text-primary-500 hover:text-primary-400 active:text-primary-600':
            hasMorePages,
          'text-gray-300 dark:text-gray-600': !hasMorePages || linksDisabled,
        }"
        rel="next"
        @click.prevent="selectNextPage"
        dusk="next"
      >
        {{ __('Next') }}
      </button>
    </nav>
  </div>
</template>

<script>
export default {
  emits: ['page'],

  props: {
    currentResourceCount: {
      type: Number,
      required: true,
    },
    allMatchingResourceCount: {
      type: Number,
      required: true,
    },
    resourceCountLabel: {
      type: String,
      required: true,
    },
    page: {
      type: Number,
      required: true,
    },
    pages: {
      type: Number,
      default: 0,
    },
    next: {
      type: Boolean,
      default: false,
    },
    previous: {
      type: Boolean,
      default: false,
    },
  },

  data: () => ({ linksDisabled: false }),

  mounted() {
    Nova.$on('resources-loaded', this.listenToResourcesLoaded)
  },

  beforeUnmount() {
    Nova.$off('resources-loaded', this.listenToResourcesLoaded)
  },

  methods: {
    /**
     * Select the previous page.
     */
    selectPreviousPage() {
      this.selectPage(this.page - 1)
    },

    /**
     * Select the next page.
     */
    selectNextPage() {
      this.selectPage(this.page + 1)
    },

    /**
     * Select the page.
     */
    selectPage(page) {
      this.linksDisabled = true
      this.$emit('page', page)
    },

    listenToResourcesLoaded() {
      this.linksDisabled = false
    },
  },

  computed: {
    /**
     * Determine if prior pages are available.
     */
    hasPreviousPages: function () {
      return this.previous
    },

    /**
     * Determine if more pages are available.
     */
    hasMorePages: function () {
      return this.next
    },
  },
}
</script>
