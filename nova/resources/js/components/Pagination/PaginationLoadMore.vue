<template>
  <div
    class="bg-20 h-9 px-3 text-center rounded-b-lg flex items-center justify-between"
  >
    <p class="leading-normal text-sm text-gray-500">{{ resourceCountLabel }}</p>

    <p v-if="allResourcesLoaded" class="leading-normal text-sm">
      {{ __('All resources loaded.') }}
    </p>

    <button
      v-else
      @click="loadMore"
      class="h-9 focus:outline-none focus:ring ring-inset rounded-lg px-4 font-bold text-primary-500 hover:text-primary-600 active:text-primary-400"
    >
      {{ buttonLabel }}
    </button>

    <p class="leading-normal text-sm text-gray-500">
      {{ __(':amount Total', { amount: resourceTotalCountLabel }) }}
    </p>
  </div>
</template>

<script>
export default {
  emits: ['load-more'],

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
    perPage: {
      type: [Number, String],
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

  methods: {
    loadMore() {
      this.$emit('load-more')
    },
  },

  computed: {
    buttonLabel() {
      return this.__('Load :perPage More', {
        perPage: Nova.formatNumber(this.perPage),
      })
    },

    allResourcesLoaded() {
      return this.currentResourceCount == this.allMatchingResourceCount
    },

    resourceTotalCountLabel() {
      return Nova.formatNumber(this.allMatchingResourceCount)
    },
  },
}
</script>
