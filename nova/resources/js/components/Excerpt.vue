<template>
  <div v-if="shouldShow && hasContent">
    <div
      class="prose prose-sm dark:prose-invert"
      :class="{ 'whitespace-pre-wrap': plainText }"
      v-html="content"
    />
  </div>
  <div v-else-if="hasContent">
    <div
      v-if="expanded"
      class="prose prose-sm dark:prose-invert"
      :class="{ 'whitespace-pre-wrap': plainText }"
      v-html="content"
    />

    <button
      type="button"
      v-if="!shouldShow"
      @click="toggle"
      class="link-default"
      :class="{ 'mt-6': expanded }"
      aria-role="button"
      tabindex="0"
    >
      {{ showHideLabel }}
    </button>
  </div>
  <div v-else>&mdash;</div>
</template>

<script>
export default {
  props: {
    plainText: {
      type: Boolean,
      default: false,
    },
    shouldShow: {
      type: Boolean,
      default: false,
    },
    content: {
      type: String,
    },
  },

  data: () => ({ expanded: false }),

  methods: {
    toggle() {
      this.expanded = !this.expanded
    },
  },

  computed: {
    hasContent() {
      return this.content !== '' && this.content !== null
    },

    showHideLabel() {
      return !this.expanded ? this.__('Show Content') : this.__('Hide Content')
    },
  },
}
</script>
