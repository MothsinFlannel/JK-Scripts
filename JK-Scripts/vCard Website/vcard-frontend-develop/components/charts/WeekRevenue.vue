<template>
  <b-card header="Revenue" class="mb-4 shadow-sm" no-body>
    <b-list-group flush>
      <b-list-group-item>
        <canvas ref="chart-canvas"></canvas>
      </b-list-group-item>

      <b-list-group-item>
        <div class="d-flex justify-content-around">
          <div>
            <div class="week-revenue__price">
              {{ $getFormattedPrice(todayRevenue) }}
            </div>
            <div>Today's Revenue</div>
          </div>

          <div>
            <div class="week-revenue__price">
              {{ $getFormattedPrice(thisWeekRevenue) }}
            </div>
            <div class="d-flex align-items-center">
              <div
                class="mr-1"
                style="
                  width: 0.75rem;
                  height: 0.75rem;
                  background-color: #acb4ff;
                  border: 1px solid #5969ff;
                "
              ></div>
              This Week
            </div>
          </div>

          <div>
            <div class="week-revenue__price">
              {{ $getFormattedPrice(lastWeekRevenue) }}
            </div>
            <div class="d-flex align-items-center">
              <div
                class="mr-1"
                style="
                  width: 0.75rem;
                  height: 0.75rem;
                  background-color: #ff9fbd;
                  border: 1px solid #ff407b;
                "
              ></div>
              Last Week
            </div>
          </div>
        </div>
      </b-list-group-item>
    </b-list-group>
  </b-card>
</template>

<script>
import Chart from 'chart.js'

Chart.defaults.global.defaultFontColor = '#71748d'
Chart.defaults.global.defaultFontFamily = "'Circular Std Book', serif"

export default {
  props: {
    revenue: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      chart: null,
    }
  },
  computed: {
    todayRevenue() {
      if (this.revenue.thisWeek) {
        return this.revenue.thisWeek[this.$dayjs().day()]
      }

      return 0
    },
    thisWeekRevenue() {
      if (this.revenue.thisWeek) {
        return this.revenue.thisWeek.reduce((result, value) => {
          return result + value
        }, 0)
      }

      return 0
    },
    lastWeekRevenue() {
      if (this.revenue.lastWeek) {
        return this.revenue.lastWeek.reduce((result, value) => {
          return result + value
        }, 0)
      }

      return 0
    },
  },
  mounted() {
    this.chart = new Chart(this.$refs['chart-canvas'], {
      type: 'line',
      data: {
        labels: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        datasets: [
          {
            label: 'This Week',
            fill: true,
            backgroundColor: '#acb4ff',
            borderColor: '#5969ff',
            borderWidth: 1,
            pointBackgroundColor: '#5969ff',
            data: this.revenue.thisWeek,
            cubicInterpolationMode: 'monotone',
          },
          {
            label: 'Last Week',
            fill: true,
            backgroundColor: '#ff9fbd',
            borderColor: '#ff407b',
            borderWidth: 1,
            pointBackgroundColor: '#ff407b',
            data: this.revenue.lastWeek,
            cubicInterpolationMode: 'monotone',
          },
        ],
      },
      options: {
        tooltips: {
          callbacks: {
            label(tooltipItem, data) {
              let label = data.datasets[tooltipItem.datasetIndex].label || ''

              if (label) {
                label += ': '
              }

              const formattedValue = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
              }).format(tooltipItem.yLabel)

              label += `${formattedValue}`
              return label
            },
          },
        },
        legend: {
          position: 'bottom',
        },
        scales: {
          yAxes: [
            {
              ticks: {
                callback: (value) => `$${value}`,
              },
            },
          ],
        },
      },
    })
  },
  beforeDestroy() {
    if (this.chart) {
      this.chart.destroy()
    }
  },
}
</script>

<style lang="scss" scoped>
.week-revenue {
  &__price {
    font-family: 'Circular Std Medium', serif;
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 8px;
    line-height: 1.2;
    color: #3d405c;

    @media (max-width: 767px) {
      font-size: 1.5rem;
    }
  }
}
</style>
