<template>
  <LoadingCard :loading="loading" class="px-6 py-4">
    <div class="h-6 flex items-center mb-4">
      <h3 class="mr-3 leading-tight text-sm font-bold">{{ title }}</h3>

      <HelpTextTooltip :text="helpText" :width="helpWidth" />

      <SelectControl
        v-if="ranges.length > 0"
        class="ml-auto w-[6rem] flex-shrink-0"
        size="xxs"
        :options="ranges"
        v-model:selected="selectedRangeKey"
        @change="handleChange"
        :aria-label="__('Select Ranges')"
      />
    </div>

    <p class="flex items-center text-4xl mb-4">
      {{ formattedValue }}
      <span v-if="suffix" class="ml-2 text-sm font-bold">{{
        formattedSuffix
      }}</span>
    </p>

    <div
      ref="chart"
      class="absolute inset-0 rounded-b-lg ct-chart"
      style="top: 60%"
    />
  </LoadingCard>
</template>

<script>
import debounce from 'lodash/debounce'
import Chartist from 'chartist'
import 'chartist-plugin-tooltips'
import 'chartist/dist/chartist.min.css'
import { singularOrPlural } from '@/util'
import 'chartist-plugin-tooltips/dist/chartist-plugin-tooltip.css'

export default {
  name: 'BaseTrendMetric',

  emits: ['selected'],

  props: {
    loading: Boolean,
    title: {},
    helpText: {},
    helpWidth: {},
    value: {},
    chartData: {},
    maxWidth: {},
    prefix: '',
    suffix: '',
    suffixInflection: { type: Boolean, default: true },
    ranges: { type: Array, default: () => [] },
    selectedRangeKey: [String, Number],
    format: {
      type: String,
      default: '0[.]00a',
    },
  },

  data: () => ({
    chartist: null,
    resizeObserver: null,
  }),

  watch: {
    selectedRangeKey: function (newRange, oldRange) {
      this.renderChart()
    },

    chartData: function (newData, oldData) {
      this.renderChart()
    },
  },

  created() {
    const debouncer = debounce(callback => callback(), Nova.config('debounce'))

    this.resizeObserver = new ResizeObserver(entries => {
      debouncer(() => {
        this.renderChart()
      })
    })
  },

  mounted() {
    const low = Math.min(...this.chartData)
    const high = Math.max(...this.chartData)

    // Use zero as the graph base if the lowest value is greater than or equal to zero.
    // This avoids the awkward situation where the chart doesn't appear filled in.
    const areaBase = low >= 0 ? 0 : low

    this.chartist = new Chartist.Line(this.$refs.chart, this.chartData, {
      lineSmooth: Chartist.Interpolation.none(),
      fullWidth: true,
      showPoint: true,
      showLine: true,
      showArea: true,
      chartPadding: {
        top: 10,
        right: 0,
        bottom: 0,
        left: 0,
      },
      low,
      high,
      areaBase,
      axisX: {
        showGrid: false,
        showLabel: true,
        offset: 0,
      },
      axisY: {
        showGrid: false,
        showLabel: true,
        offset: 0,
      },
      plugins: [
        Chartist.plugins.tooltip({
          anchorToPoint: true,
          transformTooltipTextFnc: value => {
            let formattedValue = Nova.formatNumber(
              new String(value),
              this.format
            )

            if (this.prefix) {
              return `${this.prefix}${formattedValue}`
            }

            if (this.suffix) {
              const suffix = this.suffixInflection
                ? singularOrPlural(value, this.suffix)
                : this.suffix

              return `${formattedValue} ${suffix}`
            }

            return `${formattedValue}`
          },
        }),
      ],
    })

    this.resizeObserver.observe(this.$refs.chart)
  },

  beforeUnmount() {
    this.resizeObserver.unobserve(this.$refs.chart)
  },

  methods: {
    renderChart() {
      this.chartist.update(this.chartData)
    },

    handleChange(event) {
      const value = event?.target?.value || event

      this.$emit('selected', value)
    },
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
  },
}
</script>
