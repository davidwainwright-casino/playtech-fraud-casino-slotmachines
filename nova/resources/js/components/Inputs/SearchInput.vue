<template>
  <div
    v-bind="$attrs"
    :data-testid="dataTestid"
    :dusk="dataTestid"
    :class="{ 'opacity-75': disabled }"
    ref="searchInputContainer"
  >
    <div class="relative">
      <div
        ref="input"
        @click.stop="open"
        @focus="open"
        @keydown.down.prevent="open"
        @keydown.up.prevent="open"
        :class="{
          'ring dark:border-gray-500 dark:ring-gray-700': show,
          'form-input-border-error': error,
          disabled,
        }"
        class="relative flex items-center form-control form-input-bordered form-select pr-6"
        :tabindex="show ? -1 : 0"
        :aria-expanded="show === true ? 'true' : 'false'"
        :dusk="`${dataTestid}-selected`"
      >
        <IconArrow
          v-if="shouldShowDropdownArrow && !disabled"
          class="pointer-events-none form-select-arrow"
        />

        <slot name="default">
          <div class="text-gray-400 dark:text-gray-400">
            {{ __('Click to choose') }}
          </div>
        </slot>
      </div>

      <button
        type="button"
        @click.stop="clear"
        v-if="!shouldShowDropdownArrow && !disabled"
        tabindex="-1"
        class="absolute p-2 inline-block right-[4px]"
        style="top: 6px"
        :dusk="`${dataTestid}-clear-button`"
      >
        <svg
          class="block fill-current icon h-2 w-2"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="278.046 126.846 235.908 235.908"
        >
          <path
            d="M506.784 134.017c-9.56-9.56-25.06-9.56-34.62 0L396 210.18l-76.164-76.164c-9.56-9.56-25.06-9.56-34.62 0-9.56 9.56-9.56 25.06 0 34.62L361.38 244.8l-76.164 76.165c-9.56 9.56-9.56 25.06 0 34.62 9.56 9.56 25.06 9.56 34.62 0L396 279.42l76.164 76.165c9.56 9.56 25.06 9.56 34.62 0 9.56-9.56 9.56-25.06 0-34.62L430.62 244.8l76.164-76.163c9.56-9.56 9.56-25.06 0-34.62z"
          />
        </svg>
      </button>
    </div>

    <div
      v-if="show"
      ref="dropdown"
      class="rounded-lg px-0 bg-white dark:bg-gray-900 shadow border border-gray-200 dark:border-gray-700 absolute top-0 left-0 my-1 overflow-hidden"
      :style="{ width: inputWidth + 'px', zIndex: 2000 }"
    >
      <!-- Search Input -->
      <input
        :disabled="disabled"
        v-model="search"
        ref="search"
        @keydown.enter.prevent="chooseSelected"
        @keydown.down.prevent="move(1)"
        @keydown.up.prevent="move(-1)"
        class="h-10 outline-none w-full px-3 text-sm leading-normal bg-white dark:bg-gray-700 rounded-t border-b border-gray-200 dark:border-gray-800"
        tabindex="-1"
        type="search"
        :placeholder="__('Search')"
        spellcheck="false"
      />

      <!-- Search Results -->
      <div
        ref="container"
        class="relative overflow-y-scroll text-sm"
        tabindex="-1"
        style="max-height: 155px"
        :dusk="`${dataTestid}-results`"
      >
        <div
          v-for="(option, index) in data"
          :dusk="`${dataTestid}-result-${index}`"
          :key="getTrackedByKey(option)"
          :ref="index === selected ? 'selected' : null"
          @click.stop="choose(option)"
          class="px-3 py-1.5 cursor-pointer"
          :class="{
            'border-t border-gray-100 dark:border-gray-700': index != 0,
            [`search-input-item-${index}`]: true,
            'hover:bg-gray-100 dark:hover:bg-gray-800': index !== selected,
            'bg-primary-500 text-white dark:text-gray-900': index === selected,
          }"
        >
          <slot name="option" :option="option" :selected="index === selected" />
        </div>
      </div>
    </div>

    <teleport to="body">
      <div
        @click="show = !show"
        v-show="show"
        class="absolute inset-0 z-[35]"
      />
    </teleport>
  </div>
</template>

<script>
import debounce from 'lodash/debounce'
import findIndex from 'lodash/findIndex'
import get from 'lodash/get'
import { createPopper } from '@popperjs/core'
import { mapProps } from '@/mixins'

export default {
  emits: ['clear', 'input', 'selected'],

  inheritAttrs: false,

  props: {
    dataTestid: {},
    disabled: { default: false },
    value: {},
    data: {},
    trackBy: {},
    error: {
      type: Boolean,
      default: false,
    },
    boundary: {},
    debounce: {
      type: Number,
      default: 500,
    },
    clearable: {
      type: Boolean,
      default: true,
    },
    ...mapProps(['mode']),
  },

  data: () => ({
    debouncer: null,
    show: false,
    search: '',
    selected: 0,
    popper: null,
    inputWidth: null,
  }),

  watch: {
    search(search) {
      this.selected = 0
      if (this.$refs.container) {
        this.$refs.container.scrollTop = 0
      } else {
        this.$nextTick(() => {
          this.$refs.container.scrollTop = 0
        })
      }

      this.debouncer(() => {
        this.$emit('input', search)
      })
    },

    show(show) {
      if (show) {
        let selected = findIndex(this.data, [
          this.trackBy,
          get(this.value, this.trackBy),
        ])
        if (selected !== -1) this.selected = selected
        this.inputWidth = this.$refs.input.offsetWidth

        this.$nextTick(() => {
          this.popper = createPopper(this.$refs.input, this.$refs.dropdown, {
            placement: 'bottom-start',
            onFirstUpdate: state => {
              this.$refs.container.scrollTop = this.$refs.container.scrollHeight
              this.updateScrollPosition()
              this.$refs.search.focus()
            },
          })
        })
      } else {
        if (this.popper) this.popper.destroy()
      }
    },
  },

  created() {
    this.debouncer = debounce(callback => callback(), this.debounce)
  },

  mounted() {
    document.addEventListener('keydown', this.handleEscape)

    if (this.mode === 'modal') {
      document.addEventListener('click', this.handleOutsideClick)
    }
  },

  beforeUnmount() {
    document.removeEventListener('keydown', this.handleEscape)

    if (this.mode === 'modal') {
      document.removeEventListener('click', this.handleOutsideClick)
    }
  },

  methods: {
    handleEscape(e) {
      // 'tab' or 'escape'
      if (this.show && (e.keyCode == 9 || e.keyCode == 27)) {
        setTimeout(() => this.close(), 50)
      }
    },

    getTrackedByKey(option) {
      return get(option, this.trackBy)
    },

    open() {
      if (!this.disabled) {
        this.show = true
        this.search = ''
      }
    },

    close() {
      this.show = false
    },

    clear() {
      if (!this.disabled) {
        this.selected = null
        this.$emit('clear', null)
      }
    },

    move(offset) {
      let newIndex = this.selected + offset

      if (newIndex >= 0 && newIndex < this.data.length) {
        this.selected = newIndex
        this.updateScrollPosition()
      }
    },

    updateScrollPosition() {
      this.$nextTick(() => {
        if (this.$refs.selected) {
          if (
            this.$refs.selected[0].offsetTop >
            this.$refs.container.scrollTop +
              this.$refs.container.clientHeight -
              this.$refs.selected[0].clientHeight
          ) {
            this.$refs.container.scrollTop =
              this.$refs.selected[0].offsetTop +
              this.$refs.selected[0].clientHeight -
              this.$refs.container.clientHeight
          }

          if (
            this.$refs.selected[0].offsetTop < this.$refs.container.scrollTop
          ) {
            this.$refs.container.scrollTop = this.$refs.selected[0].offsetTop
          }
        }
      })
    },

    chooseSelected(event) {
      if (event.isComposing || event.keyCode === 229) return

      if (this.data[this.selected] !== undefined) {
        this.$emit('selected', this.data[this.selected])
        this.$refs.input.focus()
        this.$nextTick(() => this.close())
      }
    },

    choose(option) {
      this.selected = findIndex(this.data, [
        this.trackBy,
        get(option, this.trackBy),
      ])
      this.$emit('selected', option)
      this.$refs.input.focus()
      this.$nextTick(() => this.close())
    },

    /**
     * Handle closing the dropdown.
     */
    handleOutsideClick(e) {
      if (
        !(
          this.$refs.searchInputContainer &&
          (this.$refs.searchInputContainer == e.target ||
            this.$refs.searchInputContainer.contains(e.target))
        )
      ) {
        this.close()
      }
    },
  },

  computed: {
    shouldShowDropdownArrow() {
      return this.value == '' || this.value == null || !this.clearable
    },
  },
}
</script>
