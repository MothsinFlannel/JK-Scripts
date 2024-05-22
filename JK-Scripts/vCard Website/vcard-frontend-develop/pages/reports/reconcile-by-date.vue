<template>
  <div>
    <div class="mb-4">
      <h3 class="mb-0">Reconcile By Date Report</h3>

      <hr class="my-2" />

      <Breadcrumb
        :items="[
          { title: 'vCard Dashboard', link: '/' },
          {
            title: 'Reconcile By Date Report',
          },
        ]"
      />
    </div>

    <b-card class="mb-4 shadow-sm" header-class="pt-1">
      <template #header>
        <div
          class="d-flex align-items-baseline justify-content-between flex-wrap"
        >
          <span class="mt-2">
            {{ `Reconcile By Date Report: ${since} — ${until}` }}
          </span>
          <div
            class="d-flex align-items-baseline text-nowrap flex-wrap flex-sm-row flex-column p-0"
            style="font-family: 'Circular Std Book', serif; color: #71748d;"
          >
            <OldLocationSelector only-live-locations class="mt-2 mr-3" />

            <div
              class="d-flex align-items-center mr-3 flex-sm-nowrap flex-wrap mt-2"
            >
              <span class="mr-2">
                Select a date range:
              </span>
              <DatePicker
                :value="dateRangeValue"
                :editable="false"
                :clearable="false"
                :range="true"
                format="MM/DD/YYYY"
                type="date"
                placeholder="Select a date range"
                style="width: 210px;"
                @input="onDateRangeChanged"
              />
            </div>
          </div>
        </div>
      </template>
      <b-alert :show="typeof error === 'string'" variant="danger">
        {{ error }}
      </b-alert>
      <b-alert :show="devices === null" variant="info" class="mb-3">
        Click the run to receive the report.
      </b-alert>
      <div
        class="d-flex align-items-baseline justify-content-between mb-3 flex-wrap"
      >
        <div class="row">
          <div v-if="devices !== null" class="col-12 d-flex flex-column">
            <span
              >Report Generated on
              {{ $dayjs(generatedOn).format('MM/DD/YYYY') }} at
              {{ $dayjs(generatedOn).format('HH:mm') }}</span
            >
          </div>
        </div>
        <div class="d-flex align-items-center align-self-end ml-auto">
          <b-button
            type="button"
            variant="outline-light"
            size="sm"
            @click="getReconcile"
          >
            Run
          </b-button>
          <b-button
            v-if="devices !== null"
            type="button"
            variant="outline-light"
            class="ml-2"
            size="sm"
            @click="exportReconcile"
          >
            Export
          </b-button>
          <b-button
            v-if="devices !== null"
            type="button"
            variant="outline-light"
            class="ml-2"
            size="sm"
            @click="printReconcile"
          >
            Print
          </b-button>
        </div>
      </div>

      <scroll-wrapper ref="scroll-wrapper">
        <div class="table-responsive">
          <table class="table b-table table-striped table-bordered">
            <thead class="thead-light text-uppercase">
              <tr>
                <th>Date</th>
                <th>Location</th>
                <th>City</th>
                <th>State</th>
                <th>Zip Code</th>
                <th>County</th>
                <th>Cash In</th>
                <th>Cash Out</th>
                <th>Net</th>
                <th>Location Split</th>
                <th>Operator Split</th>
                <th>Amount Due</th>
                <th>Amount Paid</th>
                <th>Balance Due</th>
              </tr>
            </thead>

            <tbody>
              <tr
                v-if="!devices || devices.length === 0"
                class="table-empty-row"
              >
                <td colspan="14">
                  <div class="text-center my-2">
                    No data available in table
                  </div>
                </td>
              </tr>

              <tr
                v-for="device in devices"
                :key="`${device.locationId}-${device.date}`"
                :class="{ 'location-inactive': !device.isActive }"
              >
                <td>
                  <span class="text-nowrap">{{ device.date }}</span>
                </td>
                <td>{{ device.name }}</td>
                <td>{{ device.city }}</td>
                <td class="text-uppercase">{{ device.state }}</td>
                <td>{{ device.zipCode }}</td>
                <td>{{ device.county }}</td>
                <td>{{ $getFormattedPrice(device.cashIn) }}</td>
                <td>{{ $getFormattedPrice(device.cashOut) }}</td>
                <td>{{ $getFormattedPrice(device.net) }}</td>
                <td>{{ $getFormattedPrice(device.locationSplit) }}</td>
                <td>{{ $getFormattedPrice(device.operatorSplit) }}</td>
                <td>{{ $getFormattedPrice(device.amountDue) }}</td>
                <td>{{ $getFormattedPrice(device.amountPaid) }}</td>
                <td>{{ $getFormattedPrice(device.balanceDue) }}</td>
              </tr>
            </tbody>

            <tfoot class="thead-white">
              <tr>
                <th class="text-right">Total:</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>
                  {{
                    devicesTotals && $getFormattedPrice(devicesTotals.cashIn)
                  }}
                </th>
                <th>
                  {{
                    devicesTotals && $getFormattedPrice(devicesTotals.cashOut)
                  }}
                </th>
                <th>
                  {{ devicesTotals && $getFormattedPrice(devicesTotals.net) }}
                </th>
                <th>
                  {{
                    devicesTotals &&
                    $getFormattedPrice(devicesTotals.locationSplit)
                  }}
                </th>
                <th>
                  {{
                    devicesTotals &&
                    $getFormattedPrice(devicesTotals.operatorSplit)
                  }}
                </th>
                <th>
                  {{
                    devicesTotals && $getFormattedPrice(devicesTotals.amountDue)
                  }}
                </th>
                <th>
                  {{
                    devicesTotals &&
                    $getFormattedPrice(devicesTotals.amountPaid)
                  }}
                </th>
                <th>
                  {{
                    devicesTotals &&
                    $getFormattedPrice(devicesTotals.balanceDue)
                  }}
                </th>
              </tr>
            </tfoot>
          </table>
        </div>
      </scroll-wrapper>

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
  </div>
</template>

<script>
import DatePicker from 'vue2-datepicker'
import { saveAs } from 'file-saver'

import Breadcrumb from '~/components/Breadcrumb'
import OldLocationSelector from '~/components/selectors/OldLocationSelector'
import ScrollWrapper from '~/components/scroll/ScrollWrapper.vue'

export default {
  components: {
    DatePicker,
    Breadcrumb,
    OldLocationSelector,
    ScrollWrapper,
  },
  beforeRouteUpdate(to, from, next) {
    if (!to.query.since || !to.query.until) {
      next({
        ...to,
        query: {
          ...to.query,
          since: this.$dayjs().utc().startOf('week').format('YYYY-MM-DD'),
          until: this.$dayjs().utc().endOf('week').format('YYYY-MM-DD'),
        },
      })
    } else {
      next()
    }
  },
  asyncData({ $dayjs, route, redirect }) {
    if (!route.query.since || !route.query.until) {
      redirect({
        ...route,
        query: {
          ...route.query,
          since: $dayjs().utc().startOf('week').format('YYYY-MM-DD'),
          until: $dayjs().utc().endOf('week').format('YYYY-MM-DD'),
        },
      })
    }
  },
  data() {
    return {
      isLoading: false,
      error: null,
      devices: null,
      generatedOn: this.$dayjs(),
      devicesCount: 0,
      devicesTotals: null,
      printIframe: null,
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
    daysCount() {
      if (this.$route.query.since && this.$route.query.until) {
        return (
          this.$dayjs(this.$route.query.until).diff(
            this.$dayjs(this.$route.query.since),
            'day'
          ) + 1
        )
      }

      return null
    },
    dateRangeValue() {
      return [
        this.$route.query.since
          ? this.$dayjs(this.$route.query.since, 'YYYY-MM-DD').toDate()
          : null,
        this.$route.query.until
          ? this.$dayjs(this.$route.query.until, 'YYYY-MM-DD').toDate()
          : null,
      ]
    },
    locationValue() {
      if (this.$route.query.location) {
        return +this.$route.query.location
      }

      return null
    },
  },
  watch: {
    '$route.query'() {
      if (this.devices) {
        this.getReconcile()
      }
    },
  },
  mounted() {
    window.addEventListener('message', this.onReadyToPrint, false)
  },
  beforeDestroy() {
    window.removeEventListener('message', this.onReadyToPrint, false)
  },
  methods: {
    async exportReconcile() {
      try {
        this.$nuxt.$loading.start()

        const { data } = await this.$axios.post(
          '/reports/inventory/reconcile',
          {
            filters: {
              locationId: this.$route.query.location
                ? +this.$route.query.location
                : null,
            },
            since: this.$route.query.since
              ? this.$dayjs(`${this.$route.query.since}`).format('YYYY-MM-DD')
              : null,
            until: this.$route.query.until
              ? this.$dayjs(`${this.$route.query.until}`).format('YYYY-MM-DD')
              : null,
            export: true,
          }
        )

        if (typeof data === 'string') {
          const blob = new Blob(data.split('\\n\\'), {
            type: 'text/csv;charset=utf-8',
          })

          saveAs(blob, 'reconcile-by-date.csv')
        }
      } catch (error) {
        console.error(error)
      } finally {
        this.$nuxt.$loading.finish()
      }
    },
    onReadyToPrint(event) {
      if (event.data === 'ready-to-print') {
        this.$nuxt.$loading.finish()

        try {
          this.printIframe.contentWindow.document.execCommand(
            'print',
            true,
            null
          )
        } catch {
          this.printIframe.contentWindow.print()
        }
      }
    },
    printReconcile() {
      if (this.printIframe) {
        window.document.body.removeChild(this.printIframe)
        this.printIframe = null
      }

      this.$nuxt.$loading.start()

      this.printIframe = document.createElement('iframe')

      this.printIframe.setAttribute(
        'src',
        `${window.origin}${this.$router.options.base}print/reports/reconcile-by-date${window.location.search}`
      )
      this.printIframe.setAttribute(
        'style',
        'visibility: hidden; position: absolute; left: 0; top: 0; height: 0; width: 0; border: none;'
      )

      window.document.body.appendChild(this.printIframe)
    },
    async getReconcile(isShowLoadingIndicator = true) {
      try {
        this.isLoading = isShowLoadingIndicator

        const requestQuery = window.location.search

        const { data } = await this.$axios.post(
          '/reports/inventory/reconcile',
          {
            filters: {
              locationId: this.$route.query.location
                ? +this.$route.query.location
                : null,
            },
            since: this.$route.query.since
              ? this.$dayjs(`${this.$route.query.since}`).format('YYYY-MM-DD')
              : null,
            until: this.$route.query.until
              ? this.$dayjs(`${this.$route.query.until}`).format('YYYY-MM-DD')
              : null,
            export: false,
          }
        )

        this.isLoading = false
        if (data.success && requestQuery === window.location.search) {
          this.generatedOn = this.$dayjs()
          this.devices = data.results
          this.devicesCount = data.count
          this.devicesTotals = data.totals
        }

        this.$refs['scroll-wrapper'].getScrollbarWidth()
      } catch (error) {
        this.isLoading = false

        this.error = this.$getErrorMessage(error)
      }
    },
    onDateRangeChanged(dates) {
      this.$router.push({
        ...this.$route,
        query: {
          ...this.$route.query,
          since: dates[0] ? this.$dayjs(dates[0]).format('YYYY-MM-DD') : null,
          until: dates[1] ? this.$dayjs(dates[1]).format('YYYY-MM-DD') : null,
        },
      })
    },
  },
}
</script>
