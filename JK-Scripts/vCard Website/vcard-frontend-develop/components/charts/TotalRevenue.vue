<template>
  <b-card
    :header="`${
      typeof locationId === 'number' ? 'Location Revenue' : 'Total Revenue'
    }: ${since} — ${until}`"
    class="mb-4 shadow-sm"
  >
    <b-list-group flush>
      <b-list-group-item>
        <b-alert :show="typeof error === 'string'" variant="danger">
          {{ error }}
        </b-alert>

        <canvas ref="chart-canvas"></canvas>
      </b-list-group-item>

      <b-list-group-item>
        <div class="d-flex justify-content-around">
          <div>
            <h2>{{ $getFormattedPrice(totalRevenue) }}</h2>
            <div>Revenue</div>
          </div>

          <div>
            <h2>{{ $getFormattedPrice(operatorProfit) }}</h2>
            <div>Operator Profit</div>
          </div>
        </div>
      </b-list-group-item>
    </b-list-group>

    <div
      v-if="isLoading"
      class="d-flex align-items-center justify-content-center position-absolute"
      style="
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-color: rgba(255, 255, 255, 0.5);
        z-index: 99;
      "
    >
      <b-spinner variant="primary" label="Loading..." />
    </div>
  </b-card>
</template>

<script>
import Chart from 'chart.js'

Chart.defaults.global.defaultFontColor = '#71748d'
Chart.defaults.global.defaultFontFamily = "'Circular Std Book', serif"

export default {
  props: {
    locationId: {
      type: Number,
      default: null,
    },
  },
  data() {
    return {
      error: null,
      isLoading: false,
      chart: null,
      chartData: null,
      operatorProfit: null,
    }
  },
  computed: {
    since() {
      if (this.$route.query.since) {
        return this.$dayjs(this.$route.query.since).format('MM/DD/YYYY')
      }

      return null
    },
    until() {
      if (this.$route.query.until) {
        return this.$dayjs(this.$route.query.until).format('MM/DD/YYYY')
      }

      return null
    },
    totalRevenue() {
      if (this.chartData) {
        return this.chartData.reduce((result, value) => {
          return result + value.y
        }, 0)
      }

      return 0
    },
  },
  watch: {
    '$route.query.since'() {
      this.getRevenueChart()
    },
    '$route.query.until'() {
      this.getRevenueChart()
    },
    chartData(data) {
      this.chart.data.datasets[0].data = data

      this.chart.update()
    },
  },
  mounted() {
    this.chart = new Chart(this.$refs['chart-canvas'], {
      type: 'bar',
      data: {
        datasets: [
          {
            backgroundColor: '#ff9fbd',
            borderColor: '#ff407b',
            borderWidth: 1,
            data: this.chartData,
          },
        ],
      },
      options: {
        legend: {
          display: false,
        },
        scales: {
          xAxes: [
            {
              type: 'time',
              time: {
                unit: 'day',
                displayFormats: {
                  day: 'MM/DD/YYYY',
                },
              },
              offset: true,
            },
          ],
          yAxes: [
            {
              ticks: {
                beginAtZero: true,
                callback: (value) => `$${value}`,
              },
            },
          ],
        },
      },
    })

    this.getRevenueChart()
  },
  beforeDestroy() {
    if (this.chart) {
      this.chart.destroy()
    }
  },
  methods: {
    async getRevenueChart() {
      try {
        this.error = null

        this.isLoading = true

        const { data } = await this.$axios.post('/app/charts/revenue', {
          since: this.$route.query.since
            ? this.$dayjs(this.$route.query.since).format('YYYY-MM-DD')
            : null,
          until: this.$route.query.until
            ? this.$dayjs(this.$route.query.until).format('YYYY-MM-DD')
            : null,
          filters: {
            locationId: this.locationId,
          },
        })

        if (data.success) {
          this.chartData = data.results
          this.operatorProfit = data.operatorProfit
        }

        this.isLoading = false
      } catch (error) {
        this.error = this.$getErrorMessage(error)

        this.isLoading = false
      }
    },
  },
}
</script>
