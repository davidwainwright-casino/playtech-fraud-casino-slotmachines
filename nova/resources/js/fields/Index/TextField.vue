<template>
  <div :class="`text-${field.textAlign}`">
    <template v-if="fieldValue">
      <button
        v-if="fieldValue && field.copyable && !shouldDisplayAsHtml"
        @click.prevent.stop="copy"
        type="button"
        class="flex items-center hover:bg-gray-50 dark:hover:bg-gray-900 text-gray-500 dark:text-gray-400 hover:text-gray-500 active:text-gray-600 rounded-lg px-1 -mx-1"
        v-tooltip="__('Copy to clipboard')"
      >
        <span ref="theFieldValue">
          {{ fieldValue }}
        </span>

        <Icon
          class="text-gray-500 dark:text-gray-400 ml-1"
          :solid="true"
          type="clipboard"
          width="14"
        />
      </button>

      <span
        v-else-if="fieldValue && !field.copyable && !shouldDisplayAsHtml"
        class="text-90 whitespace-nowrap"
      >
        {{ fieldValue }}
      </span>
      <div
        @click.stop
        v-else-if="fieldValue && !field.copyable && shouldDisplayAsHtml"
        v-html="fieldValue"
      />
      <p v-else>&mdash;</p>
    </template>
    <p v-else>&mdash;</p>
  </div>
</template>

<script>
import { CopiesToClipboard, FieldValue } from '@/mixins'

export default {
  mixins: [CopiesToClipboard, FieldValue],

  props: ['resourceName', 'field'],

  methods: {
    copy() {
      this.copyValueToClipboard(this.field.value)
    },
  },
}
</script>
