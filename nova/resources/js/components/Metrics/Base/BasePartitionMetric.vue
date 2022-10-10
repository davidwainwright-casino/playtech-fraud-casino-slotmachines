<template>
  <LoadingCard :loading="loading" class="px-6 py-4">
    <h3 class="h-6 flex mb-3 text-sm font-bold">
      {{ title }}

      <span class="ml-auto font-semibold text-gray-400 text-xs"
        >({{ formattedTotal }} {{ __('total') }})</span
      >
    </h3>

    <HelpTextTooltip :text="helpText" :width="helpWidth" />

    <div class="min-h-[90px]">
      <div class="overflow-hidden overflow-y-auto max-h-[90px]">
        <ul>
          <li
            v-for="item in formattedItems"
            :key="item.color"
            class="text-xs leading-normal"
          >
            <span
              class="inline-block rounded-full w-2 h-2 mr-2"
              :style="{
                backgroundColor: item.color,
              }"
            />{{ item.label }} ({{ item.value }} - {{ item.percentage }}%)
          </li>
        </ul>
      </div>

      <div
        ref="chart"
        class="right-[20px]"
        :class="chartClasses"
        style="width: 90px; height: 90px; bottom: 30px; top: calc(50% + 15px)"
      />
    </div>
  </LoadingCard>
</template>

<script>
import map from 'lodash/map'
import sumBy from 'lodash/sumBy'
import Chartist from 'chartist'
import 'chartist/dist/chartist.min.css'

const colorForIndex = index =>
  [
    '#F5573B',
    '#F99037',
    '#F2CB22',
    '#8FC15D',
    '#098F56',
    '#47C1BF',
    '#1693EB',
    '#6474D7',
    '#9C6ADE',
    '#E471DE',
  ][index]

export default {
  name: 'BasePartitionMetric',

  props: {
    loading: Boolean,
    title: String,
    helpText: {},
    helpWidth: {},
    chartData: Array,
  },

  data: () => ({ chartist: null }),

  watch: {
    chartData: function (newData, oldData) {
      this.renderChart()
    },
  },

  mounted() {
    this.chartist = new Chartist.Pie(
      this.$refs.chart,
      this.formattedChartData,
      {
        donut: true,
        donutWidth: 10,
        donutSolid: true,
        startAngle: 270,
        showLabel: false,
      }
    )

    this.chartist.on('draw', context => {
      if (context.type === 'slice') {
        context.element.attr({
          style: `fill: ${context.meta.color} !important`,
        })
      }
    })
  },

  methods: {
    renderChart() {
      this.chartist.update(this.formattedChartData)
    },

    getItemColor(item, index) {
      return typeof item.color === 'string' ? item.color : colorForIndex(index)
    },
  },

  computed: {
    chartClasses() {
      return [
        'vertical-center',
        'rounded-b-lg',
        'ct-chart',
        'mr-4',
        this.currentTotal <= 0 ? 'invisible' : '',
      ]
    },

    formattedChartData() {
      return { labels: this.formattedLabels, series: this.formattedData }
    },

    formattedItems() {
      return map(this.chartData, (item, index) => {
        return {
          label: item.label,
          value: Nova.formatNumber(item.value),
          color: this.getItemColor(item, index),
          percentage: Nova.formatNumber(String(item.percentage)),
        }
      })
    },

    formattedLabels() {
      return map(this.chartData, item => item.label)
    },

    formattedData() {
      return map(this.chartData, (item, index) => {
        return {
          value: item.value,
          meta: { color: this.getItemColor(item, index) },
        }
      })
    },

    formattedTotal() {
      let total = this.currentTotal.toFixed(2)
      let roundedTotal = Math.round(total)

      if (roundedTotal.toFixed(2) == total) {
        return Nova.formatNumber(new String(roundedTotal))
      }

      return Nova.formatNumber(new String(total))
    },

    currentTotal() {
      return sumBy(this.chartData, 'value')
    },
  },
}
</script>
