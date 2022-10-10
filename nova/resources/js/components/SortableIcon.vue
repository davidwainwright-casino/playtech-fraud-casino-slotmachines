<template>
  <button
    type="button"
    @click.prevent="handleClick"
    class="cursor-pointer inline-flex items-center focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 rounded"
    :dusk="'sort-' + uriKey"
    :aria-sort="ariaSort"
  >
    <span
      class="inline-flex font-sans font-bold uppercase text-xxs tracking-wide text-gray-500"
    >
      <slot />
    </span>

    <svg
      class="ml-2 flex-shrink-0"
      xmlns="http://www.w3.org/2000/svg"
      width="8"
      height="14"
      viewBox="0 0 8 14"
    >
      <path
        :class="descClass"
        d="M1.70710678 4.70710678c-.39052429.39052429-1.02368927.39052429-1.41421356 0-.3905243-.39052429-.3905243-1.02368927 0-1.41421356l3-3c.39052429-.3905243 1.02368927-.3905243 1.41421356 0l3 3c.39052429.39052429.39052429 1.02368927 0 1.41421356-.39052429.39052429-1.02368927.39052429-1.41421356 0L4 2.41421356 1.70710678 4.70710678z"
      />
      <path
        :class="ascClass"
        d="M6.29289322 9.29289322c.39052429-.39052429 1.02368927-.39052429 1.41421356 0 .39052429.39052429.39052429 1.02368928 0 1.41421358l-3 3c-.39052429.3905243-1.02368927.3905243-1.41421356 0l-3-3c-.3905243-.3905243-.3905243-1.02368929 0-1.41421358.3905243-.39052429 1.02368927-.39052429 1.41421356 0L4 11.5857864l2.29289322-2.29289318z"
      />
    </svg>
  </button>
</template>

<script>
import { RouteParameters } from '@/mixins'

export default {
  emits: ['sort', 'reset'],

  mixins: [RouteParameters],

  props: {
    resourceName: String,
    uriKey: String,
  },

  methods: {
    /**
     * Handle the clicke event.
     */
    handleClick() {
      if (this.isSorted && this.isDescDirection) {
        this.$emit('reset')
      } else {
        this.$emit('sort', {
          key: this.uriKey,
          direction: this.direction,
        })
      }
    },
  },

  computed: {
    /**
     * Determine if the sorting direction is descending.
     */
    isDescDirection() {
      return this.direction == 'desc'
    },

    /**
     * Determine if the sorting direction is ascending.
     */
    isAscDirection() {
      return this.direction == 'asc'
    },

    /**
     * The CSS class to apply to the ascending arrow icon
     */
    ascClass() {
      if (this.isSorted && this.isDescDirection) {
        return 'fill-gray-500 dark:fill-gray-300'
      }

      return 'fill-gray-300 dark:fill-gray-500'
    },

    /**
     * The CSS class to apply to the descending arrow icon
     */
    descClass() {
      if (this.isSorted && this.isAscDirection) {
        return 'fill-gray-500 dark:fill-gray-300'
      }

      return 'fill-gray-300 dark:fill-gray-500'
    },

    /**
     * Determine whether this column is being sorted
     */
    isSorted() {
      return (
        this.sortColumn == this.uriKey &&
        ['asc', 'desc'].includes(this.direction)
      )
    },

    /**
     * The current order query parameter for this resource
     */
    sortKey() {
      return `${this.resourceName}_order`
    },

    /**
     * The current order query parameter value
     */
    sortColumn() {
      return this.route.params[this.sortKey]
    },

    /**
     * The current direction query parameter for this resource
     */
    directionKey() {
      return `${this.resourceName}_direction`
    },

    /**
     * The current direction query parameter value
     */
    direction() {
      return this.route.params[this.directionKey]
    },

    /**
     * Determine whether this column is not being sorted
     */
    notSorted() {
      return !!!this.isSorted
    },

    /**
     * The current `aria-sort` value
     */
    ariaSort() {
      if (this.isDescDirection) {
        return 'descending'
      } else if (this.isAscDirection) {
        return 'ascending'
      }

      return 'none'
    },
  },
}
</script>
