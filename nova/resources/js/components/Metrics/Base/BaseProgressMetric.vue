<template>
  <LoadingCard :loading="loading" class="flex flex-col px-6 py-4">
    <div class="h-6 flex items-center mb-4">
      <h3 class="flex-1 mr-3 leading-tight text-sm font-bold">{{ title }}</h3>

      <HelpTextTooltip :text="helpText" :width="helpWidth" />

      <div class="flex-none text-right">
        <span class="text-gray-500 font-medium inline-block">
          {{ formattedValue }}
          <span v-if="suffix" class="text-sm">{{ formattedSuffix }}</span>
        </span>
      </div>
    </div>

    <p class="flex items-center text-4xl mb-4">{{ percentage }}%</p>

    <div class="flex h-full justify-center items-center flex-grow-1 mb-4">
      <div
        class="bg-gray-200 dark:bg-gray-900 w-full overflow-hidden h-4 flex rounded-full"
        :title="formattedValue"
      >
        <div :class="bgClass" :style="`width:${percentage}%`" />
      </div>
    </div>
  </LoadingCard>
</template>

<script>
import { singularOrPlural } from '@/util'

export default {
  name: 'BaseProgressMetric',

  props: {
    loading: { default: true },
    title: {},
    helpText: {},
    helpWidth: {},
    maxWidth: {},
    target: {},
    value: {},
    percentage: {},
    format: {
      type: String,
      default: '(0[.]00a)',
    },
    avoid: { type: Boolean, default: false },
    prefix: '',
    suffix: '',
    suffixInflection: { type: Boolean, default: true },
  },

  computed: {
    isNullValue() {
      return this.value == null
    },

    formattedValue() {
      if (!this.isNullValue) {
        const value = Nova.formatNumber(new String(this.value), this.format)

        return `${this.prefix}${value}`
      }

      return ''
    },

    formattedSuffix() {
      if (this.suffixInflection === false) {
        return this.suffix
      }

      return singularOrPlural(this.value, this.suffix)
    },

    bgClass() {
      if (this.avoid) {
        return this.percentage > 60 ? 'bg-yellow-500' : 'bg-green-300'
      }

      return this.percentage > 60 ? 'bg-green-500' : 'bg-yellow-300'
    },
  },
}
</script>
